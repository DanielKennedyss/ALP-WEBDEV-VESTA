@extends('base.base')

@section('content')
<div class="pt-32 pb-24 px-6 lg:px-8 bg-white min-h-screen dynamic-fade-in animate-fade-in">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            <span class="text-[10px] tracking-[0.3em] text-stone-400 uppercase block">Shopping Bag</span>
            <h1 class="text-2xl md:text-3xl font-serif tracking-[0.2em] mt-3 text-stone-900">YOUR CART</h1>
            <div class="w-12 h-px bg-stone-900 mx-auto mt-6"></div>
        </div>

        @if(empty($cart))
            <div class="text-center py-28 border border-dashed border-stone-200">
                <p class="text-xs tracking-[0.25em] text-stone-400 uppercase mb-8">Your shopping bag is empty</p>
                <a href="{{ route('collection') }}" class="inline-block bg-stone-900 text-white text-[10px] tracking-[0.2em] px-8 py-4 uppercase hover:bg-stone-800 transition-all">Browse Collection</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                
                {{-- Sisi Kiri: List Item Keranjang Belanja --}}
                <div class="lg:col-span-7">
                    @if($isBuyNow)
                        <div class="bg-stone-50 border border-stone-200 p-4 flex items-center justify-between text-xs text-stone-600 rounded-sm mb-6">
                            <span class="font-medium uppercase tracking-wider">⚡ Buy Now Flow Active</span>
                            <a href="{{ route('cart.view', ['cancel_buy_now' => 1]) }}" class="text-stone-900 hover:text-red-700 underline tracking-widest uppercase text-[10px] font-bold">Switch to Normal Cart</a>
                        </div>
                    @endif
                    
                    {{-- STEP 1: REVIEW ITEMS --}}
                    <div id="checkout_step_1" class="space-y-6 transition-all duration-500">
                        <div class="divide-y divide-stone-100 border-t border-b border-stone-200">
                            @foreach($cart as $cart_key => $item)
                                @php $product = $cartProducts[$cart_key] ?? null; @endphp
                                <div class="flex gap-6 py-6 items-start cart-item" data-key="{{ $cart_key }}" data-price="{{ $item['price'] }}" data-qty="{{ $item['quantity'] }}">
                                    
                                    {{-- Kiri: Gambar Item --}}
                                    <div class="w-32 h-44 shrink-0 bg-stone-50 shadow-sm border border-stone-100 overflow-hidden">
                                        <img src="{{ $item['image_path'] }}" class="w-full h-full object-cover">
                                    </div>
                                    
                                    {{-- Kanan: Deskripsi & Controls --}}
                                    <div class="flex-1 min-w-0 flex flex-col justify-between h-full">
                                        <div>
                                            <div class="flex justify-between items-start gap-4">
                                                <h3 class="text-xs font-semibold text-stone-955 tracking-wider uppercase truncate">{{ $item['name'] }}</h3>
                                                <button type="button" onclick="removeItem('{{ $cart_key }}')" class="text-[9px] tracking-[0.2em] text-stone-400 hover:text-red-700 uppercase transition-all shrink-0">Remove</button>
                                            </div>
                                            
                                            <p class="text-[10px] text-stone-500 font-mono mt-1">IDR {{ number_format($item['price'], 0, ',', '.') }} / piece</p>
                                            
                                            @php
                                                $selectedVariant = null;
                                                if ($product && isset($item['size'])) {
                                                    $selectedVariant = $product->variants->where('size_label', $item['size'])->first();
                                                }
                                                $maxStock = $selectedVariant ? $selectedVariant->stock : ($product ? $product->total_stock : 0);
                                                $currentQty = $item['quantity'];
                                                $isMax = $currentQty >= $maxStock;
                                            @endphp
                                            
                                            <div class="mt-3 flex items-center gap-3">
                                                <span class="text-[10px] text-stone-400 uppercase tracking-widest">Size:</span>
                                                <div class="relative group w-32">
                                                    <select onchange="updateSize('{{ $cart_key }}', this.value)" 
                                                            class="w-full font-mono text-[10px] tracking-[0.1em] uppercase bg-transparent bg-none border-b border-stone-200 py-1 focus:border-stone-900 focus:outline-none transition-all duration-300 cursor-pointer appearance-none text-stone-700 hover:text-stone-900 pr-6">
                                                        @foreach($product->variants as $v)
                                                            @php
                                                                $spacesCount = 8 - strlen($v->size_label) - strlen((string)$v->stock);
                                                                $spaces = str_repeat("\u{00A0}", max(2, $spacesCount));
                                                            @endphp
                                                            <option value="{{ $v->size_label }}" {{ $item['size'] == $v->size_label ? 'selected' : '' }} class="font-mono">
                                                                {{ $v->size_label }}{{ $spaces }}STOK: {{ $v->stock }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-stone-400 group-hover:text-stone-900 transition-colors">
                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 flex items-center justify-between pt-3 border-t border-stone-50">
                                            {{-- Quantity Selector --}}
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center border border-stone-200 bg-white">
                                                    <button type="button" onclick="updateQty('{{ $cart_key }}', {{ $item['quantity'] - 1 }})" class="w-7 h-7 hover:bg-stone-50 transition-colors flex items-center justify-center text-xs text-stone-600">−</button>
                                                    <span class="w-8 text-center text-xs font-semibold text-stone-800 font-mono">{{ $item['quantity'] }}</span>
                                                    <button type="button" {{ $isMax ? 'disabled' : '' }} onclick="updateQty('{{ $cart_key }}', {{ $item['quantity'] + 1 }})" class="w-7 h-7 hover:bg-stone-50 transition-colors flex items-center justify-center text-xs text-stone-600 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white">+</button>
                                                </div>
                                                @if($isMax)
                                                    <span class="text-[8px] text-red-500 font-bold uppercase tracking-wider">MAX STOCK REACHED</span>
                                                @endif
                                            </div>
                                            
                                            {{-- Subtotal per Item --}}
                                            <div class="text-right">
                                                <span class="text-[9px] text-stone-400 uppercase tracking-widest block">Item Subtotal</span>
                                                <span class="text-xs font-semibold font-mono text-stone-900">IDR {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- STEP 2: SHIPPING ADDRESS --}}
                    <div id="checkout_step_2" class="space-y-8 hidden transition-all duration-500">
                        <div class="bg-white border border-stone-200 p-8 shadow-sm space-y-6">
                            <div class="flex justify-between items-center border-b border-stone-100 pb-4">
                                <h3 class="text-xs tracking-[0.2em] font-serif uppercase text-stone-900 font-bold">Shipping Address</h3>
                                <button type="button" onclick="toggleCheckoutStep(1)" class="text-[10px] tracking-widest uppercase font-bold text-stone-500 hover:text-black">
                                    ← Back to Cart
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Dropdown Provinsi --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] tracking-widest uppercase text-stone-400 font-semibold">Province</label>
                                    <div class="relative group">
                                        <select id="select_province" onchange="onProvinceChange(this.value)"
                                                class="w-full font-mono text-[10px] tracking-[0.1em] uppercase bg-transparent bg-none border-b border-stone-200 py-2.5 focus:border-stone-900 focus:outline-none transition-all duration-300 cursor-pointer appearance-none text-stone-800 pr-6">
                                            <option value="">Select Province</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-stone-400 group-hover:text-stone-900 transition-colors">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Dropdown Kota/Kabupaten --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] tracking-widest uppercase text-stone-400 font-semibold">City / Kabupaten</label>
                                    <div class="relative group">
                                        <select id="select_city" onchange="onCityChange()" disabled
                                                class="w-full font-mono text-[10px] tracking-[0.1em] uppercase bg-transparent bg-none border-b border-stone-200 py-2.5 focus:border-stone-900 focus:outline-none transition-all duration-300 cursor-pointer appearance-none text-stone-800 pr-6 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <option value="">Select Province First</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-stone-400 group-hover:text-stone-900 transition-colors">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Dropdown Kurir --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] tracking-widest uppercase text-stone-400 font-semibold">Courier</label>
                                    <div class="relative group">
                                        <select id="select_courier" onchange="fetchShippingCost()" disabled
                                                class="w-full font-mono text-[10px] tracking-[0.1em] uppercase bg-transparent bg-none border-b border-stone-200 py-2.5 focus:border-stone-900 focus:outline-none transition-all duration-300 cursor-pointer appearance-none text-stone-800 pr-6 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <option value="">Select City First</option>
                                            <option value="jne">JNE (Jalur Nugraha Ekakurir)</option>
                                            <option value="pos">POS Indonesia</option>
                                            <option value="tiki">TIKI (Titipan Kilat)</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-stone-400 group-hover:text-stone-900 transition-colors">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Dropdown Layanan --}}
                                <div class="space-y-2">
                                    <label class="text-[10px] tracking-widest uppercase text-stone-400 font-semibold">Delivery Service</label>
                                    <div class="relative group">
                                        <select id="select_service" onchange="onServiceChange(this)" disabled
                                                class="w-full font-mono text-[10px] tracking-[0.1em] uppercase bg-transparent bg-none border-b border-stone-200 py-2.5 focus:border-stone-900 focus:outline-none transition-all duration-300 cursor-pointer appearance-none text-stone-800 pr-6 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <option value="">Select Courier First</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-stone-400 group-hover:text-stone-900 transition-colors">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div id="shipping_loading" class="text-[8px] uppercase tracking-widest text-stone-400 hidden mt-1.5 animate-pulse font-semibold">Fetching shipping services...</div>
                                </div>
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div class="space-y-2">
                                <label class="text-[10px] tracking-widest uppercase text-stone-400 font-semibold">Full Street Address</label>
                                <textarea id="input_address" placeholder="ENTER YOUR FULL ADDRESS (STREET NAME, BUILDING, SUITE, ETC.)" rows="3"
                                          class="w-full border border-stone-200 px-4 py-3 text-[10px] tracking-widest uppercase focus:outline-none focus:border-black transition-all font-mono resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Sisi Kanan: Order Summary & Loyalty & Coupons --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white border border-stone-200 p-8 shadow-sm">
                        <h3 class="text-xs tracking-[0.2em] font-serif uppercase mb-8 text-stone-900 border-b border-stone-100 pb-4">Order Summary</h3>
                        
                        <div class="space-y-4 text-xs">
                            <div class="flex justify-between text-stone-500">
                                <span>Jumlah Item</span>
                                <span class="font-mono text-stone-800">{{ collect($cart)->sum('quantity') }} items</span>
                            </div>
                            
                            <div class="flex justify-between text-stone-500">
                                <span>Subtotal</span>
                                <span class="font-mono text-stone-800">IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="flex justify-between text-stone-500">
                                <span>Shipping</span>
                                <span id="summary_shipping_val" class="uppercase tracking-widest text-[9px] font-bold text-stone-400">TBD</span>
                            </div>
                            
                            @php $vatAmount = $subtotal * 0.10; @endphp
                            <div class="flex justify-between text-stone-500 border-b border-stone-100 pb-4">
                                <span>VAT (10% Included)</span>
                                <span class="font-mono text-stone-800">IDR {{ number_format($vatAmount, 0, ',', '.') }}</span>
                            </div>
                            
                            {{-- Voucher Discount (Dynamic) --}}
                            <div id="voucher_discount_row" class="flex justify-between text-stone-900 font-medium hidden border-b border-stone-100 pb-2 border-dashed">
                                <span class="flex items-center gap-1">Voucher Discount (<span id="active_voucher_badge" class="uppercase tracking-wider font-bold"></span>)</span>
                                <span class="font-mono text-stone-900">-IDR <span id="voucher_discount_val">0</span></span>
                            </div>
                            
                            {{-- Points Discount (Dynamic) --}}
                            <div id="points_discount_row" class="flex justify-between text-emerald-600 font-medium hidden">
                                <span>Points Discount</span>
                                <span class="font-mono">-IDR <span id="points_discount_val">0</span></span>
                            </div>
                            
                            {{-- Grand Total --}}
                            <div class="flex justify-between font-serif text-base font-semibold pt-4 text-stone-900 border-t border-stone-100">
                                <span>Total</span>
                                <span id="final_total_val" class="font-mono text-stone-955 text-lg font-bold">IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        @if(Auth::check())
                            {{-- Loyalty Rewards Section --}}
                            <div class="mt-8 pt-6 border-t border-stone-100 space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-[10px] tracking-[0.2em] font-serif uppercase text-stone-900 font-bold">VESTA Loyalty Rewards</h4>
                                    <span class="text-[9px] bg-stone-100 px-2.5 py-0.5 text-stone-500 rounded-full font-bold">
                                        {{ number_format(Auth::user()->loyalty_points ?? 0, 0) }} PTS
                                    </span>
                                </div>
                                
                                @if(Auth::user()->loyalty_points > 0)
                                    <div class="flex items-center gap-3 bg-stone-50 p-4 border border-stone-100 transition-all duration-300 hover:border-stone-900 rounded-sm">
                                        <input type="checkbox" id="use_points" name="use_points" value="1" 
                                               class="w-4 h-4 text-black border-stone-300 focus:ring-black cursor-pointer"
                                               onchange="calculateTotal()">
                                        <label for="use_points" class="text-[11px] tracking-wide text-stone-700 cursor-pointer select-none">
                                            Apply loyalty points for this purchase
                                        </label>
                                    </div>
                                @else
                                    <p class="text-[10px] text-stone-400 italic">No points available for redemption.</p>
                                @endif
                            </div>
                            
                            {{-- Voucher Code Input Section --}}
                            <div class="mt-6 pt-6 border-t border-stone-100 space-y-4">
                                <h4 class="text-[10px] tracking-[0.2em] font-serif uppercase text-stone-900 font-bold">Promo Voucher</h4>
                                <div class="flex gap-2">
                                    <input type="text" id="voucher_code" placeholder="ENTER VOUCHER CODE"
                                           class="w-full border border-stone-200 px-4 py-3 text-[10px] tracking-widest uppercase focus:outline-none focus:border-black transition-all font-mono">
                                    <button type="button" id="apply_voucher_btn" onclick="applyVoucher()" class="bg-stone-900 text-white text-[9px] tracking-[0.2em] px-6 py-3 hover:bg-black transition-all duration-300 uppercase active:scale-95 shrink-0 flex items-center justify-center min-w-[100px]">
                                        <span id="btn_text">Apply</span>
                                        <span id="btn_spinner" class="hidden animate-spin h-3 w-3 border-2 border-white border-t-transparent rounded-full"></span>
                                    </button>
                                </div>
                                <div id="voucher_message" class="text-[10px] tracking-wide mt-2 hidden transition-all duration-300 transform translate-y-[-5px]"></div>
                            </div>
                            
                            {{-- Place Order Form Action --}}
                            <form id="checkout_form" action="{{ route('checkout.process') }}" method="POST" class="mt-8">
                                @csrf
                                <input type="hidden" id="points_input_hidden" name="points_to_redeem" value="0">
                                <input type="hidden" id="voucher_code_hidden" name="applied_voucher_code" value="">
                                
                                {{-- Hidden fields for RajaOngkir shipping details --}}
                                <input type="hidden" id="shipping_address_hidden" name="shipping_address" value="">
                                <input type="hidden" id="shipping_courier_hidden" name="shipping_courier" value="">
                                <input type="hidden" id="shipping_service_hidden" name="shipping_service" value="">
                                <input type="hidden" id="shipping_cost_hidden" name="shipping_cost" value="0">

                                <button type="button" id="checkout_main_btn" onclick="onMainBtnClick()" class="w-full bg-stone-955 text-white text-[11px] tracking-[0.3em] py-5 hover:bg-stone-800 transition-all uppercase shadow-lg shadow-stone-100 active:scale-[0.99] duration-200 font-bold text-center">
                                    Proceed
                                </button>
                            </form>
                        @else
                            {{-- Guest Prompt --}}
                            <div class="mt-8 pt-6 border-t border-stone-100 space-y-4">
                                <p class="text-[11px] text-stone-400 text-center leading-relaxed">
                                    Please log in to apply your loyalty points, utilize promotional vouchers, and proceed to payment.
                                </p>
                                <a href="{{ route('login') }}" class="w-full block text-center bg-stone-955 text-white text-[11px] tracking-[0.3em] py-5 hover:bg-stone-800 transition-all uppercase font-bold text-center">
                                    Sign In to Checkout
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @endif
    </div>
</div>

<style>
    .dynamic-fade-in { animation: smoothFade 0.8s ease forwards; }
    @keyframes smoothFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .hidden { display: none !important; }
    .message-pop { animation: msgPop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    @keyframes msgPop { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Background colors for active/hover */
    .bg-stone-955 {
        background-color: #1c1917; /* stone-900 */
    }
    .bg-stone-955:hover {
        background-color: #0c0a09; /* stone-950 */
    }
</style>

<script>
    function updateQty(key, newQty) {
        if (newQty <= 0) {
            removeItem(key);
            return;
        }
        fetch(`/cart/update/${key}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ quantity: newQty })
        }).then(() => location.reload());
    }

    function updateSize(key, size) {
        fetch(`/cart/update-size/${key}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ new_size: size })
        }).then(() => location.reload());
    }

    function removeItem(key) {
        fetch(`/cart/remove/${key}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => location.reload());
    }

    const baseSubtotal = {{ $subtotal }};
    const userPoints = {{ Auth::check() ? (Auth::user()->loyalty_points ?? 0) : 0 }};
    let activeVoucherDiscount = 0;
    
    // RajaOngkir State Variables
    let currentStep = 1;
    let selectedShippingCost = 0;

    function calculateTotal() {
        const checkbox = document.getElementById('use_points');
        const discountRow = document.getElementById('points_discount_row');
        const discountVal = document.getElementById('points_discount_val');
        const finalTotalVal = document.getElementById('final_total_val');
        const hiddenInput = document.getElementById('points_input_hidden');

        let currentSubtotalAfterVoucher = baseSubtotal - activeVoucherDiscount;
        if (currentSubtotalAfterVoucher < 0) {
            currentSubtotalAfterVoucher = 0;
        }

        let pointsToUse = 0;
        let discountAmount = 0;

        if (checkbox && checkbox.checked) {
            let maxAllowedDiscount = Math.max(0, currentSubtotalAfterVoucher - 1000);
            let maxPointsNeeded = Math.floor(maxAllowedDiscount / 1000);
            pointsToUse = Math.min(userPoints, maxPointsNeeded);
            discountAmount = pointsToUse * 1000;
            
            discountRow.classList.remove('hidden');
            if (hiddenInput) hiddenInput.value = pointsToUse;
        } else {
            discountRow.classList.add('hidden');
            if (hiddenInput) hiddenInput.value = 0;
        }

        let finalTotal = currentSubtotalAfterVoucher - discountAmount + selectedShippingCost;
        if (finalTotal < 1000) {
            finalTotal = 1000;
        }
        
        if (discountVal) discountVal.textContent = discountAmount.toLocaleString('id-ID');
        if (finalTotalVal) finalTotalVal.textContent = 'IDR ' + finalTotal.toLocaleString('id-ID');
    }

    function applyVoucher() {
        const inputField = document.getElementById('voucher_code');
        const btnText = document.getElementById('btn_text');
        const btnSpinner = document.getElementById('btn_spinner');
        const msgBox = document.getElementById('voucher_message');
        
        const voucherRow = document.getElementById('voucher_discount_row');
        const voucherVal = document.getElementById('voucher_discount_val');
        const voucherBadge = document.getElementById('active_voucher_badge');
        const hiddenVoucherInput = document.getElementById('voucher_code_hidden');
        
        const code = inputField.value.trim();
        
        if (!code) {
            msgBox.className = "text-[10px] tracking-wide mt-2 text-red-600 message-pop";
            msgBox.textContent = "Please enter a valid voucher code first.";
            msgBox.classList.remove('hidden');
            return;
        }

        btnText.classList.add('hidden');
        btnSpinner.classList.remove('hidden');
        msgBox.classList.add('hidden');

        // Dynamic extraction of origin to guarantee compatibility across local port/domain forwards (Laravel Herd)
        const applyVoucherUrl = window.location.origin + '/checkout/apply-voucher';

        fetch(applyVoucherUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ voucher_code: code })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            btnText.classList.remove('hidden');
            btnSpinner.classList.add('hidden');
            
            if (data.success) {
                activeVoucherDiscount = data.discount;
                if (hiddenVoucherInput) hiddenVoucherInput.value = code.toUpperCase();
                
                if (voucherBadge) voucherBadge.textContent = code.toUpperCase();
                if (voucherVal) voucherVal.textContent = data.discount.toLocaleString('id-ID');
                if (voucherRow) voucherRow.classList.remove('hidden');
                
                msgBox.className = "text-[10px] tracking-wide mt-2 text-stone-900 font-semibold message-pop";
                msgBox.textContent = data.message;
                msgBox.classList.remove('hidden');
                
                calculateTotal();
            } else {
                activeVoucherDiscount = 0;
                if (hiddenVoucherInput) hiddenVoucherInput.value = "";
                if (voucherRow) voucherRow.classList.add('hidden');
                
                msgBox.className = "text-[10px] tracking-wide mt-2 text-red-600 message-pop";
                msgBox.textContent = data.message;
                msgBox.classList.remove('hidden');
                
                calculateTotal();
            }
        })
        .catch(error => {
            btnText.classList.remove('hidden');
            btnSpinner.classList.add('hidden');
            msgBox.className = "text-[10px] tracking-wide mt-2 text-red-600 message-pop";
            msgBox.textContent = "An error occurred while validating the voucher. Please try again.";
            msgBox.classList.remove('hidden');
            console.error("Voucher AJAX Error:", error);
        });
    }

    // =========================================================================
    // RAJAONGKIR STATE & INTERACTION JS
    // =========================================================================

    function toggleCheckoutStep(step) {
        const step1Div = document.getElementById('checkout_step_1');
        const step2Div = document.getElementById('checkout_step_2');
        const mainBtn = document.getElementById('checkout_main_btn');
        
        if (step === 2) {
            step1Div.classList.add('hidden');
            step2Div.classList.remove('hidden');
            currentStep = 2;
            if (mainBtn) {
                mainBtn.textContent = "Place Order & Pay";
            }
            loadProvinces();
        } else {
            step2Div.classList.add('hidden');
            step1Div.classList.remove('hidden');
            currentStep = 1;
            if (mainBtn) {
                mainBtn.textContent = "Proceed";
            }
        }
    }

    function onMainBtnClick() {
        if (currentStep === 1) {
            toggleCheckoutStep(2);
        } else {
            // We are on step 2, validate and submit the form
            const province = document.getElementById('select_province').value;
            const city = document.getElementById('select_city').value;
            const courier = document.getElementById('select_courier').value;
            const service = document.getElementById('select_service').value;
            const address = document.getElementById('input_address').value.trim();
            
            if (!province || !city || !courier || !service || !address) {
                alert("Please fill in your complete shipping address and select a courier service.");
                return;
            }
            
            // Populating the hidden checkout form values
            const provText = document.getElementById('select_province').options[document.getElementById('select_province').selectedIndex].text;
            const cityText = document.getElementById('select_city').options[document.getElementById('select_city').selectedIndex].text;
            
            document.getElementById('shipping_address_hidden').value = address + ", " + cityText + ", " + provText;
            document.getElementById('shipping_courier_hidden').value = courier.toUpperCase();
            document.getElementById('shipping_service_hidden').value = service;
            document.getElementById('shipping_cost_hidden').value = selectedShippingCost;
            
            // Trigger actual form submission!
            document.getElementById('checkout_form').submit();
        }
    }

    function loadProvinces() {
        const provSelect = document.getElementById('select_province');
        if (provSelect.options.length > 1) return; // already loaded
        
        fetch('/shipping/provinces')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    data.data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.province_id;
                        opt.textContent = p.province;
                        provSelect.appendChild(opt);
                    });
                }
            });
    }

    function onProvinceChange(provinceId) {
        const citySelect = document.getElementById('select_city');
        const courierSelect = document.getElementById('select_courier');
        const serviceSelect = document.getElementById('select_service');
        
        citySelect.innerHTML = '<option value="">Select City / Kabupaten</option>';
        citySelect.disabled = true;
        
        courierSelect.value = "";
        courierSelect.disabled = true;
        
        serviceSelect.innerHTML = '<option value="">Select Courier First</option>';
        serviceSelect.disabled = true;
        
        if (!provinceId) return;

        fetch(`/shipping/cities/${provinceId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    data.data.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.city_id;
                        opt.textContent = (c.type ? c.type + " " : "") + c.city_name;
                        citySelect.appendChild(opt);
                    });
                    citySelect.disabled = false;
                }
            });
    }

    function onCityChange() {
        const courierSelect = document.getElementById('select_courier');
        const serviceSelect = document.getElementById('select_service');
        
        courierSelect.value = "";
        courierSelect.disabled = false;
        
        serviceSelect.innerHTML = '<option value="">Select Courier First</option>';
        serviceSelect.disabled = true;
    }

    function fetchShippingCost() {
        const cityId = document.getElementById('select_city').value;
        const courier = document.getElementById('select_courier').value;
        const serviceSelect = document.getElementById('select_service');
        const loading = document.getElementById('shipping_loading');
        
        serviceSelect.innerHTML = '<option value="">Select Service</option>';
        serviceSelect.disabled = true;
        
        if (!cityId || !courier) return;
        
        loading.classList.remove('hidden');
        
        fetch('/shipping/cost', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                destination_city_id: cityId,
                courier: courier
            })
        })
        .then(res => res.json())
        .then(data => {
            loading.classList.add('hidden');
            if (data.success && data.services.length > 0) {
                data.services.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.service;
                    opt.dataset.cost = s.cost;
                    opt.textContent = s.service + " (" + s.description + ") - IDR " + s.cost.toLocaleString('id-ID') + " (" + s.etd + " DAYS)";
                    serviceSelect.appendChild(opt);
                });
                serviceSelect.disabled = false;
            } else {
                alert(data.message || "No shipping services available for the selected destination.");
            }
        })
        .catch(err => {
            loading.classList.add('hidden');
            console.error(err);
            alert("An error occurred while calculating shipping cost. Please try again.");
        });
    }

    function onServiceChange(selectElement) {
        const selectedOpt = selectElement.options[selectElement.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            selectedShippingCost = 0;
        } else {
            selectedShippingCost = parseInt(selectedOpt.dataset.cost) || 0;
        }
        
        // Update summary shipping cost display
        const shippingVal = document.getElementById('summary_shipping_val');
        if (selectedShippingCost > 0) {
            shippingVal.textContent = 'IDR ' + selectedShippingCost.toLocaleString('id-ID');
            shippingVal.classList.remove('text-stone-400', 'uppercase');
            shippingVal.classList.add('font-mono', 'text-stone-850');
        } else {
            shippingVal.textContent = 'TBD';
            shippingVal.classList.remove('font-mono', 'text-stone-850');
            shippingVal.classList.add('text-stone-400', 'uppercase');
        }
        
        // Update Grand Total
        calculateTotal();
    }
</script>
@endsection