<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\LoyaltyPointHistory;
use App\Models\Voucher; // REVISI: Import Model Voucher untuk Logika Klaim Potongan Harga
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
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Please login or sign up first to add items to your cart.');
        }

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

    /**
     * Tampilan Halaman Cart (Keranjang)
     */
    public function view_cart()
    {
        // Bersihkan data temporary buy_now saat user kembali membuka halaman keranjang belanja utama
        session()->forget('buy_now');

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
     * Halaman Review Checkout Terpadu (Langkah Penengah Sebelum Pembayaran)
     */
    public function view_checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to proceed to checkout.');
        }

        // Tentukan context data: Apakah dari flow "Buy Now" or "Cart"
        $isBuyNow = session()->has('buy_now');
        $cartItems = $isBuyNow ? [session('buy_now')] : session('cart', []);

        if (empty($cartItems)) {
            return redirect()->route('collection')->with('error', 'Your checkout instance is empty.');
        }

        $subtotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        $user = Auth::user();

        return view('store.checkout', compact('cartItems', 'subtotal', 'user', 'isBuyNow'));
    }

    /**
     * Direct Checkout (Beli Sekarang) - Dialihkan Sebagai Handler State Session Review
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

        // Simpan data ke session temporary 'buy_now'
        session()->forget('buy_now');
        session()->put('buy_now', [
            'product_id' => $product->id,
            'name'        => $product->name,
            'price'      => $product->price,
            'quantity'   => $quantity,
            'size'       => $selectedSize,
            'image_path' => $product->image_path ?? null,
        ]);

        // Arahkan langsung ke halaman review checkout
        return redirect()->route('checkout.view');
    }

    /**
     * Proses Pembuatan Transaksi Utama & Integrasi Midtrans Token
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->back()->with('error', 'Unauthenticated context.');

        // Ambil session berdasarkan alur pembeliannya
        $isBuyNow = session()->has('buy_now');
        $cart = $isBuyNow ? [session('buy_now')] : session('cart', []);

        if (empty($cart)) return redirect()->back()->with('error', 'Transaction session has expired or is empty.');
        
        // 1. Kalkulasi Harga Asli (Subtotal)
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        
        // 2. Kalkulasi Potongan Poin Loyalty
        $pointsToRedeem = (int) $request->input('points_to_redeem', 0);
        $discountPoints = 0;
        $pointsRedeemed = 0;

        if ($pointsToRedeem > 0) {
            $pointsRedeemed = min($pointsToRedeem, $user->loyalty_points);
            $discountPoints = $pointsRedeemed * 1000; // Skema: 1 Poin = Rp 1.000
            
            // Pengaman: Jika diskon poin melebihi subtotal, sisakan nominal aman Rp 1.000 untuk tagihan Midtrans
            if ($discountPoints >= $subtotal) {
                $discountPoints = max(0, $subtotal - 1000);
                $pointsRedeemed = ceil($discountPoints / 1000);
            }
        }

        // 3. Harga Final Setelah Potongan
        $totalPrice = max(1000, $subtotal - $discountPoints);

        DB::beginTransaction();
        try {
            // 4. Potong poin user di database (Menggunakan Fresh Lock)
            if ($pointsRedeemed > 0) {
                $user = $user->fresh();
                if ($user->loyalty_points < $pointsRedeemed) {
                    throw new \Exception('Manipulated or insufficient loyalty points value.');
                }
                $user->decrement('loyalty_points', $pointsRedeemed);
            }

            // Membangun array data produk untuk JSON tracking database
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

            // 5. Buat Record Transaksi di Database Lokal VESTA
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $firstProductId = collect($cart)->first()['product_id'] ?? 0;

            $order = Transaction::create([
                'user_id'          => $user->id, 
                'invoice_number'   => $invoiceNumber,
                'product_id'       => $firstProductId,
                'quantity'         => collect($cart)->sum('quantity'),
                'subtotal'         => $subtotal,
                'discount_points'  => $discountPoints,
                'points_redeemed'  => $pointsRedeemed,
                'total_price'      => $totalPrice,
                'cart_items'       => $stockItems,
                'customer_name'    => $user->name,
                'customer_email'   => $user->email,
                'status'           => 'pending',
                'payment_url'      => null,
                'paid_at'          => null,
            ]);

            // 6. Catat Mutasi Poin ke Ledger History Auditing
            if ($pointsRedeemed > 0) {
                LoyaltyPointHistory::create([
                    'user_id'        => $user->id,
                    'transaction_id' => $order->id,
                    'type'           => 'redeem',
                    'points'         => $pointsRedeemed,
                    'description'    => "Redeemed points for Order #" . $invoiceNumber,
                ]);
            }

            // 7. Midtrans Integration Assembly Payload
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

            // Menambahkan item minus di Midtrans Invoice sebagai visual pemotong diskon poin
            if ($discountPoints > 0) {
                $item_details[] = [
                    'id'       => 'DISC-POINTS',
                    'price'    => -(int) $discountPoints,
                    'quantity' => 1,
                    'name'     => 'Privilege Points Discount',
                ];
            }

            $params = [
                'transaction_details' => ['order_id' => $order->invoice_number, 'gross_amount' => $totalPrice],
                'item_details'        => $item_details,
                'customer_details'    => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                ],
                'callbacks' => ['finish' => route('payment_return', $order->id)],
            ];

            // Request Token dari Gateway Midtrans Snap API
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->update(['payment_url' => $snapToken]);
            
            DB::commit();

            // Format mapping arrays untuk view layout template pembayaran
            $cartItems = collect($stockItems)->map(function($item) {
                $item['subtotal'] = $item['price'] * $item['quantity'];
                return $item;
            })->toArray();

            // Bersihkan session data berdasarkan jenis flow belanja yang aktif
            if ($isBuyNow) {
                session()->forget('buy_now');
            } else {
                session()->forget('cart');
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

    /**
     * Get Wishlist Items (JSON response)
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
     * Toggle Wishlist Item (JSON response)
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
     * Sync Wishlist Items from LocalStorage on Login (JSON response)
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
     * REVISI: Logika AJAX Klaim / Redeem Potongan Voucher Belanja di Halaman Checkout VESTA
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
        
        // REVISI TOTAL: Menggunakan pencarian dinamis yang fleksibel terhadap nama kolom tabel database 'vouchers' kamu
        $voucher = Voucher::where('code', $voucherCode)
            ->where(function($query) {
                // Mencoba mencocokkan kolom status kustom jika ada di phpMyAdmin / database lokalmu
                if (DB::getSchemaBuilder()->hasColumn('vouchers', 'status')) {
                    $query->where('status', 'active');
                } elseif (DB::getSchemaBuilder()->hasColumn('vouchers', 'is_active')) {
                    $query->where('is_active', true);
                }
            })
            ->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher code is invalid or has expired.']);
        }

        // 2. Cek Masa Berlaku Tanggal Voucher (Mencegah kecurangan waktu di lokal)
        $now = now();
        if (($voucher->start_date && $now->lt($voucher->start_date)) || ($voucher->end_date && $now->gt($voucher->end_date))) {
            return response()->json(['success' => false, 'message' => 'This voucher is not currently active.']);
        }

        // 3. Cek Sisa Kuota Pemakaian Kupon Toko
        if (!is_null($voucher->usage_limit) && $voucher->current_usage >= $voucher->usage_limit) {
            return response()->json(['success' => false, 'message' => 'Voucher quota has been fully redeemed.']);
        }

        // 4. Hitung Subtotal Keranjang Saat Ini untuk Validasi Minimum Pengecekan Belanja
        $isBuyNow = session()->has('buy_now');
        $cartItems = $isBuyNow ? [session('buy_now')] : session('cart', []);

        if (empty($cartItems)) {
            return response()->json(['success' => false, 'message' => 'Your checkout items are empty.']);
        }

        $subtotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);

        // 5. Cek Apakah Keranjang Memenuhi Syarat Minimum Pembelian Voucher
        if ($subtotal < $voucher->min_spend) {
            return response()->json([
                'success' => false, 
                'message' => 'Minimum spend of Rp ' . number_format($voucher->min_spend, 0, ',', '.') . ' is required for this voucher.'
            ]);
        }

        // 6. Hitung Nominal Potongan Harga Berdasarkan Tipe Voucher (Fixed Amount atau Percentage)
        $discountAmount = 0;
        if ($voucher->type === 'fixed') {
            $discountAmount = $voucher->reward_amount;
        } elseif ($voucher->type === 'percentage') {
            $discountAmount = ($voucher->reward_amount / 100) * $subtotal;
            
            // Batasi dengan nilai maksimum diskon jika field max_discount tersedia di tabelmu
            if (isset($voucher->max_discount) && $voucher->max_discount > 0) {
                $discountAmount = min($discountAmount, $voucher->max_discount);
            }
        }

        // Pengaman nilai diskon tidak boleh melebihi subtotal agar Midtrans tidak menolak nominal minus
        $discountAmount = min($discountAmount, $subtotal - 1000);

        // 7. Simpan state voucher yang berhasil divalidasi ke session agar bisa ditarik saat eksekusi tombol checkout()
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
}