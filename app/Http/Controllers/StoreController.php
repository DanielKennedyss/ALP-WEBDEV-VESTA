<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\LoyaltyPointHistory;
use App\Models\ProductReview;
use App\Models\Voucher; // REVISI: Import Model Voucher untuk Logika Klaim Potongan Harga
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; // REVISI: Import Facade Mail untuk sistem notifikasi

class StoreController extends Controller
{
    /**
     * Tampilan Landing Page
     */
    public function show()
    {
        $products = Product::with(['category', 'variants'])->get();
        return view('home', compact('products'));
    }

    /**
     * Halaman Koleksi dengan Filter & Search (INTEGRASI FUZZY SEARCH)
     */
    public function collection(Request $request)
    {
        $query = Product::with(['category', 'variants']);

        // REVISI LOGIKA: Kondisi SQL "LIKE" dihapus dari backend agar pencarian ejaan samar (Fuzzy Search)
        // bisa dikerjakan langsung oleh Fuse.js di front-end secara real-time dan typo-tolerant.

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $selectedGender = $request->gender;
            if (strtolower($selectedGender) === 'male' || strtolower($selectedGender) === 'female') {
                $query->whereIn('gender', [$selectedGender, 'Unisex', 'unisex']);
            } else {
                $query->where('gender', $selectedGender);
            }
        }

        // Filter by size
        if ($request->filled('size')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('size_label', $request->size);
            });
        }

        // Sorting Logic
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_low': $query->orderBy('price', 'asc'); break;
            case 'price_high': $query->orderBy('price', 'desc'); break;
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            default: $query->orderBy('created_at', 'desc'); break;
        }

        $products = $query->get();
        $categories = Category::orderBy('name')->pluck('name');
        $genders = Product::select('gender')->distinct()->orderBy('gender')->pluck('gender');
        $sizes = ProductVariant::select('size_label')->distinct()->orderBy('size_label')->pluck('size_label');

        return view('store.collection', compact('products', 'categories', 'genders', 'sizes'));
    }

    /**
     * Tambah ke Keranjang (Session Based)
     */
    public function add_to_cart(Request $request, $product_id)
    {
        $product = Product::with('variants')->findOrFail($product_id);
        $quantity = $request->input('quantity', 1);
        $selectedSize = $request->input('size', null);

        if ($quantity < 1) return redirect()->back()->with('error', 'Quantity must be at least 1.');

        // Cek Stok Berdasarkan Ukuran
        if ($selectedSize) {
            $variant = $product->variants->where('size_label', $selectedSize)->first();
            if (!$variant) return redirect()->back()->with('error', 'Size not available.');
            $availableStock = $variant->stock;
        } else {
            $availableStock = $product->total_stock;
        }

        $cartKey = $selectedSize ? $product_id . '-' . $selectedSize : (string) $product_id;
        $cart = session()->get('cart', []);
        $existingQuantity = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $totalQuantity = $existingQuantity + $quantity;

        if ($totalQuantity > $availableStock) {
            return redirect()->back()->with('error', 'Requested quantity exceeds stock.');
        }

        $cart[$cartKey] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $totalQuantity,
            'size' => $selectedSize,
            'image_path' => $product->image_path,
        ];

        session()->put('cart', $cart);

        // Sync to database if user is logged in
        if (Auth::check()) {
            \App\Models\CartItem::saveSessionCartToDb(Auth::user());
        }

        return redirect()->back()->with('success', $product->name . ' added to cart.');
    }

    /**
     * Tampilan Halaman Cart (Keranjang)
     */
    public function view_cart(Request $request)
    {
        if ($request->has('cancel_buy_now')) {
            session()->forget('buy_now');
            session()->forget('applied_voucher');
            return redirect()->route('cart.view');
        }

        $isBuyNow = session()->has('buy_now');
        $cart = $isBuyNow ? ['buy_now' => session('buy_now')] : session('cart', []);
        
        $cartProducts = [];
        $subtotal = 0;
        
        foreach ($cart as $key => $item) {
            $cartProducts[$key] = Product::with('variants')->find($item['product_id']);
            $subtotal += ($item['price'] * $item['quantity']);
        }

        $user = Auth::user();
        
        return view('store.cart', compact('cart', 'cartProducts', 'subtotal', 'isBuyNow', 'user'));
    }

    /**
     * Review Checkout
     */
    public function view_checkout()
    {
        return redirect()->route('cart.view');
    }

    /**
     * Direct Checkout (Beli Sekarang)
     */
    public function direct_checkout(Request $request, $product_id)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Please login or sign up first to buy products.');
        }

        $product = Product::with('variants')->findOrFail($product_id);
        $quantity = (int) $request->input('quantity', 1);
        $selectedSize = $request->input('size', null);

        if ($selectedSize) {
            $variant = $product->variants->where('size_label', $selectedSize)->first();
            if (!$variant) return redirect()->back()->with('error', 'Size not available.');
            $availableStock = $variant->stock;
        } else {
            $availableStock = $product->total_stock;
        }

        if ($quantity < 1 || $quantity > $availableStock) {
            return redirect()->back()->with('error', 'Requested quantity is invalid or exceeds available stock.');
        }

        session()->forget('buy_now');
        session()->forget('applied_voucher');

        session()->put('buy_now', [
            'product_id' => $product->id,
            'name'        => $product->name,
            'price'      => $product->price,
            'quantity'   => $quantity,
            'size'       => $selectedSize,
            'image_path' => $product->image_path ?? null,
        ]);

        return redirect()->route('cart.view');
    }

    /**
     * Proses Pembuatan Transaksi Utama & Integrasi Midtrans Token
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->back()->with('error', 'Unauthenticated context.');

        $isBuyNow = session()->has('buy_now');
        $cart = $isBuyNow ? [session('buy_now')] : session('cart', []);

        if (empty($cart)) return redirect()->back()->with('error', 'Transaction session has expired or is empty.');
        
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        
        $discountVoucher = 0;
        $voucherId = null;
        if (session()->has('applied_voucher')) {
            $appliedVoucher = session('applied_voucher');
            $voucher = Voucher::find($appliedVoucher['id']);
            if ($voucher && !$voucher->isExpired() && !$voucher->isSoldOut()) {
                $discountVoucher = (int) $voucher->calculateDiscount($subtotal);
                $voucherId = $voucher->id;
            }
        }

        $priceAfterVoucher = max(0, $subtotal - $discountVoucher);

        $pointsToRedeem = (int) $request->input('points_to_redeem', 0);
        $discountPoints = 0;
        $pointsRedeemed = 0;

        if ($pointsToRedeem > 0) {
            $pointsRedeemed = min($pointsToRedeem, $user->loyalty_points);
            $discountPoints = $pointsRedeemed * 1000;
            
            if ($discountPoints >= $priceAfterVoucher) {
                $discountPoints = max(0, $priceAfterVoucher - 1000);
                $pointsRedeemed = ceil($discountPoints / 1000);
            }
        }

        $shippingAddress = $request->input('shipping_address', null);
        $shippingCourier = $request->input('shipping_courier', null);
        $shippingService = $request->input('shipping_service', null);
        $shippingCost = (int) $request->input('shipping_cost', 0);

        $totalPrice = max(1000, $priceAfterVoucher - $discountPoints + $shippingCost);

        DB::beginTransaction();
        try {
            if ($pointsRedeemed > 0) {
                $user = $user->fresh();
                if ($user->loyalty_points < $pointsRedeemed) {
                    throw new \Exception('Manipulated or insufficient loyalty points value.');
                }
                $user->decrement('loyalty_points', $pointsRedeemed);
            }

            if ($voucherId) {
                $voucher = Voucher::find($voucherId);
                if ($voucher) {
                    $voucher->increment('used_quota');
                }
            }

            $stockItems = collect($cart)->map(function($item) {
                return [
                    'product_id' => $item['product_id'],
                    'name'       => $item['name'],
                    'price'      => $item['price'],
                    'quantity'   => $item['quantity'],
                    'size'       => $item['size'] ?? null,
                    'image_path' => $item['image_path'] ?? null,
                ];
            })->values()->toArray();

            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $firstProductId = collect($cart)->first()['product_id'] ?? 0;

            $order = Transaction::create([
                'user_id'          => $user->id, 
                'invoice_number'   => $invoiceNumber,
                'product_id'       => $firstProductId,
                'quantity'         => collect($cart)->sum('quantity'),
                'subtotal'         => $subtotal,
                'discount_voucher' => $discountVoucher,
                'discount_points'  => $discountPoints,
                'points_redeemed'  => $pointsRedeemed,
                'shipping_address' => $shippingAddress,
                'shipping_courier' => $shippingCourier,
                'shipping_service' => $shippingService,
                'shipping_cost'    => $shippingCost,
                'total_price'      => $totalPrice,
                'cart_items'       => $stockItems,
                'customer_name'    => $user->name,
                'customer_email'   => $user->email,
                'status'           => 'pending',
                'payment_url'      => null,
                'paid_at'          => null,
            ]);

            if ($pointsRedeemed > 0) {
                LoyaltyPointHistory::create([
                    'user_id'        => $user->id,
                    'transaction_id' => $order->id,
                    'type'           => 'redeem',
                    'points'         => $pointsRedeemed,
                    'description'    => "Redeemed points for Order #" . $invoiceNumber,
                ]);
            }

            $this->initMidtrans();
            $item_details = [];
            foreach ($cart as $key => $item) {
                $item_details[] = [
                    'id'       => (string) $item['product_id'],
                    'price'    => (int) $item['price'],
                    'quantity' => (int) $item['quantity'],
                    'name'     => substr($item['name'], 0, 50),
                ];
            }

            if ($discountVoucher > 0) {
                $item_details[] = [
                    'id'       => 'DISC-VOUCHER',
                    'price'    => -(int) $discountVoucher,
                    'quantity' => 1,
                    'name'     => 'Promo Voucher Discount',
                ];
            }

            if ($discountPoints > 0) {
                $item_details[] = [
                    'id'       => 'DISC-POINTS',
                    'price'    => -(int) $discountPoints,
                    'quantity' => 1,
                    'name'     => 'Privilege Points Discount',
                ];
            }

            if ($shippingCost > 0) {
                $item_details[] = [
                    'id'       => 'SHIPPING-COST',
                    'price'    => (int) $shippingCost,
                    'quantity' => 1,
                    'name'     => 'Shipping (' . strtoupper($shippingCourier) . ' - ' . $shippingService . ')',
                ];
            }

            $params = [
                'transaction_details' => ['order_id' => $order->invoice_number, 'gross_amount' => (int) $totalPrice],
                'item_details'        => $item_details,
                'customer_details'    => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                ],
                'callbacks' => ['finish' => route('payment_return', $order->id)],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->update(['payment_url' => $snapToken]);
            
            DB::commit();

            $cartItems = collect($stockItems)->map(function($item) {
                $item['subtotal'] = $item['price'] * $item['quantity'];
                return $item;
            })->toArray();

            if ($isBuyNow) {
                session()->forget('buy_now');
            } else {
                session()->forget('cart');
                if (Auth::check()) {
                    \App\Models\CartItem::where('user_id', Auth::id())->delete();
                }
            }

            // REVISI EMAIL: Kirim email tagihan pembayaran 1x24 jam secara otomatis ke Gmail Pembeli
            try {
                Mail::to($order->customer_email)->send(new \App\Mail\OrderPlacedMail($order));
            } catch (\Exception $mailEx) {
                Log::error('Mail Placement Warning: ' . $mailEx->getMessage());
            }

            return view('store.payment', compact('snapToken', 'order', 'cartItems'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Unified Checkout Execution Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Checkout workflow failed: ' . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi Status Pembayaran dengan Midtrans
     */
    public function payment_status($order_id)
    {
        $order = Transaction::findOrFail($order_id);
        if ($order->status === 'success') {
            return redirect()->route('profile')->with('success', 'Payment Successful! Thank you.');
        }

        $this->initMidtrans();
        try {
            $statusResponse = \Midtrans\Transaction::status($order->invoice_number);
            $transactionStatus = $statusResponse->transaction_status;
            
            $oldStatus = $order->status;
            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $order->status = 'success';
                if (!$order->paid_at) $order->paid_at = now();
                $order->save();

                // REVISI EMAIL: Pengaman jika webhook asinkronus terhambat, tembak invoice dari alur return status
                if ($oldStatus !== 'success') {
                    try {
                        Mail::to($order->customer_email)->send(new \App\Mail\PaymentSuccessMail($order));
                    } catch (\Exception $mailEx) {
                        Log::error('Mail Status Return Warning: ' . $mailEx->getMessage());
                    }
                }
            } elseif ($transactionStatus == 'pending') {
                $order->status = 'pending';
                $order->save();
            } else {
                $order->status = 'cancelled';
                $this->handleFailedTransactionRefund($order, $oldStatus, $order->status);
                $order->save();
            }
            
        } catch (\Exception $e) {
            Log::error('Payment Status Error: ' . $e->getMessage());
        }

        if ($order->status == 'success') {
            return redirect()->route('profile')->with('success', 'Payment Successful! Thank you.');
        }
        return redirect()->route('profile')->with('error', 'Payment status: ' . $order->status);
    }

    /**
     * Callback dari Midtrans Snap JS (Webhook Backend)
     */
    public function payment_callback(Request $request, $order_id)
    {
        $order = Transaction::findOrFail($order_id);
        $callbackStatus = $request->input('status', 'pending');
        $oldStatus = $order->status;

        if ($callbackStatus === 'success' && $oldStatus !== 'success') {
            $order->status = 'success';
            $order->paid_at = now();
            $order->save();

            // REVISI EMAIL: Kirim digital receipt invoice lunas otomatis secara real-time via background queue
            try {
                Mail::to($order->customer_email)->send(new \App\Mail\PaymentSuccessMail($order));
            } catch (\Exception $mailEx) {
                Log::error('Mail Success Notification Callback Warning: ' . $mailEx->getMessage());
            }
        } elseif ($callbackStatus === 'failed' && $oldStatus === 'pending') {
            $order->status = 'failed';
            $this->handleFailedTransactionRefund($order, $oldStatus, $order->status);
            $order->save();
        }

        return response()->json(['status' => $order->status]);
    }

    /**
     * Helper Method untuk Refund Poin
     */
    private function handleFailedTransactionRefund(Transaction $order, $oldStatus, $newStatus)
    {
        if (in_array($newStatus, ['failed', 'cancelled', 'expired']) && !in_array($oldStatus, ['failed', 'cancelled', 'expired'])) {
            $user = $order->user;
            if ($user && $order->points_redeemed > 0) {
                $user->increment('loyalty_points', $order->points_redeemed);
                
                LoyaltyPointHistory::create([
                    'user_id'        => $user->id,
                    'transaction_id' => $order->id,
                    'type'           => 'refund',
                    'points'         => $order->points_redeemed,
                    'description'    => "Points refunded from failed Order #" . $order->invoice_number,
                ]);
            }
        }
    }

    /**
     * Retry payment for pending orders
     */
    public function payment_retry($order_id)
    {
        $order = Transaction::findOrFail($order_id);
        if ($order->status === 'success') {
            return redirect()->route('profile')->with('success', 'This order has already been paid.');
        }

        if (!$order->payment_url) {
            return redirect()->route('profile')->with('error', 'No payment token available for this order.');
        }

        $snapToken = $order->payment_url;
        $cartItems = collect($order->cart_items ?? [])->map(function($item) {
            return [
                'name'        => $item['name'] ?? 'Unknown',
                'price'      => $item['price'] ?? 0,
                'quantity'   => $item['quantity'] ?? 1,
                'size'       => $item['size'] ?? null,
                'image_path' => $item['image_path'] ?? null,
                'subtotal'   => ($item['price'] ?? 0) * ($item['quantity'] ?? 1),
            ];
        })->toArray();

        return view('store.payment', compact('snapToken', 'order', 'cartItems'));
    }

    /**
     * Konfigurasi Internal Midtrans
     */
    private function initMidtrans()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
        \Midtrans\Config::$curlOptions = [
            CURLOPT_HTTPHEADER => [], 
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ];
    }

    public function payment_return($order_id)
    {
        return request()->has('order_id') ? $this->payment_status($order_id) : redirect()->route('payment_status', $order_id);
    }

    public function update_cart(Request $request, $cart_key)
    {
        if ($cart_key === 'buy_now') {
            $item = session()->get('buy_now');
            if ($item) {
                $newQuantity = (int) $request->input('quantity', 1);
                if ($newQuantity <= 0) {
                    session()->forget('buy_now');
                    session()->forget('applied_voucher');
                    return redirect()->back()->with('success', 'Item removed from cart.');
                }

                $product = Product::with('variants')->find($item['product_id']);
                if ($product) {
                    $size = $item['size'] ?? null;
                    if ($size) {
                        $variant = $product->variants->where('size_label', $size)->first();
                        $maxStock = $variant ? $variant->stock : 0;
                    } else {
                        $maxStock = $product->total_stock;
                    }

                    if ($newQuantity > $maxStock) {
                        return redirect()->back()->with('error', 'Quantity exceeds available stock.');
                    }
                }

                $item['quantity'] = $newQuantity;
                session()->put('buy_now', $item);
            }
            return redirect()->back();
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$cart_key])) {
            $newQuantity = (int) $request->input('quantity', 1);
            if ($newQuantity <= 0) {
                unset($cart[$cart_key]);
                session()->put('cart', $cart);
                if (Auth::check()) {
                    \App\Models\CartItem::saveSessionCartToDb(Auth::user());
                }
                return redirect()->back()->with('success', 'Item removed from cart.');
            }

            $product = Product::with('variants')->find($cart[$cart_key]['product_id']);
            if ($product) {
                $size = $cart[$cart_key]['size'] ?? null;
                if ($size) {
                    $variant = $product->variants->where('size_label', $size)->first();
                    $maxStock = $variant ? $variant->stock : 0;
                } else {
                    $maxStock = $product->total_stock;
                }

                if ($newQuantity > $maxStock) {
                    return redirect()->back()->with('error', 'Quantity exceeds available stock.');
                }
            }

            $cart[$cart_key]['quantity'] = $newQuantity;
            session()->put('cart', $cart);
            if (Auth::check()) {
                \App\Models\CartItem::saveSessionCartToDb(Auth::user());
            }
        }
        return redirect()->back();
    }

    public function update_cart_size(Request $request, $cart_key)
    {
        if ($cart_key === 'buy_now') {
            $item = session()->get('buy_now');
            if ($item) {
                $newSize = $request->input('new_size');
                $item['size'] = $newSize;
                session()->put('buy_now', $item);
            }
            return redirect()->back();
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$cart_key])) {
            $item = $cart[$cart_key];
            $newSize = $request->input('new_size');
            $newKey = $item['product_id'] . '-' . $newSize;

            if (isset($cart[$newKey]) && $newKey !== $cart_key) {
                $cart[$newKey]['quantity'] += $item['quantity'];
                unset($cart[$cart_key]);
            } else {
                $item['size'] = $newSize;
                unset($cart[$cart_key]);
                $cart[$newKey] = $item;
            }
            session()->put('cart', $cart);
            if (Auth::check()) {
                \App\Models\CartItem::saveSessionCartToDb(Auth::user());
            }
        }
        return redirect()->back();
    }

    public function remove_from_cart($cart_key)
    {
        if ($cart_key === 'buy_now') {
            session()->forget('buy_now');
            session()->forget('applied_voucher');
            return redirect()->back();
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$cart_key])) {
            unset($cart[$cart_key]);
            session()->put('cart', $cart);
            if (Auth::check()) {
                \App\Models\CartItem::where('user_id', Auth::id())->delete();
            }
        }
        return redirect()->back();
    }

    /**
     * Get Wishlist Items
     */
    public function get_wishlist()
    {
        if (!Auth::check()) {
            return response()->json([]);
        }
        $user = Auth::user();
        $products = Product::join('wishlists', 'products.id', '=', 'wishlists.product_id')
            ->where('wishlists.user_id', $user->id)
            ->select('products.*')
            ->with(['category'])
            ->get();
        return response()->json($products);
    }

    /**
     * Toggle Wishlist Item
     */
    public function toggle_wishlist($product_id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $user = Auth::user();
        $product = Product::findOrFail($product_id);
        $wishlist = DB::table('wishlists')
            ->where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->first();

        if ($wishlist) {
            DB::table('wishlists')
                ->where('user_id', $user->id)
                ->where('product_id', $product_id)
                ->delete();
            $status = 'removed';
        } else {
            DB::table('wishlists')->insert([
                'user_id'    => $user->id,
                'product_id' => $product_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $status = 'added';
        }

        $count = DB::table('wishlists')->where('user_id', $user->id)->count();
        return response()->json([
            'status' => $status,
            'count'  => $count
        ]);
    }

    /**
     * Sync Wishlist Items dari LocalStorage saat Login
     */
    public function sync_wishlist(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $user = Auth::user();
        $productIds = $request->input('product_ids', []);

        foreach ($productIds as $id) {
            $exists = Product::find($id);
            if ($exists) {
                $alreadyInWishlist = DB::table('wishlists')
                    ->where('user_id', $user->id)
                    ->where('product_id', $id)
                    ->exists();
                if (!$alreadyInWishlist) {
                    DB::table('wishlists')->insert([
                        'user_id'    => $user->id,
                        'product_id' => $id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
        $count = DB::table('wishlists')->where('user_id', $user->id)->count();
        return response()->json([
            'status' => 'synced',
            'count'  => $count
        ]);
    }

    /**
     * Klaim Potongan Voucher Belanja di Halaman Checkout VESTA
     */
    public function apply_voucher(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login to use voucher.'], 401);
        }

        $request->validate([
            'voucher_code' => 'required|string',
        ]);

        $voucherCode = strtoupper($request->input('voucher_code'));
        $voucher = Voucher::where('code', $voucherCode)->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher code is invalid.']);
        }

        if ($voucher->isExpired()) {
            return response()->json(['success' => false, 'message' => 'This voucher has expired.']);
        }

        if ($voucher->isSoldOut()) {
            return response()->json(['success' => false, 'message' => 'Voucher quota has been fully redeemed.']);
        }

        $isBuyNow = session()->has('buy_now');
        $cartItems = $isBuyNow ? [session('buy_now')] : session('cart', []);

        if (empty($cartItems)) {
            return response()->json(['success' => false, 'message' => 'Your checkout items are empty.']);
        }

        $subtotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        $discountAmount = (int) $voucher->calculateDiscount($subtotal);
        $discountAmount = min($discountAmount, $subtotal - 1000);
        
        if ($discountAmount < 0) {
            $discountAmount = 0;
        }

        session()->put('applied_voucher', [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'discount' => $discountAmount
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Voucher "' . $voucher->code . '" successfully applied!',
            'discount' => $discountAmount,
            'formatted_discount' => 'Rp ' . number_format($discountAmount, 0, ',', '.')
        ]);
    }

    /**
     * Cancel a pending order
     */
    public function cancelOrder(Transaction $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be cancelled.');
        }

        $oldStatus = $order->status;
        $order->status = 'cancelled';

        $this->handleFailedTransactionRefund($order, $oldStatus, 'cancelled');
        $order->save();

        return redirect()->back()->with('success', 'Order #' . $order->invoice_number . ' has been cancelled successfully.');
    }

    /**
     * View tracking status of an order
     */
    public function trackOrder(Transaction $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('store.track', compact('order'));
    }

    /**
     * Mark shipped order as received
     */
    public function markAsReceived(Transaction $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'shipped') {
            return redirect()->back()->with('error', 'Only shipped orders can be marked as received.');
        }

        $order->status = 'delivered';
        $order->save();

        return redirect()->back()->with('success', 'Order #' . $order->invoice_number . ' has been marked as received.');
    }

    /**
     * Submit reviews for products in a transaction
     */
    public function submitReview(Request $request, Transaction $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($order->status, ['delivered', 'completed'])) {
            return redirect()->back()->with('error', 'You can only review items on delivered or completed orders.');
        }

        $request->validate([
            'reviews' => 'required|array',
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.comment' => 'nullable|string|max:1000',
        ]);

        foreach ($request->input('reviews') as $reviewData) {
            ProductReview::create([
                'user_id' => Auth::id(),
                'product_id' => $reviewData['product_id'],
                'transaction_id' => $order->id,
                'rating' => $reviewData['rating'],
                'comment' => $reviewData['comment'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Thank you for your review!');
    }
}