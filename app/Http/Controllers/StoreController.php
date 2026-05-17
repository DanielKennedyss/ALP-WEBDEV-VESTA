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

class StoreController extends Controller
{
    public function show()
    {
        $products = Product::with(['category', 'variants'])->get();
        return view('home', compact('products'));
    }

    public function collection(Request $request)
    {
        $query = Product::with(['category', 'variants']);

        // Search by product name
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
            $query->where('gender', $request->gender);
        }

        // Filter by size (only show products that have this size variant)
        if ($request->filled('size')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('size_label', $request->size);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->get();

        // Get dynamic filter options from database
        $categories = Category::orderBy('name')->pluck('name');
        $genders = Product::select('gender')->distinct()->orderBy('gender')->pluck('gender');
        $sizes = ProductVariant::select('size_label')->distinct()->orderBy('size_label')->pluck('size_label');

        return view('store.collection', compact('products', 'categories', 'genders', 'sizes'));
    }

    public function add_to_cart(Request $request, $product_id)
    {
        $product = Product::with('variants')->findOrFail($product_id);
        $quantity = $request->input('quantity', 1);
        $selectedSize = $request->input('size', null);

        if ($quantity < 1) {
            return redirect()->back()->with('error', 'Quantity must be at least 1.');
        }

        // If a size is selected, check stock for that specific variant
        if ($selectedSize) {
            $variant = $product->variants->where('size_label', $selectedSize)->first();
            if (!$variant) {
                return redirect()->back()->with('error', 'Selected size is not available.');
            }
            $availableStock = $variant->stock;
        } else {
            $availableStock = $product->total_stock;
        }

        // Use composite key for cart (product_id + size)
        $cartKey = $selectedSize ? $product_id . '-' . $selectedSize : (string) $product_id;

        $cart = session()->get('cart', []);
        $existingQuantity = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $totalQuantity = $existingQuantity + $quantity;

        if ($totalQuantity > $availableStock) {
            return redirect()->back()->with('error', 'Requested total quantity exceeds available stock.');
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $totalQuantity;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'size' => $selectedSize,
                'image_path' => $product->image_path,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', $product->name . ' has been added to your cart.');
    }

    public function view_cart()
    {
        $cart = session()->get('cart', []);

        // Load full product data for each cart item (for images, variants)
        $cartProducts = [];
        foreach ($cart as $key => $item) {
            $cartProducts[$key] = Product::with('variants')->find($item['product_id']);
        }

        return view('store.cart', compact('cart', 'cartProducts'));
    }

    public function remove_from_cart($cart_key)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$cart_key])) {
            $removedName = $cart[$cart_key]['name'];
            unset($cart[$cart_key]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', $removedName . ' has been removed from your cart.');
        }
        return redirect()->back()->with('error', 'Item not found in cart.');
    }

    public function update_cart(Request $request, $cart_key)
    {
        $quantity = (int) $request->input('quantity');
        if ($quantity < 1) {
            return $this->remove_from_cart($cart_key);
        }

        $cart = session()->get('cart', []);
        if (!isset($cart[$cart_key])) {
            return redirect()->back()->with('error', 'Item not found in cart.');
        }

        $item = $cart[$cart_key];
        $product = Product::with('variants')->findOrFail($item['product_id']);

        // Check stock for the specific size variant or total stock
        if (!empty($item['size'])) {
            $variant = $product->variants->where('size_label', $item['size'])->first();
            $maxStock = $variant ? $variant->stock : 0;
        } else {
            $maxStock = $product->total_stock;
        }

        if ($quantity > $maxStock) {
            return redirect()->back()->with('error', 'Only ' . $maxStock . ' available in stock.');
        }

        $cart[$cart_key]['quantity'] = $quantity;
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Cart updated.');
    }

    public function update_cart_size(Request $request, $cart_key)
    {
        $newSize = $request->input('new_size');
        $cart = session()->get('cart', []);

        if (!isset($cart[$cart_key])) {
            return redirect()->back()->with('error', 'Item not found in cart.');
        }

        $item = $cart[$cart_key];
        $product = Product::with('variants')->findOrFail($item['product_id']);
        $variant = $product->variants->where('size_label', $newSize)->first();

        if (!$variant || $variant->stock <= 0) {
            return redirect()->back()->with('error', 'Selected size is not available.');
        }

        // Remove old entry
        unset($cart[$cart_key]);

        // Create new cart key with new size
        $newKey = $item['product_id'] . '-' . $newSize;

        // If already exists with new size, merge quantities
        if (isset($cart[$newKey])) {
            $mergedQty = $cart[$newKey]['quantity'] + $item['quantity'];
            if ($mergedQty > $variant->stock) {
                $mergedQty = $variant->stock;
            }
            $cart[$newKey]['quantity'] = $mergedQty;
        } else {
            $item['size'] = $newSize;
            $cart[$newKey] = $item;
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Size updated to ' . $newSize . '.');
    }

    public function checkout(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Your cart is empty!');
        }

        DB::beginTransaction();
        try {
            $totalPrice = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

            $order = Transaction::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'product_id' => $cart[key($cart)]['product_id'] ?? 0,
                'quantity' => collect($cart)->sum('quantity'),
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
                'transaction_details' => [
                    'order_id' => $order->invoice_number,
                    'gross_amount' => $totalPrice,
                ],
                'item_details' => $item_details,
                'customer_details' => [
                    'first_name' => Auth::check() ? Auth::user()->name : 'Guest',
                    'email' => Auth::check() ? Auth::user()->email : 'guest@example.com',
                ],
                'callbacks' => [
                    'finish' => route('payment_return', $order->id),
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->payment_url = $snapToken;
            $order->save();

            DB::commit();
            session()->forget('cart');

            return view('store.payment', compact('snapToken', 'order'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }

    public function direct_checkout(Request $request, $product_id)
    {
        \Log::info('direct_checkout called', ['product_id' => $product_id, 'quantity' => $request->input('quantity')]);

        $product = Product::with('variants')->findOrFail($product_id);
        $quantity = $request->input('quantity', 1);

        \Log::info('Product found', ['name' => $product->name, 'total_stock' => $product->total_stock]);

        if ($quantity < 1 || $quantity > $product->total_stock) {
            \Log::info('Invalid quantity redirect');
            return redirect()->back()->with('error', 'Invalid quantity.');
        }

        DB::beginTransaction();
        try {
            $totalPrice = $product->price * $quantity;

            $customerName = Auth::check() ? Auth::user()->name : 'Guest';
            $customerEmail = Auth::check() ? Auth::user()->email : null;

            \Log::info('Creating transaction', ['totalPrice' => $totalPrice, 'customer' => $customerName]);

            $order = Transaction::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'product_id' => $product->id,
                'quantity' => $quantity,
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
                'transaction_details' => [
                    'order_id' => $order->invoice_number,
                    'gross_amount' => $totalPrice,
                ],
                'item_details' => [
                    [
                        'id' => $product->id,
                        'price' => $product->price,
                        'quantity' => $quantity,
                        'name' => substr($displayName, 0, 50),
                    ]
                ],
                'customer_details' => [
                    'first_name' => $customerName,
                    'email' => $customerEmail ?? 'guest@example.com',
                ],
                'callbacks' => [
                    'finish' => route('payment_return', $order->id),
                ],
            ];

            // Suppress warning from Midtrans SDK - "Undefined array key 10023" is a known PHP warning issue
            $snapToken = @\Midtrans\Snap::getSnapToken($params);
            $order->payment_url = $snapToken;
            $order->save();

            \Log::info('Transaction created successfully', ['order_id' => $order->id, 'snapToken' => $snapToken]);

            DB::commit();

            return view('store.payment', compact('snapToken', 'order'));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Direct checkout failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }

    public function payment_retry($order_id)
    {
        $order = Transaction::findOrFail($order_id);

        // Only allow retry for pending orders
        if ($order->status !== 'pending' || !$order->payment_url) {
            return redirect()->route('profile')->with('error', 'This order cannot be retried.');
        }

        $snapToken = $order->payment_url;
        return view('store.payment', compact('snapToken', 'order'));
    }

    public function payment_status($order_id)
    {
        $order = Transaction::findOrFail($order_id);

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            $statusResponse = \Midtrans\Transaction::status($order->invoice_number);
            $transactionStatus = $statusResponse->transaction_status;

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $order->status = 'completed';
                if (!$order->paid_at) {
                    $order->paid_at = now();
                }
            } elseif ($transactionStatus == 'pending') {
                $order->status = 'pending';
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $order->status = 'cancelled';
            }
            $order->save();
        } catch (\Exception $e) {
            $order->status = 'cancelled';
            $order->payment_url = null;
            $order->save();
            return redirect()->route('profile')->with('error', 'Unable to retrieve payment status.');
        }

        if ($order->status == 'completed') {
            return redirect()->route('profile')->with('success', 'Payment successful! Thank you for shopping with VESTA.');
        } elseif ($order->status == 'pending') {
            return redirect()->route('profile')->with('error', 'Payment is still pending. You can retry from your order history.');
        } else {
            return redirect()->route('profile')->with('error', 'Payment failed or expired. Please try again.');
        }
    }

    public function payment_return($order_id)
    {
        return request()->has('order_id') ? $this->payment_status($order_id) : redirect()->route('payment_status', $order_id);
    }
}