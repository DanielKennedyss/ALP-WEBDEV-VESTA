<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\LoyaltyPointHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
     * Halaman Koleksi dengan Filter & Search
     */
    public function collection(Request $request)
    {
        $query = Product::with(['category', 'variants']);

        // Search by product name, description, or SKU
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

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
        return redirect()->back()->with('success', $product->name . ' added to cart.');
    }

    public function view_cart()
    {
        $cart = session()->get('cart', []);
        $cartProducts = [];
        $subtotal = 0;
        
        foreach ($cart as $key => $item) {
            $cartProducts[$key] = Product::with('variants')->find($item['product_id']);
            $subtotal += ($item['price'] * $item['quantity']);
        }
        
        return view('store.cart', compact('cart', 'cartProducts', 'subtotal'));
    }

    /**
     * Proses Checkout Utama (Terintegrasi dengan Poin VESTA)
     */
    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) return redirect()->back()->with('error', 'Cart is empty!');

        $user = Auth::user();
        
        // 1. Kalkulasi Harga Asli (Subtotal)
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        
        // 2. Kalkulasi Potongan Poin
        $pointsToRedeem = (int) $request->input('points_to_redeem', 0);
        $discountPoints = 0;
        $pointsRedeemed = 0;

        if ($user && $pointsToRedeem > 0) {
            $pointsRedeemed = min($pointsToRedeem, $user->loyalty_points);
            $discountPoints = $pointsRedeemed * 1000; // 1 Poin = Rp 1.000

            // Cegah minus jika diskon poin melebihi subtotal
            if ($discountPoints > $subtotal) {
                $discountPoints = $subtotal;
                $pointsRedeemed = $subtotal / 1000;
            }
        }

        // 3. Harga Final
        $totalPrice = max(0, $subtotal - $discountPoints);

        DB::beginTransaction();
        try {
            // 4. Potong poin user (Lock)
            if ($pointsRedeemed > 0) {
                $user->decrement('loyalty_points', $pointsRedeemed);
            }

            // Build cart_items for stock tracking
            $stockItems = collect($cart)->map(function($item) {
                return [
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? null,
                    'image_path' => $item['image_path'] ?? null,
                ];
            })->values()->toArray();

            // 5. Buat Transaksi Baru
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $order = Transaction::create([
                'user_id'          => Auth::id(), 
                'invoice_number'   => $invoiceNumber,
                'product_id'       => $cart[key($cart)]['product_id'] ?? 0,
                'quantity'         => collect($cart)->sum('quantity'),
                'subtotal'         => $subtotal,
                'discount_points'  => $discountPoints,
                'points_redeemed'  => $pointsRedeemed,
                'total_price'      => $totalPrice,
                'cart_items'       => $stockItems,
                'customer_name'    => Auth::check() ? $user->name : 'Guest',
                'customer_email'   => Auth::check() ? $user->email : null,
                'status'           => 'pending',
                'payment_url'      => null,
                'paid_at'          => null,
            ]);

            // 6. Catat Mutasi Poin ke Ledger History
            if ($pointsRedeemed > 0) {
                LoyaltyPointHistory::create([
                    'user_id'        => $user->id,
                    'transaction_id' => $order->id,
                    'type'           => 'redeem',
                    'points'         => $pointsRedeemed,
                    'description'    => "Redeemed points for Order #" . $invoiceNumber,
                ]);
            }

            // 7. Midtrans Integration (Gunakan Helper)
            $this->initMidtrans();

            $item_details = [];
            foreach ($cart as $product_id => $item) {
                $item_details[] = [
                    'id' => $product_id,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name' => substr($item['name'], 0, 50),
                ];
            }

            // Tambahkan minus item untuk potongan diskon poin agar ditagih Midtrans secara akurat
            if ($discountPoints > 0) {
                $item_details[] = [
                    'id'       => 'DISC-POINTS',
                    'price'    => -$discountPoints,
                    'quantity' => 1,
                    'name'     => 'Privilege Points Discount',
                ];
            }

            $params = [
                'transaction_details' => ['order_id' => $order->invoice_number, 'gross_amount' => $totalPrice],
                'item_details' => $item_details,
                'customer_details' => [
                    'first_name' => Auth::check() ? Auth::user()->name : 'Guest',
                    'email' => Auth::check() ? Auth::user()->email : 'guest@example.com',
                ],
                'callbacks' => ['finish' => route('payment_return', $order->id)],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->update(['payment_url' => $snapToken]);

            DB::commit();

            // Format cart untuk halaman pembayaran
            $cartItems = collect($stockItems)->map(function($item) {
                $item['subtotal'] = $item['price'] * $item['quantity'];
                return $item;
            })->toArray();

            session()->forget('cart');

            return view('store.payment', compact('snapToken', 'order', 'cartItems'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }

    /**
     * Direct Checkout (Beli Sekarang)
     */
    public function direct_checkout(Request $request, $product_id)
    {
        $product = Product::with('variants')->findOrFail($product_id);
        $quantity = $request->input('quantity', 1);
        $user = Auth::user();

        if ($quantity < 1 || $quantity > $product->total_stock) {
            return redirect()->back()->with('error', 'Invalid quantity.');
        }

        // 1. Kalkulasi Harga Asli (Subtotal)
        $subtotal = $product->price * $quantity;

        // 2. Kalkulasi Potongan Poin
        $pointsToRedeem = (int) $request->input('points_to_redeem', 0);
        $discountPoints = 0;
        $pointsRedeemed = 0;

        if ($user && $pointsToRedeem > 0) {
            $pointsRedeemed = min($pointsToRedeem, $user->loyalty_points);
            $discountPoints = $pointsRedeemed * 1000;

            if ($discountPoints > $subtotal) {
                $discountPoints = $subtotal;
                $pointsRedeemed = $subtotal / 1000;
            }
        }

        // 3. Harga Final
        $totalPrice = max(0, $subtotal - $discountPoints);

        DB::beginTransaction();
        try {
            // 4. Potong poin user
            if ($pointsRedeemed > 0) {
                $user->decrement('loyalty_points', $pointsRedeemed);
            }

            $customerName = Auth::check() ? Auth::user()->name : 'Guest';
            $customerEmail = Auth::check() ? Auth::user()->email : null;
            $selectedSize = $request->input('size', null);

            $stockItems = [[
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'size' => $selectedSize,
                'image_path' => $product->image_path ?? null,
            ]];

            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $order = Transaction::create([
                'user_id'          => Auth::id(),
                'invoice_number'   => $invoiceNumber,
                'product_id'       => $product->id,
                'quantity'         => $quantity,
                'subtotal'         => $subtotal,
                'discount_points'  => $discountPoints,
                'points_redeemed'  => $pointsRedeemed,
                'total_price'      => $totalPrice, 
                'cart_items'       => $stockItems,
                'customer_name'    => $customerName,
                'customer_email'   => $customerEmail,
                'status'           => 'pending',
                'payment_url'      => null,
                'paid_at'          => null,
            ]);

            // 5. Catat Mutasi Poin ke Ledger History
            if ($pointsRedeemed > 0) {
                LoyaltyPointHistory::create([
                    'user_id'        => $user->id,
                    'transaction_id' => $order->id,
                    'type'           => 'redeem',
                    'points'         => $pointsRedeemed,
                    'description'    => "Redeemed points for Direct Checkout #" . $invoiceNumber,
                ]);
            }

            $this->initMidtrans();

            $item_details = [[
                'id' => $product->id,
                'price' => $product->price,
                'quantity' => $quantity,
                'name' => substr($product->name, 0, 50),
            ]];

            if ($discountPoints > 0) {
                $item_details[] = [
                    'id'       => 'DISC-POINTS',
                    'price'    => -$discountPoints,
                    'quantity' => 1,
                    'name'     => 'Privilege Points Discount',
                ];
            }

            $params = [
                'transaction_details' => ['order_id' => $order->invoice_number, 'gross_amount' => $totalPrice],
                'item_details' => $item_details,
                'customer_details' => [
                    'first_name' => $customerName,
                    'email' => $customerEmail ?? 'guest@example.com',
                ],
                'callbacks' => ['finish' => route('payment_return', $order->id)],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->update(['payment_url' => $snapToken]);

            DB::commit();
            
            $cartItems = $stockItems;
            $cartItems[0]['subtotal'] = $totalPrice;

            return view('store.payment', compact('snapToken', 'order', 'cartItems'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Direct Checkout Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Checkout failed: ' . $e->getMessage());
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
            } elseif ($transactionStatus == 'pending') {
                $order->status = 'pending';
            } else {
                $order->status = 'cancelled';
            }
            
            // GARDA PENGAMAN REFUND POIN JIKA MIDTRANS GAGAL
            $this->handleFailedTransactionRefund($order, $oldStatus, $order->status);

            $order->save();
        } catch (\Exception $e) {
            Log::error('Payment Status Error: ' . $e->getMessage());
        }

        if ($order->status == 'success') {
            return redirect()->route('profile')->with('success', 'Payment Successful! Thank you.');
        }

        return redirect()->route('profile')->with('error', 'Payment status: ' . $order->status);
    }

    /**
     * Callback from Midtrans Snap JS
     */
    public function payment_callback(Request $request, $order_id)
    {
        $order = Transaction::findOrFail($order_id);
        $callbackStatus = $request->input('status', 'pending');
        $oldStatus = $order->status;

        if ($callbackStatus === 'success' && $oldStatus !== 'success') {
            $order->status = 'success';
            $order->paid_at = now();
        } elseif ($callbackStatus === 'failed' && $oldStatus === 'pending') {
            $order->status = 'failed';
            // GARDA PENGAMAN REFUND POIN
            $this->handleFailedTransactionRefund($order, $oldStatus, $order->status);
        }

        $order->save();

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
                // Kembalikan poin
                $user->increment('loyalty_points', $order->points_redeemed);
                
                // Log History
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
                'name' => $item['name'] ?? 'Unknown',
                'price' => $item['price'] ?? 0,
                'quantity' => $item['quantity'] ?? 1,
                'size' => $item['size'] ?? null,
                'image_path' => $item['image_path'] ?? null,
                'subtotal' => ($item['price'] ?? 0) * ($item['quantity'] ?? 1),
            ];
        })->toArray();

        return view('store.payment', compact('snapToken', 'order', 'cartItems'));
    }

    /**
     * Konfigurasi Internal Midtrans (DRY Principle)
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

    /**
     * Update Cart Item Quantity
     */
    public function update_cart(Request $request, $cart_key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cart_key])) {
            $newQuantity = (int) $request->input('quantity', 1);

            if ($newQuantity <= 0) {
                unset($cart[$cart_key]);
                session()->put('cart', $cart);
                return redirect()->back()->with('success', 'Item removed from cart.');
            }

            // Check stock limit
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
        }

        return redirect()->back();
    }

    /**
     * Update Cart Item Size
     */
    public function update_cart_size(Request $request, $cart_key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cart_key])) {
            $item = $cart[$cart_key];
            $newSize = $request->input('new_size');
            $newKey = $item['product_id'] . '-' . $newSize;

            // If the new size key already exists, merge quantities
            if (isset($cart[$newKey]) && $newKey !== $cart_key) {
                $cart[$newKey]['quantity'] += $item['quantity'];
                unset($cart[$cart_key]);
            } else {
                $item['size'] = $newSize;
                unset($cart[$cart_key]);
                $cart[$newKey] = $item;
            }

            session()->put('cart', $cart);
        }

        return redirect()->back();
    }

    public function remove_from_cart($cart_key)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$cart_key])) {
            unset($cart[$cart_key]);
            session()->put('cart', $cart);
        }
        return redirect()->back();
    }
}