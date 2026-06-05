<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\LoyaltyPointHistory;
use App\Models\ProductReview;
use App\Models\Voucher;
use App\Models\CartItem;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StoreController extends Controller
{
    /**
     * Tampilan Landing Page VESTA
     */
    public function show(): View
    {
        $products = Product::with(['category', 'variants'])->get();
        return view('home', compact('products'));
    }

    /**
     * Halaman Koleksi Terkunci Berbasis Event Aktif (Locked Event Landing Page)
     */
    public function collection(Request $request): View
    {
        session()->forget('buy_now');
        session()->forget('applied_voucher');

        $activeEvent = Event::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with(['products' => function($q) {
                $q->with(['category', 'variants', 'reviews.user'])
                  ->withAvg('reviews', 'rating')
                  ->withCount('reviews');
            }])
            ->first();

        // Fallback ke produk terbaru jika tidak ada event kurasi yang aktif
        $products = collect();
        if (!$activeEvent) {
            $products = Product::with(['category', 'variants', 'reviews.user'])
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->orderBy('created_at', 'desc')
                ->take(12)
                ->get();
        }

        return view('store.collection', compact('activeEvent', 'products'));
    }

    /**
     * Halaman Katalog Utama: Integrasi Multi-Column Fonetik Typo-Tolerant Fuzzy Search
     */
    public function catalog(Request $request)
    {
        session()->forget('buy_now');
        session()->forget('applied_voucher');

        $query = Product::with(['category', 'variants', 'reviews.user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

// ==========================================================================
        // ADVANCED TYPO-TOLERANT ENGINE (Native SQL Fuzziness + Soundex)
        // ==========================================================================
        if ($request->filled('search')) {
            $search = trim($request->search);
            
            $query->where(function ($q) use ($search) {
                // 1. EXACT & STANDARD LIKE (Prioritas Utama untuk exact match)
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('season', 'like', '%' . $search . '%')
                  ->orWhereHas('category', function ($catQ) use ($search) {
                      $catQ->where('name', 'like', '%' . $search . '%');
                  });

                // Memecah kata untuk safety net
                $words = explode(' ', $search);
                
                foreach ($words as $word) {
                    if (strlen($word) > 2) { // Hanya memproses kata > 2 huruf
                        
                        // 2. WILDCARD CHARACTER INJECTION (Deteksi Typo Hilang Huruf)
                        // Mengubah input typo "shrt" menjadi "%s%h%r%t%"
                        $fuzzyWord = '%' . implode('%', str_split($word)) . '%';
                        
                        $q->orWhere('name', 'like', $fuzzyWord)
                          ->orWhere('season', 'like', $fuzzyWord)
                          ->orWhere('description', 'like', $fuzzyWord);

                        // 3. SOUNDEX FALLBACK (Deteksi Typo Fonetik)
                        // Mempertahankan fallback suara untuk kasus salah eja vokal
                        $q->orWhereRaw("SOUNDEX(name) = SOUNDEX(?)", [$word])
                          ->orWhereRaw("SOUNDEX(season) = SOUNDEX(?)", [$word])
                          ->orWhereRaw("SOUNDEX(description) LIKE CONCAT('%', SOUNDEX(?), '%')", [$word]);
                    }
                }
            });
        }
        // ==========================================================================

        // Filter Berdasarkan Musim Mode (Seasonal Filter)
        if ($request->filled('season')) {
            $query->where('season', $request->season);
        }

        // Filter Berdasarkan Kategori Pakaian
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // Filter Berdasarkan Keterikatan Event Curated
        if ($request->filled('filter_event')) {
            $query->whereHas('events', function ($q) use ($request) {
                $q->where('events.id', $request->filter_event);
            });
        }

        // Filter Berdasarkan Target Gender (Include Unisex Allocation)
        if ($request->filled('gender')) {
            $selectedGender = $request->gender;
            if (in_array(strtolower($selectedGender), ['male', 'female'])) {
                $query->whereIn('gender', [$selectedGender, 'Unisex', 'unisex']);
            } else {
                $query->where('gender', $selectedGender);
            }
        }

        // Filter Berdasarkan Ukuran Stok Pakaian
        if ($request->filled('size')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('size_label', $request->size);
            });
        }

        // Aturan Pengurutan Katalog (Sorting Engine)
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_low': $query->orderBy('price', 'asc'); break;
            case 'price_high': $query->orderBy('price', 'desc'); break;
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            default: $query->orderBy('created_at', 'desc'); break;
        }

        // Eksekusi data kompilasi dengan Pagination
        $products = $query->paginate(12)->withQueryString();
        
        // Pengambilan data penunjang filter untuk antarmuka komponen Blade
        $categories = Category::orderBy('name')->pluck('name');
        $genders = Product::select('gender')->distinct()->orderBy('gender')->pluck('gender');
        $sizes = ProductVariant::select('size_label')->distinct()->orderBy('size_label')->pluck('size_label');
        $seasons = ['Spring', 'Summer', 'Autumn', 'Winter']; 

        $activeEvent = Event::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        $activeEventProductIds = [];
        if ($activeEvent) {
            $activeEventProductIds = $activeEvent->products()->pluck('products.id')->toArray();
        }

        $filteredEvent = $request->filled('filter_event') ? Event::find($request->filter_event) : null;

        // ==========================================================================
        // REVISI UTAMA: INTERSEPTOR AJAX/XMLHTTPREQUEST UNTUK LIVE SEARCH BOX
        // ==========================================================================
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('store.catalog', compact(
                'products', 'categories', 'genders', 'sizes', 'seasons', 
                'activeEvent', 'activeEventProductIds', 'filteredEvent'
            ));
        }
        // ==========================================================================

        return view('store.catalog', compact(
            'products', 'categories', 'genders', 'sizes', 'seasons', 
            'activeEvent', 'activeEventProductIds', 'filteredEvent'
        ));
    }

    /**
     * Tambah ke Keranjang Belanja (Session-Based with DB Sync Capability)
     */
    public function add_to_cart(Request $request, $product_id): RedirectResponse
    {
        $product = Product::with('variants')->findOrFail($product_id);
        $quantity = (int) $request->input('quantity', 1);
        $selectedSize = $request->input('size', null);

        if ($quantity < 1) {
            return redirect()->back()->with('error', 'Quantity must be at least 1.');
        }

        if ($selectedSize) {
            $variant = $product->variants->where('size_label', $selectedSize)->first();
            if (!$variant) return redirect()->back()->with('error', 'Selected size option is unavailable.');
            $availableStock = $variant->stock;
        } else {
            $availableStock = $product->total_stock;
        }

        $cartKey = $selectedSize ? $product_id . '-' . $selectedSize : (string) $product_id;
        $cart = session()->get('cart', []);
        $existingQuantity = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $totalQuantity = $existingQuantity + $quantity;

        if ($totalQuantity > $availableStock) {
            return redirect()->back()->with('error', 'Requested volume exceeds current inventory allocation.');
        }

        $cart[$cartKey] = [
            'product_id' => $product->id,
            'name'       => $product->name,
            'price'      => $product->price,
            'quantity'   => $totalQuantity,
            'size'       => $selectedSize,
            'image_path' => $product->image_path,
        ];

        session()->put('cart', $cart);

        if (Auth::check()) {
            CartItem::saveSessionCartToDb(Auth::user());
        }

        return redirect()->back()->with('success', "{$product->name} successfully appended to cart.");
    }

    /**
     * Render Halaman Keranjang Belanja (Cart View Summary)
     */
    public function view_cart(Request $request): View
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
     * Interseptor Pengaman Alur Checkout Direct Link
     */
    public function view_checkout()
    {
        return redirect()->route('cart.view');
    }

    /**
     * Alur Instant Purchase (Beli Sekarang Bypass Cart)
     */
    public function direct_checkout(Request $request, $product_id): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Please authenticate your identity first to acquire products.');
        }

        $product = Product::with('variants')->findOrFail($product_id);
        $quantity = (int) $request->input('quantity', 1);
        $selectedSize = $request->input('size', null);

        if ($selectedSize) {
            $variant = $product->variants->where('size_label', $selectedSize)->first();
            if (!$variant) return redirect()->back()->with('error', 'Size variant is out of scope.');
            $availableStock = $variant->stock;
        } else {
            $availableStock = $product->total_stock;
        }

        if ($quantity < 1 || $quantity > $availableStock) {
            return redirect()->back()->with('error', 'Requested volume is mathematically invalid or out of stock.');
        }

        session()->forget('buy_now');
        session()->forget('applied_voucher');

        session()->put('buy_now', [
            'product_id' => $product->id,
            'name'       => $product->name,
            'price'      => $product->price,
            'quantity'   => $quantity,
            'size'       => $selectedSize,
            'image_path' => $product->image_path ?? null,
        ]);

        return redirect()->route('cart.view');
    }

    /**
     * Core Checkout Workflow & Integrasi Kalkulasi Pajak Dinamis (PPN 11%)
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->back()->with('error', 'Unauthenticated secure context.');

        $isBuyNow = session()->has('buy_now');
        $cart = $isBuyNow ? [session('buy_now')] : session('cart', []);

        $checkedKeys = [];
        if (!$isBuyNow && $request->has('checked_items') && !empty($request->input('checked_items'))) {
            $checkedKeys = explode(',', $request->input('checked_items'));
            $cart = array_filter($cart, function($key) use ($checkedKeys) {
                return in_array($key, $checkedKeys);
            }, ARRAY_FILTER_USE_KEY);
        }

        if (empty($cart)) return redirect()->back()->with('error', 'Transaction compilation scope is empty.');
        
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
                $pointsRedeemed = (int) ceil($discountPoints / 1000);
            }
        }

        $baseTaxableAmount = max(0, $priceAfterVoucher - $discountPoints);
        
        $taxPercentage = 0.11;
        $calculatedTax = (int) round($baseTaxableAmount * $taxPercentage);

        $shippingAddress = $request->input('shipping_address', null);
        $shippingCourier = $request->input('shipping_courier', null);
        $shippingService = $request->input('shipping_service', null);
        $shippingCost = (int) $request->input('shipping_cost', 0);

        $totalPrice = max(1000, $baseTaxableAmount + $calculatedTax + $shippingCost);

        DB::beginTransaction();
        try {
            if ($pointsRedeemed > 0) {
                $user = $user->fresh();
                if ($user->loyalty_points < $pointsRedeemed) {
                    throw new \Exception('Security warning: Manipulated or sync error regarding customer loyalty points.');
                }
                $user->decrement('loyalty_points', $pointsRedeemed);
            }

            if ($voucherId) {
                $voucher = Voucher::find($voucherId);
                if ($voucher) $voucher->increment('used_quota');
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
                'tax'              => $calculatedTax, 
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
                    'description'    => "Redeemed points for Order #{$invoiceNumber}",
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

            if ($calculatedTax > 0) {
                $item_details[] = [
                    'id'       => 'GOVT-TAX-11',
                    'price'    => (int) $calculatedTax,
                    'quantity' => 1,
                    'name'     => 'Government Tax (PPN 11%)',
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
                    CartItem::where('user_id', Auth::id())->delete();
                }
            }

            try {
                Mail::to($order->customer_email)->send(new \App\Mail\OrderPlacedMail($order));
            } catch (\Exception $mailEx) {
                Log::error('Mail Placement Notification Exception Captured: ' . $mailEx->getMessage());
            }

            return view('store(' . $order->id . ').payment', compact('snapToken', 'order', 'cartItems'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Unified Checkout Execution Error Architecture Broken: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Checkout pipeline structural error: ' . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi Status Pembayaran Gateway dengan Core Database
     */
    public function payment_status($order_id)
    {
        $order = Transaction::findOrFail($order_id);
        if ($order->status === 'success') {
            return redirect()->route('profile')->with('success', 'Payment Route Complete. Executive Verification Passed.');
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

                if ($oldStatus !== 'success') {
                    try {
                        Mail::to($order->customer_email)->send(new \App\Mail\PaymentSuccessMail($order));
                    } catch (\Exception $mailEx) {
                        Log::error('Mail Status Client Return Execution Warning: ' . $mailEx->getMessage());
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
            Log::error('Payment Status Verification Framework Fallback Error: ' . $e->getMessage());
        }

        if ($order->status == 'success') {
            return redirect()->route('profile')->with('success', 'Payment Successful! Thank you.');
        }
        return redirect()->route('profile')->with('error', 'Payment current deployment state: ' . $order->status);
    }

    /**
     * Webhook Backend Gateway Async Processor (Kunci Sinkronisasi Real-Time)
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

            try {
                Mail::to($order->customer_email)->send(new \App\Mail\PaymentSuccessMail($order));
            } catch (\Exception $mailEx) {
                Log::error('Mail Success Notification Webhook Exception Warning: ' . $mailEx->getMessage());
            }
        } elseif ($callbackStatus === 'failed' && $oldStatus === 'pending') {
            $order->status = 'failed';
            $this->handleFailedTransactionRefund($order, $oldStatus, $order->status);
            $order->save();
        }

        return response()->json(['status' => $order->status]);
    }

    /**
     * Manajemen Reverse Engine Pengembalian Poin Loyalitas Jika Transaksi Korup/Batal
     */
    private function handleFailedTransactionRefund(Transaction $order, $oldStatus, $newStatus): void
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
                    'description'    => "Automatic safety rollback points from broken Order #{$order->invoice_number}",
                ]);
            }
        }
    }

    /**
     * Fungsionalitas Mengulang Request Token Snap Pembayaran untuk Status Order Pending
     */
    public function payment_retry($order_id): View|RedirectResponse
    {
        $order = Transaction::findOrFail($order_id);
        if ($order->status === 'success') {
            return redirect()->route('profile')->with('success', 'This configuration payload has already been completed.');
        }

        if (!$order->payment_url) {
            return redirect()->route('profile')->with('error', 'Token execution buffer missing for this transaction allocation.');
        }

        $snapToken = $order->payment_url;
        $cartItems = collect($order->cart_items ?? [])->map(function($item) {
            return [
                'name'       => $item['name'] ?? 'Unknown Collection Piece',
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
     * Deklarasi Konfigurasi Proteksi Enkapsulasi Client Curl Midtrans
     */
    private function initMidtrans(): void
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
     * Fungsionalitas Modifikasi Kuantitas Item di Sisi Keranjang Belanja
     */
    public function update_cart(Request $request, $cart_key): RedirectResponse
    {
        if ($cart_key === 'buy_now') {
            $item = session()->get('buy_now');
            if ($item) {
                $newQuantity = (int) $request->input('quantity', 1);
                if ($newQuantity <= 0) {
                    session()->forget('buy_now');
                    session()->forget('applied_voucher');
                    return redirect()->back()->with('success', 'Item removed from context successfully.');
                }

                $product = Product::with('variants')->find($item['product_id']);
                if ($product) {
                    $size = $item['size'] ?? null;
                    $maxStock = $size ? ($product->variants->where('size_label', $size)->first()?->stock ?? 0) : $product->total_stock;

                    if ($newQuantity > $maxStock) {
                        return redirect()->back()->with('error', 'Requested amount hits the maximum boundary limit of storage allocation.');
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
                if (Auth::check()) CartItem::saveSessionCartToDb(Auth::user());
                return redirect()->back()->with('success', 'Item removed from context successfully.');
            }

            $product = Product::with('variants')->find($cart[$cart_key]['product_id']);
            if ($product) {
                $size = $cart[$cart_key]['size'] ?? null;
                $maxStock = $size ? ($product->variants->where('size_label', $size)->first()?->stock ?? 0) : $product->total_stock;

                if ($newQuantity > $maxStock) {
                    return redirect()->back()->with('error', 'Requested amount hits the maximum boundary limit of storage allocation.');
                }
            }

            $cart[$cart_key]['quantity'] = $newQuantity;
            session()->put('cart', $cart);
            if (Auth::check()) CartItem::saveSessionCartToDb(Auth::user());
        }
        return redirect()->back();
    }

    /**
     * Modifikasi Ukuran Stok Pakaian dari Komponen Dropdown Keranjang Belanja
     */
    public function update_cart_size(Request $request, $cart_key): RedirectResponse
    {
        if ($cart_key === 'buy_now') {
            $item = session()->get('buy_now');
            if ($item) {
                $item['size'] = $request->input('new_size');
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
            if (Auth::check()) CartItem::saveSessionCartToDb(Auth::user());
        }
        return redirect()->back();
    }

    /**
     * Penghapusan Item Tertentu Secara Instan dari Keranjang Belanja
     */
    public function remove_from_cart($cart_key): RedirectResponse
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
                CartItem::where('user_id', Auth::id())->delete();
            }
        }
        return redirect()->back();
    }

    public function get_wishlist()
    {
        if (!Auth::check()) return response()->json([]);
        $user = Auth::user();
        $products = Product::join('wishlists', 'products.id', '=', 'wishlists.product_id')
            ->where('wishlists.user_id', $user->id)
            ->select('products.*')
            ->with(['category'])
            ->get();
        return response()->json($products);
    }

    public function toggle_wishlist($product_id)
    {
        if (!Auth::check()) return response()->json(['error' => 'Unauthenticated access parameter.'], 401);
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
        return response()->json(['status' => $status, 'count' => $count]);
    }

    public function sync_wishlist(Request $request)
    {
        if (!Auth::check()) return response()->json(['error' => 'Unauthenticated access parameter.'], 401);
        $user = Auth::user();
        $productIds = $request->input('product_ids', []);

        foreach ($productIds as $id) {
            if (Product::find($id)) {
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
        return response()->json(['status' => 'synced', 'count' => $count]);
    }

    /**
     * Klaim Potongan Voucher Belanja di Halaman Checkout VESTA
     */
    public function apply_voucher(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please authenticate to use promotional vouchers.'], 401);
        }

        $request->validate(['voucher_code' => 'required|string']);

        $voucherCode = strtoupper($request->input('voucher_code'));
        $voucher = Voucher::where('code', $voucherCode)->first();

        if (!$voucher) return response()->json(['success' => false, 'message' => 'Voucher code is non-existent.']);
        if ($voucher->isExpired()) return response()->json(['success' => false, 'message' => 'This allocation time boundary has expired.']);
        if ($voucher->isSoldOut()) return response()->json(['success' => false, 'message' => 'Voucher total quota has reached its limits.']);

        $isBuyNow = session()->has('buy_now');
        $cartItems = $isBuyNow ? [session('buy_now')] : session('cart', []);

        if (empty($cartItems)) return response()->json(['success' => false, 'message' => 'Checkout workspace is vacant.']);

        $subtotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        $discountAmount = (int) $voucher->calculateDiscount($subtotal);
        $discountAmount = min($discountAmount, $subtotal - 1000);
        
        if ($discountAmount < 0) $discountAmount = 0;

        session()->put('applied_voucher', [
            'id'       => $voucher->id,
            'code'     => $voucher->code,
            'discount' => $discountAmount
        ]);

        return response()->json([
            'success'            => true,
            'message'            => "Voucher \"{$voucher->code}\" successfully injected into core context!",
            'discount'           => $discountAmount,
            'type'               => $voucher->type,
            'reward_value'       => (float) $voucher->reward_value,
            'formatted_discount' => 'Rp ' . number_format($discountAmount, 0, ',', '.')
        ]);
    }

    /**
     * Membatalkan Pesanan yang Berstatus Pending Sebelum Kadaluarsa
     */
    public function cancelOrder(Transaction $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if ($order->status !== 'pending') return redirect()->back()->with('error', 'Only unfulfilled metrics can be terminated.');

        $oldStatus = $order->status;
        $order->status = 'cancelled';

        $this->handleFailedTransactionRefund($order, $oldStatus, 'cancelled');
        $order->save();

        return redirect()->back()->with('success', "Order #{$order->invoice_number} successfully aborted.");
    }

    /**
     * Monitoring Status Pelacakan Kiriman Logistik Pesanan (Include Review History Check)
     */
    public function trackOrder(Transaction $order): View
    {
        if ($order->user_id !== Auth::id()) abort(403);
        $order->load(['reviews.product']);
        return view('store.track', compact('order'));
    }

    /**
     * Konfirmasi Penerimaan Barang dari Sisi Pembeli (Mark Cargo Delivered)
     */
    public function markAsReceived(Transaction $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if ($order->status !== 'shipped') return redirect()->back()->with('error', 'Only active shipping routes can be configured as delivered.');

        $order->status = 'delivered';
        $order->save();

        return redirect()->back()->with('success', "Order #{$order->invoice_number} has been updated to delivered state.");
    }

    /**
     * Submit Ulasan & Rating Produk Pasca Penerimaan Barang (CRM Implementation)
     */
    public function submitReview(Request $request, Transaction $order): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if (!in_array($order->status, ['delivered', 'completed'])) {
            return redirect()->back()->with('error', 'Feedback form is locked until product delivery verification.');
        }

        $request->validate([
            'reviews'              => 'required|array',
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating'     => 'required|integer|min:1|max:5',
            'reviews.*.comment'    => 'nullable|string|max:1000',
        ]);

        foreach ($request->input('reviews') as $reviewData) {
            $exists = ProductReview::where('user_id', Auth::id())
                ->where('product_id', $reviewData['product_id'])
                ->where('transaction_id', $order->id)
                ->exists();

            if (!$exists) {
                ProductReview::create([
                    'user_id'        => Auth::id(),
                    'product_id'     => $reviewData['product_id'],
                    'transaction_id' => $order->id,
                    'rating'         => $reviewData['rating'],
                    'comment'        => $reviewData['comment'] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Thank you for documenting your luxury product feedback experience!');
    }
}