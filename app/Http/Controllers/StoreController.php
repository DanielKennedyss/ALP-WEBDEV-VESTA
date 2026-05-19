<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Transaction;
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

        // Filter by gender (Male/Female also includes Unisex products)
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

        // Data untuk dropdown filter
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

        if ($quantity < 1) {
            return redirect()->back()->with('error', 'Quantity must be at least 1.');
        }

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
        foreach ($cart as $key => $item) {
            $cartProducts[$key] = Product::with('variants')->find($item['product_id']);
        }
        return view('store.cart', compact('cart', 'cartProducts'));
    }

    /**
     * Proses Checkout Utama (Sesuai Revisi Best Practice)
     */
    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) return redirect()->back()->with('error', 'Cart is empty!');

        DB::beginTransaction();
        try {
            $totalPrice = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

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

            $order = Transaction::create([
                'user_id'        => Auth::id(), 
                'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'product_id' => $cart[key($cart)]['product_id'] ?? 0,
                'quantity' => collect($cart)->sum('quantity'),
                'cart_items' => $stockItems,
                'total_price' => $totalPrice,
                'customer_name' => Auth::check() ? Auth::user()->name : 'Guest',
                'customer_email' => Auth::check() ? Auth::user()->email : null,
                'status' => 'pending',
                'payment_url' => null,
                'paid_at' => null,
            ]);

            // Midtrans payment integration
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // Disable SSL verification for localhost/dev environments
            // Include CURLOPT_HTTPHEADER => [] to prevent "Undefined array key 10023" warning
            \Midtrans\Config::$curlOptions = [
                CURLOPT_HTTPHEADER => [],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
            ];

            $item_details = [];
            foreach ($cart as $product_id => $item) {
                $item_details[] = [
                    'id' => $product_id,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name' => substr($item['name'], 0, 50),
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

            // Save cart items for display on payment page before clearing
            $cartItems = collect($cart)->map(function($item) {
                return [
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? null,
                    'image_path' => $item['image_path'] ?? null,
                    'subtotal' => $item['price'] * $item['quantity'],
                ];
            })->values()->toArray();

            session()->forget('cart');

            return view('store.payment', compact('snapToken', 'order', 'cartItems'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Direct Checkout (Beli Sekarang)
     */
    public function direct_checkout(Request $request, $product_id)
    {
        $product = Product::with('variants')->findOrFail($product_id);
        $quantity = $request->input('quantity', 1);

        if ($quantity < 1 || $quantity > $product->total_stock) {
            return redirect()->back()->with('error', 'Invalid quantity.');
        }

        DB::beginTransaction();
        try {
            $totalPrice = $product->price * $quantity;

            $customerName = Auth::check() ? Auth::user()->name : 'Guest';
            $customerEmail = Auth::check() ? Auth::user()->email : null;

            \Log::info('Creating transaction', ['totalPrice' => $totalPrice, 'customer' => $customerName]);

            $selectedSize = $request->input('size', null);

            // Build cart_items for stock tracking
            $stockItems = [[
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'size' => $selectedSize,
                'image_path' => $product->image_path ?? null,
            ]];

            $order = Transaction::create([
                'user_id'        => Auth::id(), // Simpan ID pembeli
                'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'product_id' => $product->id,
                'quantity' => $quantity,
                'cart_items' => $stockItems,
                'total_price' => $totalPrice,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'status' => 'pending',
                'payment_url' => null,
                'paid_at' => null,
            ]);

            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;
            // Disable SSL verification - use both options to fully disable
            \Midtrans\Config::$curlOptions = [
                CURLOPT_HTTPHEADER => [],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
            ];

            $displayName = $product->name;

            $params = [
                'transaction_details' => ['order_id' => $order->invoice_number, 'gross_amount' => $totalPrice],
                'item_details' => [[
                    'id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'name' => substr($product->name, 0, 50),
                ]],
                'customer_details' => [
                    'first_name' => Auth::check() ? Auth::user()->name : 'Guest',
                    'email' => Auth::check() ? Auth::user()->email : 'guest@example.com',
                ],
                'callbacks' => ['finish' => route('payment_return', $order->id)],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->update(['payment_url' => $snapToken]);

            DB::commit();

            // Save item details for display on payment page
            $cartItems = collect($stockItems)->map(function($item) {
                return [
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? null,
                    'image_path' => $item['image_path'] ?? null,
                    'subtotal' => $item['price'] * $item['quantity'],
                ];
            })->toArray();

            return view('store.payment', compact('snapToken', 'order', 'cartItems'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Checkout failed.');
        }
    }

    /**
     * Sinkronisasi Status Pembayaran dengan Midtrans
     */
    public function payment_status($order_id)
    {
        $order = Transaction::findOrFail($order_id);

        // If already success, redirect immediately
        if ($order->status === 'success') {
            return redirect()->route('profile')->with('success', 'Payment Successful! Thank you.');
        }

        $this->initMidtrans();

        try {
            $statusResponse = \Midtrans\Transaction::status($order->invoice_number);
            $transactionStatus = $statusResponse->transaction_status;

            // Update status ke 'success' agar bisa dijumlahkan di profil
            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $order->status = 'success';
                if (!$order->paid_at) $order->paid_at = now();
            } elseif ($transactionStatus == 'pending') {
                $order->status = 'pending';
            } else {
                $order->status = 'cancelled';
            }
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
     * Callback from Midtrans Snap JS (called via AJAX from onSuccess)
     * This directly marks the order as success without relying on Midtrans Status API
     */
    public function payment_callback(Request $request, $order_id)
    {
        $order = Transaction::findOrFail($order_id);
        $callbackStatus = $request->input('status', 'pending');

        if ($callbackStatus === 'success' && $order->status !== 'success') {
            $order->status = 'success';
            $order->paid_at = now();
            $order->save();
        } elseif ($callbackStatus === 'failed' && $order->status === 'pending') {
            $order->status = 'failed';
            $order->save();
        }

        return response()->json(['status' => $order->status]);
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
        $cartItems = $order->cart_items ?? [];

        // Format cart items for display
        $cartItems = collect($cartItems)->map(function($item) {
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
     * Konfigurasi Internal Midtrans
     */
    private function initMidtrans()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
        \Midtrans\Config::$curlOptions = [
            CURLOPT_HTTPHEADER => [], // TAMBAHKAN BARIS INI
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