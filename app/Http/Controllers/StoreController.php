<?php

namespace App\Http\Controllers;

use App\Models\Product;
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

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $products = $query->orderBy('created_at', 'desc')->get();
        return view('store.collection', compact('products'));
    }

    public function add_to_cart(Request $request, $product_id)
    {
        $product = Product::with('variants')->findOrFail($product_id);
        $quantity = $request->input('quantity', 1);

        if ($quantity < 1) {
            return redirect()->back()->with('error', 'Quantity must be at least 1.');
        }

        // Gunakan total stock dari semua variants
        $totalStock = $product->total_stock;

        $cart = session()->get('cart', []);
        $existingQuantity = isset($cart[$product_id]) ? $cart[$product_id]['quantity'] : 0;
        $totalQuantity = $existingQuantity + $quantity;

        if ($totalQuantity > $totalStock) {
            return redirect()->back()->with('error', 'Requested total quantity exceeds available stock.');
        }

        if (isset($cart[$product_id])) {
            $cart[$product_id]['quantity'] = $totalQuantity;
        } else {
            $cart[$product_id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function view_cart()
    {
        $cart = session()->get('cart', []);
        return view('store.cart', compact('cart'));
    }

    public function remove_from_cart($product_id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$product_id])) {
            unset($cart[$product_id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    public function update_cart(Request $request, $product_id)
    {
        $quantity = (int) $request->input('quantity');
        if ($quantity < 1) {
            return $this->remove_from_cart($product_id);
        }

        $product = Product::with('variants')->findOrFail($product_id);
        if ($quantity > $product->total_stock) {
            return redirect()->back()->with('error', 'Requested quantity exceeds available stock.');
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$product_id])) {
            $cart[$product_id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Cart updated successfully.');
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
            return redirect()->route('home')->with('error', 'Unable to retrieve payment status.');
        }

        if ($order->status == 'completed') {
            return redirect()->route('home')->with('success', 'Payment successful!');
        } elseif ($order->status == 'pending') {
            return redirect()->route('home')->with('error', 'Payment is pending. Please complete it.');
        } else {
            return redirect()->route('home')->with('error', 'Payment failed or expired.');
        }
    }

    public function payment_return($order_id)
    {
        return request()->has('order_id') ? $this->payment_status($order_id) : redirect()->route('payment_status', $order_id);
    }
}