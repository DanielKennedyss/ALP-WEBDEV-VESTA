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
            <div class="text-center py-24">
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-sm tracking-widest text-gray-400 uppercase mb-6">Your cart is empty</p>
                <a href="{{ route('collections.index') }}" class="inline-block border border-black text-xs tracking-[0.2em] px-8 py-4 hover:bg-black hover:text-white transition-colors">
                    BROWSE COLLECTION
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                
                {{-- Sisi Kiri: List Item Keranjang Belanja --}}
                <div class="lg:col-span-7">

                    
                    {{-- STEP 1: REVIEW ITEMS --}}
                    <div id="checkout_step_1" class="space-y-6 transition-all duration-500">
                        @if(!$isBuyNow)
                            <div class="flex items-center gap-3 pb-4 border-b border-stone-200 mb-2">
                                <input type="checkbox" id="select_all_cart" checked 
                                       class="w-4 h-4 text-black border-stone-300 focus:ring-black cursor-pointer rounded-sm"
                                       onchange="toggleSelectAll(this)">
                                <label for="select_all_cart" class="text-[10px] tracking-widest uppercase font-semibold text-stone-600 cursor-pointer select-none">Select All Items</label>
                            </div>
                        @endif
                        <div class="divide-y divide-stone-100 border-t border-b border-stone-200">
                            @foreach($cart as $cart_key => $item)
                                @php $product = $cartProducts[$cart_key] ?? null; @endphp
                                <div class="flex gap-6 py-6 items-start cart-item" data-key="{{ $cart_key }}" data-price="{{ $item['price'] }}" data-qty="{{ $item['quantity'] }}">
                                    
                                    {{-- Checklist Checkbox --}}
                                    @if(!$isBuyNow)
                                    <div class="flex items-center justify-center pt-20 shrink-0">
                                        <input type="checkbox" name="cart_items_checked[]" value="{{ $cart_key }}" checked 
                                               class="cart-item-checkbox w-4 h-4 text-black border-stone-300 focus:ring-black cursor-pointer rounded-sm"
                                               onchange="onCartCheckboxChange()">
                                    </div>
                                    @endif
                                    
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

                            @if(Auth::check() && Auth::user()->addresses->isNotEmpty())
                                <div class="space-y-2 border-b border-stone-100 pb-6">
                                    <label class="text-[10px] tracking-widest uppercase text-stone-400 font-semibold">Select Saved Address</label>
                                    <div class="relative group">
                                        <select id="select_saved_address" onchange="applySavedAddress(this.value)"
                                                class="w-full font-mono text-[10px] tracking-[0.1em] uppercase bg-transparent bg-none border-b border-stone-200 py-2.5 focus:border-stone-900 focus:outline-none transition-all duration-300 pr-10 text-stone-700 hover:text-stone-900 cursor-pointer appearance-none">
                                            <option value="">-- USE A NEW / OTHER ADDRESS --</option>
                                            @foreach(Auth::user()->addresses()->orderBy('is_default', 'desc')->latest()->get() as $addr)
                                                <option value="{{ json_encode($addr) }}" {{ $addr->is_default ? 'selected' : '' }}>
                                                    {{ $addr->label }} {{ $addr->is_default ? '(DEFAULT)' : '' }} - {{ $addr->city_name }}, {{ $addr->province_name }}
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
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Dropdown Provinsi --}}
                                <div class="space-y-2" id="province_dropdown_container">
                                    <label class="text-[10px] tracking-widest uppercase text-stone-400 font-semibold">Province</label>
                                    <div class="relative group">
                                        <input type="text" id="select_province" onfocus="showProvinceDropdown()" oninput="filterProvinces(this.value)" onchange="onProvinceInput(this.value)" placeholder="TYPE OR SELECT PROVINCE" autocomplete="off"
                                               class="w-full font-mono text-[10px] tracking-[0.1em] uppercase bg-transparent bg-none border-b border-stone-200 py-2.5 focus:border-stone-900 focus:outline-none transition-all duration-300 pr-10 text-stone-800">
                                        {{-- Custom Centered SVG Arrow on the Far Right --}}
                                        <div id="province_arrow" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-stone-400">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                        {{-- Clear Button (x) --}}
                                        <button type="button" id="clear_province_btn" onclick="clearProvinceSelection()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-stone-400 hover:text-black hidden cursor-pointer">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        {{-- Custom Dropdown List Container --}}
                                        <div id="province_list_dropdown" class="absolute z-[150] left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white text-stone-800 border border-stone-200 shadow-lg py-1 text-[10px] uppercase font-mono tracking-wider hidden transition-all duration-100">
                                        </div>
                                    </div>
                                </div>

                                {{-- Dropdown Kota/Kabupaten --}}
                                <div class="space-y-2" id="city_dropdown_container">
                                    <label class="text-[10px] tracking-widest uppercase text-stone-400 font-semibold">City / Kabupaten</label>
                                    <div class="relative group">
                                        <input type="text" id="select_city" disabled onfocus="showCityDropdown()" oninput="filterCities(this.value)" onchange="onCityInput(this.value)" placeholder="SELECT PROVINCE FIRST" autocomplete="off"
                                               class="w-full font-mono text-[10px] tracking-[0.1em] uppercase bg-transparent bg-none border-b border-stone-200 py-2.5 focus:border-stone-900 focus:outline-none transition-all duration-300 pr-10 text-stone-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                        {{-- Custom Centered SVG Arrow on the Far Right --}}
                                        <div id="city_arrow" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-stone-400">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                        {{-- Clear Button (x) --}}
                                        <button type="button" id="clear_city_btn" onclick="clearCitySelection()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-stone-400 hover:text-black hidden cursor-pointer">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        {{-- Custom Dropdown List Container --}}
                                        <div id="city_list_dropdown" class="absolute z-[150] left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white text-stone-800 border border-stone-200 shadow-lg py-1 text-[10px] uppercase font-mono tracking-wider hidden transition-all duration-100">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Dropdown Kurir --}}
                                <div class="space-y-2" id="courier_dropdown_container">
                                    <label class="text-[10px] tracking-widest uppercase text-stone-400 font-semibold">Courier</label>
                                    <div class="relative group">
                                        <input type="text" id="select_courier" readonly disabled onfocus="showCourierDropdown()" placeholder="SELECT CITY FIRST" autocomplete="off"
                                               class="w-full font-mono text-[10px] tracking-[0.1em] uppercase bg-transparent bg-none border-b border-stone-200 py-2.5 focus:border-stone-900 focus:outline-none transition-all duration-300 pr-10 text-stone-800 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                        <input type="hidden" id="select_courier_code" value="">
                                        {{-- Custom Centered SVG Arrow on the Far Right --}}
                                        <div id="courier_arrow" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-stone-400">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                        {{-- Clear Button (x) --}}
                                        <button type="button" id="clear_courier_btn" onclick="clearCourierSelection()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-stone-400 hover:text-black hidden cursor-pointer">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        {{-- Custom Dropdown List Container --}}
                                        <div id="courier_list_dropdown" class="absolute z-[150] left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white text-stone-800 border border-stone-200 shadow-lg py-1 text-[10px] uppercase font-mono tracking-wider hidden transition-all duration-100">
                                        </div>
                                    </div>
                                </div>

                                {{-- Dropdown Layanan --}}
                                <div class="space-y-2" id="service_dropdown_container">
                                    <label class="text-[10px] tracking-widest uppercase text-stone-400 font-semibold">Delivery Service</label>
                                    <div class="relative group">
                                        <input type="text" id="select_service" readonly disabled onfocus="showServiceDropdown()" placeholder="SELECT COURIER FIRST" autocomplete="off"
                                               class="w-full font-mono text-[10px] tracking-[0.1em] uppercase bg-transparent bg-none border-b border-stone-200 py-2.5 focus:border-stone-900 focus:outline-none transition-all duration-300 pr-10 text-stone-800 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                        <input type="hidden" id="select_service_code" value="">
                                        <input type="hidden" id="select_service_cost" value="0">
                                        {{-- Custom Centered SVG Arrow on the Far Right --}}
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-stone-400">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                        {{-- Custom Dropdown List Container --}}
                                        <div id="service_list_dropdown" class="absolute z-[150] left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white text-stone-800 border border-stone-200 shadow-lg py-1 text-[10px] uppercase font-mono tracking-wider hidden transition-all duration-100">
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

                            @if(Auth::check())
                                <div class="pt-4 border-t border-stone-100 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" id="chk_save_address" onchange="toggleSaveAddressLabel(this.checked)"
                                               class="w-4 h-4 text-black border-stone-300 focus:ring-black cursor-pointer rounded-sm">
                                        <label for="chk_save_address" class="text-[10px] tracking-widest uppercase font-semibold text-stone-600 cursor-pointer select-none">
                                            Save this address for future purchases
                                        </label>
                                    </div>
                                    <div id="save_address_label_container" class="space-y-2 hidden">
                                        <label class="text-[10px] tracking-widest uppercase text-stone-400 font-semibold">Address Label (e.g., Home, Office)</label>
                                        <input type="text" id="input_save_address_label" placeholder="E.G. HOME, OFFICE"
                                               class="w-full border border-stone-200 px-4 py-3 text-[10px] tracking-widest uppercase focus:outline-none focus:border-black transition-all font-mono">
                                    </div>
                                </div>
                            @endif
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
                                <span id="summary_items_count" class="font-mono text-stone-800">{{ collect($cart)->sum('quantity') }} items</span>
                            </div>
                            
                            <div class="flex justify-between text-stone-500">
                                <span>Subtotal</span>
                                <span id="summary_subtotal_val" class="font-mono text-stone-800">IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            
                            <div id="summary_shipping_row" class="flex justify-between text-stone-500 hidden">
                                <span>Shipping</span>
                                <span id="summary_shipping_val" class="uppercase tracking-widest text-[9px] font-bold text-stone-400">TBD</span>
                            </div>
                            
                            @php $vatAmount = $subtotal * 0.10; @endphp
                            <div class="flex justify-between text-stone-500 border-b border-stone-100 pb-4">
                                <span>VAT (10%)</span>
                                <span id="summary_vat_val" class="font-mono text-stone-800">IDR {{ number_format($vatAmount, 0, ',', '.') }}</span>
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
                            @php $initialTotal = $subtotal + $vatAmount; @endphp
                            <div class="flex justify-between font-serif text-base font-semibold pt-4 text-stone-900 border-t border-stone-100">
                                <span>Total</span>
                                <span id="final_total_val" class="font-mono text-stone-955 text-lg font-bold">IDR {{ number_format($initialTotal, 0, ',', '.') }}</span>
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
                                <input type="hidden" id="checked_items_hidden" name="checked_items" value="">
                                <input type="hidden" id="points_input_hidden" name="points_to_redeem" value="0">
                                <input type="hidden" id="voucher_code_hidden" name="applied_voucher_code" value="">
                                
                                {{-- Hidden fields for RajaOngkir shipping details --}}
                                <input type="hidden" id="shipping_address_hidden" name="shipping_address" value="">
                                <input type="hidden" id="shipping_courier_hidden" name="shipping_courier" value="">
                                <input type="hidden" id="shipping_service_hidden" name="shipping_service" value="">
                                <input type="hidden" id="shipping_cost_hidden" name="shipping_cost" value="0">

                                {{-- Hidden fields for saved addresses --}}
                                <input type="hidden" id="save_address_hidden" name="save_address" value="0">
                                <input type="hidden" id="save_address_label_hidden" name="save_address_label" value="">
                                <input type="hidden" id="province_name_hidden" name="province_name" value="">
                                <input type="hidden" id="city_name_hidden" name="city_name" value="">
                                <input type="hidden" id="raw_address_hidden" name="raw_address" value="">

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

@if($isBuyNow)
<!-- Exit Confirmation Modal -->
<div id="exitConfirmModal" class="fixed inset-0 z-[200] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white border border-stone-200 max-w-md w-full p-8 shadow-2xl relative dynamic-fade-in">
            <h3 class="text-xs tracking-[0.2em] font-serif uppercase text-stone-900 font-bold mb-4">Discard Buy Now Session?</h3>
            <p class="text-[11px] text-stone-500 leading-relaxed uppercase tracking-wider mb-8">
                Leaving this view will discard your temporary "Buy Now" session, and you will be returned to your standard shopping cart.
            </p>
            <div class="flex gap-4">
                <button type="button" id="btnConfirmLeave" class="flex-1 bg-stone-900 text-white text-[10px] tracking-[0.2em] py-4 hover:bg-black transition-all uppercase font-bold text-center">
                    Confirm / Leave
                </button>
                <button type="button" onclick="closeExitModal()" class="flex-1 border border-stone-200 text-stone-600 text-[10px] tracking-[0.2em] py-4 hover:bg-stone-50 transition-all uppercase font-bold text-center">
                    Cancel / Stay
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    .dynamic-fade-in { animation: smoothFade 0.8s ease forwards; }
    @keyframes smoothFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .hidden { display: none !important; }
    .message-pop { animation: msgPop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    @keyframes msgPop { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Hide native calendar picker indicator in Chrome/Safari/Edge to avoid duplicate dropdown arrows */
    input::-webkit-calendar-picker-indicator {
        display: none !important;
        -webkit-appearance: none !important;
    }
    
    /* Background colors for active/hover */
    .bg-stone-955 {
        background-color: #1c1917; /* stone-900 */
    }
    .bg-stone-955:hover {
        background-color: #0c0a09; /* stone-950 */
    }
    
    /* Enforce monospace font for all select elements and options to align tabular text correctly */
    select, select option {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
    }
</style>

<script>
    let isBuyNowFlowActive = {{ $isBuyNow ? 'true' : 'false' }};
    let isLeavingConfirmed = false;

    function updateQty(key, newQty) {
        if (newQty <= 0) {
            removeItem(key);
            return;
        }
        fetch(`/cart/update/${key}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ quantity: newQty })
        }).then(() => {
            isLeavingConfirmed = true;
            location.reload();
        });
    }

    function updateSize(key, size) {
        fetch(`/cart/update-size/${key}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ new_size: size })
        }).then(() => {
            isLeavingConfirmed = true;
            location.reload();
        });
    }

    function removeItem(key) {
        fetch(`/cart/remove/${key}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => {
            isLeavingConfirmed = true;
            location.reload();
        });
    }

    const userPoints = {{ Auth::check() ? (Auth::user()->loyalty_points ?? 0) : 0 }};
    let activeVoucherType = null;
    let activeVoucherRewardValue = 0;
    
    // RajaOngkir & Shipping State Variables
    let currentStep = 1;
    let selectedShippingCost = 0;
    let allProvinces = [];
    let allCities = [];
    let isManualShipping = false;

    // =========================================================================
    // CHECKLIST & SELECTION JS
    // =========================================================================

    function toggleSelectAll(selectAllCheckbox) {
        const checkboxes = document.querySelectorAll('.cart-item-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = selectAllCheckbox.checked;
        });
        calculateTotal();
    }

    function onCartCheckboxChange() {
        const selectAll = document.getElementById('select_all_cart');
        const checkboxes = document.querySelectorAll('.cart-item-checkbox');
        const checkedCount = document.querySelectorAll('.cart-item-checkbox:checked').length;
        if (selectAll) {
            selectAll.checked = (checkedCount === checkboxes.length);
        }
        calculateTotal();
    }

    function calculateTotal() {
        const checkbox = document.getElementById('use_points');
        const discountRow = document.getElementById('points_discount_row');
        const discountVal = document.getElementById('points_discount_val');
        const finalTotalVal = document.getElementById('final_total_val');
        const hiddenInput = document.getElementById('points_input_hidden');

        // Dynamic subtotal and quantity calculation based on checked items
        let currentSubtotal = 0;
        let checkedItemCount = 0;
        let checkedKeys = [];

        document.querySelectorAll('.cart-item').forEach(item => {
            const cb = item.querySelector('.cart-item-checkbox');
            if (isBuyNowFlowActive || (cb && cb.checked)) {
                const price = parseFloat(item.getAttribute('data-price')) || 0;
                const qty = parseInt(item.getAttribute('data-qty')) || 0;
                currentSubtotal += price * qty;
                checkedItemCount += qty;
                checkedKeys.push(item.getAttribute('data-key'));
            }
        });

        // Update hidden inputs for controller
        const checkedItemsHidden = document.getElementById('checked_items_hidden');
        if (checkedItemsHidden) {
            checkedItemsHidden.value = checkedKeys.join(',');
        }

        // Update items count and subtotal displays
        const itemsCountVal = document.getElementById('summary_items_count');
        if (itemsCountVal) {
            itemsCountVal.textContent = checkedItemCount + ' items';
        }

        const subtotalVal = document.getElementById('summary_subtotal_val');
        if (subtotalVal) {
            subtotalVal.textContent = 'IDR ' + currentSubtotal.toLocaleString('id-ID');
        }

        // Update VAT (10% Included) display
        let vatAmount = Math.round(currentSubtotal * 0.10);
        const vatVal = document.getElementById('summary_vat_val');
        if (vatVal) {
            vatVal.textContent = 'IDR ' + vatAmount.toLocaleString('id-ID');
        }

        // Calculate Voucher discount dynamically
        let activeVoucherDiscount = 0;
        if (activeVoucherType === 'percentage') {
            activeVoucherDiscount = Math.round((currentSubtotal * activeVoucherRewardValue) / 100);
        } else if (activeVoucherType === 'fixed') {
            activeVoucherDiscount = Math.min(activeVoucherRewardValue, currentSubtotal);
        }
        
        // Show/hide voucher row and update value
        const voucherRow = document.getElementById('voucher_discount_row');
        const voucherVal = document.getElementById('voucher_discount_val');
        if (activeVoucherDiscount > 0) {
            if (voucherVal) voucherVal.textContent = activeVoucherDiscount.toLocaleString('id-ID');
            if (voucherRow) voucherRow.classList.remove('hidden');
        } else {
            if (voucherRow) voucherRow.classList.add('hidden');
        }

        let currentSubtotalAfterVoucher = currentSubtotal - activeVoucherDiscount;
        if (currentSubtotalAfterVoucher < 0) {
            currentSubtotalAfterVoucher = 0;
        }

        let pointsToUse = 0;
        let discountAmount = 0;

        if (checkbox && checkbox.checked && checkedItemCount > 0) {
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

        let finalTotal = currentSubtotalAfterVoucher + vatAmount - discountAmount + selectedShippingCost;
        
        if (checkedItemCount === 0) {
            finalTotal = 0;
        } else if (finalTotal < 1000) {
            finalTotal = 1000;
        }
        
        if (discountVal) discountVal.textContent = discountAmount.toLocaleString('id-ID');
        if (finalTotalVal) finalTotalVal.textContent = 'IDR ' + finalTotal.toLocaleString('id-ID');

        // Disable Proceed button if no items are checked
        const mainBtn = document.getElementById('checkout_main_btn');
        if (mainBtn) {
            if (checkedItemCount === 0) {
                mainBtn.disabled = true;
                mainBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                mainBtn.disabled = false;
                mainBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
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
                activeVoucherType = data.type || 'fixed';
                activeVoucherRewardValue = parseFloat(data.reward_value) || 0;
                if (hiddenVoucherInput) hiddenVoucherInput.value = code.toUpperCase();
                
                if (voucherBadge) voucherBadge.textContent = code.toUpperCase();
                if (voucherRow) voucherRow.classList.remove('hidden');
                
                msgBox.className = "text-[10px] tracking-wide mt-2 text-stone-900 font-semibold message-pop";
                msgBox.textContent = data.message;
                msgBox.classList.remove('hidden');
                
                calculateTotal();
            } else {
                activeVoucherType = null;
                activeVoucherRewardValue = 0;
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

    function toggleSaveAddressLabel(checked) {
        const container = document.getElementById('save_address_label_container');
        if (container) {
            if (checked) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    }

    function applySavedAddress(jsonStr) {
        if (!jsonStr) {
            return;
        }
        
        const addr = JSON.parse(jsonStr);
        
        if (allProvinces.length === 0) {
            fetch('/shipping/provinces')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        allProvinces = [...data.data].sort((a, b) => 
                            a.province.localeCompare(b.province)
                        );
                        proceedWithApply(addr);
                    } else {
                        showVestaToast("Failed to load provinces for saved address.", "error");
                    }
                })
                .catch(err => {
                    showVestaToast("Error loading provinces for saved address.", "error");
                });
        } else {
            proceedWithApply(addr);
        }
    }

    function proceedWithApply(addr) {
        const provinceInput = document.getElementById('select_province');
        const cityInput = document.getElementById('select_city');
        const addressTextarea = document.getElementById('input_address');
        
        const matchingProv = allProvinces.find(p => p.province.toUpperCase() === addr.province_name.toUpperCase());
        if (!matchingProv) {
            showVestaToast("Could not match saved address province with shipping system.", "error");
            return;
        }
        
        provinceInput.value = matchingProv.province;
        document.getElementById('clear_province_btn').classList.remove('hidden');
        document.getElementById('province_arrow').classList.add('hidden');
        
        cityInput.value = "";
        cityInput.placeholder = "LOADING CITIES...";
        cityInput.disabled = true;
        
        document.getElementById('select_courier').value = "";
        document.getElementById('select_courier').disabled = true;
        document.getElementById('select_courier_code').value = "";
        document.getElementById('clear_courier_btn').classList.add('hidden');
        document.getElementById('courier_arrow').classList.remove('hidden');
        
        document.getElementById('select_service').value = "";
        document.getElementById('select_service').disabled = true;
        document.getElementById('select_service_code').value = "";
        document.getElementById('select_service_cost').value = "0";
        
        fetch(`/shipping/cities/${matchingProv.province_id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allCities = [...data.data].sort((a, b) => {
                        const aName = (a.type ? a.type + " " : "") + a.city_name;
                        const bName = (b.type ? b.type + " " : "") + b.city_name;
                        return aName.localeCompare(bName);
                    });
                    
                    cityInput.placeholder = "TYPE OR SELECT CITY";
                    cityInput.disabled = false;
                    
                    const matchingCity = allCities.find(c => {
                        const fullName = ((c.type ? c.type + " " : "") + c.city_name).toUpperCase();
                        const savedCity = addr.city_name.toUpperCase();
                        return fullName === savedCity || fullName.includes(savedCity) || savedCity.includes(fullName);
                    });
                    
                    if (matchingCity) {
                        const fullName = (matchingCity.type ? matchingCity.type + " " : "") + matchingCity.city_name;
                        cityInput.value = fullName;
                        document.getElementById('clear_city_btn').classList.remove('hidden');
                        document.getElementById('city_arrow').classList.add('hidden');
                        
                        const courierInput = document.getElementById('select_courier');
                        courierInput.value = "";
                        courierInput.placeholder = "SELECT COURIER";
                        courierInput.disabled = false;
                        
                        addressTextarea.value = addr.full_address;
                    } else {
                        showVestaToast("Could not match saved address city with shipping system. Please select city manually.", "warning");
                        cityInput.value = "";
                        addressTextarea.value = addr.full_address;
                    }
                } else {
                    cityInput.placeholder = "FAILED TO LOAD CITIES";
                    showVestaToast("Failed to load cities for saved address.", "error");
                }
            })
            .catch(err => {
                cityInput.placeholder = "ERROR LOADING CITIES";
                showVestaToast("Error loading cities for saved address.", "error");
            });
    }

    function toggleCheckoutStep(step) {
        const step1Div = document.getElementById('checkout_step_1');
        const step2Div = document.getElementById('checkout_step_2');
        const mainBtn = document.getElementById('checkout_main_btn');
        const shippingRow = document.getElementById('summary_shipping_row');
        
        if (step === 2) {
            step1Div.classList.add('hidden');
            step2Div.classList.remove('hidden');
            currentStep = 2;
            if (mainBtn) {
                mainBtn.textContent = "Place Order & Pay";
            }
            if (shippingRow) {
                shippingRow.classList.remove('hidden');
            }
            loadProvinces();

            // Auto-apply default address if exists and not yet set
            const savedAddressSelect = document.getElementById('select_saved_address');
            if (savedAddressSelect && savedAddressSelect.value && !document.getElementById('select_province').value) {
                applySavedAddress(savedAddressSelect.value);
            }
        } else {
            step2Div.classList.add('hidden');
            step1Div.classList.remove('hidden');
            currentStep = 1;
            if (mainBtn) {
                mainBtn.textContent = "Proceed";
            }
            if (shippingRow) {
                shippingRow.classList.add('hidden');
            }
        }
    }

    function onMainBtnClick() {
        if (currentStep === 1) {
            toggleCheckoutStep(2);
        } else {
            // We are on step 2, validate and submit the form
            const provinceName = document.getElementById('select_province').value.trim();
            const cityName = document.getElementById('select_city').value.trim();
            const courier = document.getElementById('select_courier_code').value;
            const service = document.getElementById('select_service_code').value;
            const address = document.getElementById('input_address').value.trim();
            
            if (!provinceName) {
                showVestaToast("Please select a shipping province.", "error");
                return;
            }
            if (!cityName) {
                showVestaToast("Please select a shipping city / kabupaten.", "error");
                return;
            }
            if (!courier) {
                showVestaToast("Please select a shipping courier.", "error");
                return;
            }
            if (!service) {
                showVestaToast("Please select a delivery service.", "error");
                return;
            }
            if (!address) {
                showVestaToast("Please enter your full street address.", "error");
                return;
            }
            
            // Populating the hidden checkout form values
            document.getElementById('shipping_address_hidden').value = address + ", " + cityName + ", " + provinceName;
            document.getElementById('shipping_courier_hidden').value = courier.toUpperCase();
            document.getElementById('shipping_service_hidden').value = service;
            document.getElementById('shipping_cost_hidden').value = selectedShippingCost;

            // Set the save address flag and label if authenticated and checked
            const chkSaveAddress = document.getElementById('chk_save_address');
            if (chkSaveAddress && chkSaveAddress.checked) {
                document.getElementById('save_address_hidden').value = "1";
                document.getElementById('save_address_label_hidden').value = (document.getElementById('input_save_address_label').value || "").trim();
            } else {
                document.getElementById('save_address_hidden').value = "0";
                document.getElementById('save_address_label_hidden').value = "";
            }
            
            document.getElementById('province_name_hidden').value = provinceName;
            document.getElementById('city_name_hidden').value = cityName;
            document.getElementById('raw_address_hidden').value = address;
            
            // Trigger actual form submission!
            isLeavingConfirmed = true;
            document.getElementById('checkout_form').submit();
        }
    }

    function loadProvinces() {
        if (allProvinces.length > 0) return; // already loaded
        
        fetch('/shipping/provinces')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allProvinces = [...data.data].sort((a, b) => 
                        a.province.localeCompare(b.province)
                    );
                }
            });
    }

    function showProvinceDropdown() {
        const dropdown = document.getElementById('province_list_dropdown');
        dropdown.classList.remove('hidden');
        
        if (allProvinces.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-2.5 text-stone-500 italic animate-pulse">Loading provinces...</div>';
            fetch('/shipping/provinces')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        allProvinces = [...data.data].sort((a, b) => 
                            a.province.localeCompare(b.province)
                        );
                        filterProvinces(document.getElementById('select_province').value);
                    } else {
                        dropdown.innerHTML = '<div class="px-4 py-2.5 text-red-500">Failed to load provinces</div>';
                    }
                })
                .catch(err => {
                    dropdown.innerHTML = '<div class="px-4 py-2.5 text-red-500">Error loading provinces</div>';
                });
        } else {
            filterProvinces(document.getElementById('select_province').value);
        }
    }

    function filterProvinces(query) {
        const dropdown = document.getElementById('province_list_dropdown');
        dropdown.classList.remove('hidden');
        
        const filtered = allProvinces.filter(p => 
            p.province.toUpperCase().includes(query.toUpperCase())
        );
        
        dropdown.innerHTML = "";
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-2.5 text-stone-500 italic font-mono uppercase tracking-wider text-[10px]">No provinces found</div>';
        } else {
            filtered.forEach(p => {
                const div = document.createElement('div');
                div.className = "px-4 py-2.5 hover:bg-stone-100 hover:text-stone-900 cursor-pointer transition-colors border-b border-stone-100 last:border-0 font-mono uppercase tracking-wider text-[10px]";
                div.textContent = p.province;
                div.onmousedown = (e) => {
                    e.preventDefault();
                };
                div.onclick = () => {
                    selectProvince(p.province);
                };
                dropdown.appendChild(div);
            });
        }
    }

    function selectProvince(provinceName) {
        const input = document.getElementById('select_province');
        input.value = provinceName;
        document.getElementById('province_list_dropdown').classList.add('hidden');
        onProvinceInput(provinceName);
    }

    function onProvinceInput(provinceName) {
        const matchingProv = allProvinces.find(p => p.province.toUpperCase() === provinceName.toUpperCase());
        const cityInput = document.getElementById('select_city');
        const courierInput = document.getElementById('select_courier');
        const courierCodeHidden = document.getElementById('select_courier_code');
        const serviceInput = document.getElementById('select_service');
        const serviceCodeHidden = document.getElementById('select_service_code');
        const serviceCostHidden = document.getElementById('select_service_cost');
        
        if (matchingProv) {
            isManualShipping = false;
            cityInput.value = "";
            cityInput.placeholder = "LOADING CITIES...";
            cityInput.disabled = true;
            courierInput.value = "";
            courierInput.placeholder = "SELECT CITY FIRST";
            courierInput.disabled = true;
            courierCodeHidden.value = "";
            serviceInput.value = "";
            serviceInput.placeholder = "SELECT COURIER FIRST";
            serviceInput.disabled = true;
            serviceCodeHidden.value = "";
            serviceCostHidden.value = "0";
            
            loadCities(matchingProv.province_id);
            
            document.getElementById('clear_province_btn').classList.remove('hidden');
            document.getElementById('province_arrow').classList.add('hidden');
            
            document.getElementById('clear_courier_btn').classList.add('hidden');
            document.getElementById('courier_arrow').classList.remove('hidden');
        } else {
            isManualShipping = false;
            cityInput.value = "";
            cityInput.placeholder = "SELECT PROVINCE FIRST";
            cityInput.disabled = true;
            courierInput.value = "";
            courierInput.placeholder = "SELECT CITY FIRST";
            courierInput.disabled = true;
            courierCodeHidden.value = "";
            serviceInput.value = "";
            serviceInput.placeholder = "SELECT COURIER FIRST";
            serviceInput.disabled = true;
            serviceCodeHidden.value = "";
            serviceCostHidden.value = "0";
            
            selectedShippingCost = 0;
            const shippingVal = document.getElementById('summary_shipping_val');
            if (shippingVal) {
                shippingVal.textContent = 'TBD';
                shippingVal.className = 'uppercase tracking-widest text-[9px] font-bold text-stone-400';
            }
            calculateTotal();
            
            document.getElementById('clear_province_btn').classList.add('hidden');
            document.getElementById('province_arrow').classList.remove('hidden');
            
            document.getElementById('clear_courier_btn').classList.add('hidden');
            document.getElementById('courier_arrow').classList.remove('hidden');
        }
    }

    function clearProvinceSelection() {
        const provinceInput = document.getElementById('select_province');
        provinceInput.value = "";
        onProvinceInput("");
    }

    function loadCities(provinceId) {
        allCities = [];
        const cityDropdown = document.getElementById('city_list_dropdown');
        cityDropdown.innerHTML = '<div class="px-4 py-2.5 text-stone-500 italic animate-pulse">Loading cities...</div>';
        
        fetch(`/shipping/cities/${provinceId}`)
            .then(res => res.json())
            .then(data => {
                const cityInput = document.getElementById('select_city');
                if (data.success) {
                    allCities = [...data.data].sort((a, b) => {
                        const aName = (a.type ? a.type + " " : "") + a.city_name;
                        const bName = (b.type ? b.type + " " : "") + b.city_name;
                        return aName.localeCompare(bName);
                    });
                    cityDropdown.innerHTML = "";
                    if (cityInput) {
                        cityInput.placeholder = "TYPE OR SELECT CITY";
                        cityInput.disabled = false;
                    }
                } else {
                    cityDropdown.innerHTML = '<div class="px-4 py-2.5 text-red-500">Failed to load cities</div>';
                    if (cityInput) cityInput.placeholder = "FAILED TO LOAD CITIES";
                }
            })
            .catch(err => {
                cityDropdown.innerHTML = '<div class="px-4 py-2.5 text-red-500">Error loading cities</div>';
            });
    }

    function showCityDropdown() {
        const input = document.getElementById('select_city');
        if (input.disabled) return;
        const dropdown = document.getElementById('city_list_dropdown');
        dropdown.classList.remove('hidden');
        filterCities(input.value);
    }

    function filterCities(query) {
        const dropdown = document.getElementById('city_list_dropdown');
        dropdown.classList.remove('hidden');
        const filtered = allCities.filter(c => {
            const fullName = (c.type ? c.type + " " : "") + c.city_name;
            return fullName.toUpperCase().includes(query.toUpperCase());
        });
        dropdown.innerHTML = "";
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-2.5 text-stone-500 italic font-mono uppercase tracking-wider text-[10px]">No cities found</div>';
        } else {
            filtered.forEach(c => {
                const fullName = (c.type ? c.type + " " : "") + c.city_name;
                const div = document.createElement('div');
                div.className = "px-4 py-2.5 hover:bg-stone-100 hover:text-stone-900 cursor-pointer transition-colors border-b border-stone-100 last:border-0 font-mono uppercase tracking-wider text-[10px]";
                div.textContent = fullName;
                div.onmousedown = (e) => e.preventDefault();
                div.onclick = () => selectCity(fullName);
                dropdown.appendChild(div);
            });
        }
    }

    function selectCity(cityName) {
        const input = document.getElementById('select_city');
        input.value = cityName;
        document.getElementById('city_list_dropdown').classList.add('hidden');
        onCityInput(cityName);
    }

    function onCityInput(cityName) {
        const courierInput = document.getElementById('select_courier');
        const courierCodeHidden = document.getElementById('select_courier_code');
        const serviceInput = document.getElementById('select_service');
        const serviceCodeHidden = document.getElementById('select_service_code');
        const serviceCostHidden = document.getElementById('select_service_cost');
        
        const matchingCity = allCities.find(c => {
            const fullName = (c.type ? c.type + " " : "") + c.city_name;
            return fullName.toUpperCase() === cityName.toUpperCase();
        });
        
        if (matchingCity) {
            courierInput.value = "";
            courierInput.placeholder = "SELECT COURIER";
            courierInput.disabled = false;
            courierCodeHidden.value = "";
            serviceInput.value = "";
            serviceInput.placeholder = "SELECT COURIER FIRST";
            serviceInput.disabled = true;
            serviceCodeHidden.value = "";
            serviceCostHidden.value = "0";
            
            document.getElementById('clear_city_btn').classList.remove('hidden');
            document.getElementById('city_arrow').classList.add('hidden');
            
            document.getElementById('clear_courier_btn').classList.add('hidden');
            document.getElementById('courier_arrow').classList.remove('hidden');
        } else {
            courierInput.value = "";
            courierInput.placeholder = "SELECT CITY FIRST";
            courierInput.disabled = true;
            courierCodeHidden.value = "";
            serviceInput.value = "";
            serviceInput.placeholder = "SELECT COURIER FIRST";
            serviceInput.disabled = true;
            serviceCodeHidden.value = "";
            serviceCostHidden.value = "0";
            
            selectedShippingCost = 0;
            const shippingVal = document.getElementById('summary_shipping_val');
            if (shippingVal) {
                shippingVal.textContent = 'TBD';
                shippingVal.className = 'uppercase tracking-widest text-[9px] font-bold text-stone-400';
            }
            calculateTotal();
            
            document.getElementById('clear_city_btn').classList.add('hidden');
            document.getElementById('city_arrow').classList.remove('hidden');
            
            document.getElementById('clear_courier_btn').classList.add('hidden');
            document.getElementById('courier_arrow').classList.remove('hidden');
        }
    }

    function clearCitySelection() {
        const cityInput = document.getElementById('select_city');
        cityInput.value = "";
        onCityInput("");
    }

    function clearCourierSelection() {
        const courierInput = document.getElementById('select_courier');
        courierInput.value = "";
        onCourierInput("");
    }

    function onCourierInput(courierName) {
        const courierInput = document.getElementById('select_courier');
        const courierCodeHidden = document.getElementById('select_courier_code');
        const serviceInput = document.getElementById('select_service');
        const serviceCodeHidden = document.getElementById('select_service_code');
        const serviceCostHidden = document.getElementById('select_service_cost');
        
        if (courierName) {
            document.getElementById('clear_courier_btn').classList.remove('hidden');
            document.getElementById('courier_arrow').classList.add('hidden');
        } else {
            courierInput.value = "";
            courierCodeHidden.value = "";
            serviceInput.value = "";
            serviceInput.placeholder = "SELECT COURIER FIRST";
            serviceInput.disabled = true;
            serviceCodeHidden.value = "";
            serviceCostHidden.value = "0";
            
            selectedShippingCost = 0;
            const shippingVal = document.getElementById('summary_shipping_val');
            if (shippingVal) {
                shippingVal.textContent = 'TBD';
                shippingVal.className = 'uppercase tracking-widest text-[9px] font-bold text-stone-400';
            }
            calculateTotal();
            
            document.getElementById('clear_courier_btn').classList.add('hidden');
            document.getElementById('courier_arrow').classList.remove('hidden');
        }
    }

    function checkInitialSelection() {
        const provinceVal = document.getElementById('select_province').value;
        const cityVal = document.getElementById('select_city').value;
        const courierVal = document.getElementById('select_courier').value;
        
        if (provinceVal) {
            document.getElementById('clear_province_btn').classList.remove('hidden');
            document.getElementById('province_arrow').classList.add('hidden');
        }
        if (cityVal) {
            document.getElementById('clear_city_btn').classList.remove('hidden');
            document.getElementById('city_arrow').classList.add('hidden');
        }
        if (courierVal) {
            document.getElementById('clear_courier_btn').classList.remove('hidden');
            document.getElementById('courier_arrow').classList.add('hidden');
        }
    }

    function showCourierDropdown() {
        const input = document.getElementById('select_courier');
        if (input.disabled) return;
        
        const dropdown = document.getElementById('courier_list_dropdown');
        dropdown.classList.remove('hidden');
        
        dropdown.innerHTML = "";
        
        if (isManualShipping) {
            const div = document.createElement('div');
            div.className = "px-4 py-2.5 hover:bg-stone-100 hover:text-stone-900 cursor-pointer transition-colors border-b border-stone-100 last:border-0 font-mono uppercase tracking-wider text-[10px]";
            div.textContent = "Standard Delivery";
            div.onmousedown = (e) => e.preventDefault();
            div.onclick = () => selectCourier("manual", "Standard Delivery");
            dropdown.appendChild(div);
        } else {
            const options = [
                { code: "jne", name: "JNE (Jalur Nugraha Ekakurir)" },
                { code: "pos", name: "POS Indonesia" },
                { code: "tiki", name: "TIKI (Titipan Kilat)" }
            ];
            
            options.forEach(opt => {
                const div = document.createElement('div');
                div.className = "px-4 py-2.5 hover:bg-stone-100 hover:text-stone-900 cursor-pointer transition-colors border-b border-stone-100 last:border-0 font-mono uppercase tracking-wider text-[10px]";
                div.textContent = opt.name;
                div.onmousedown = (e) => e.preventDefault();
                div.onclick = () => selectCourier(opt.code, opt.name);
                dropdown.appendChild(div);
            });
        }
    }

    function selectCourier(code, name) {
        const input = document.getElementById('select_courier');
        input.value = name;
        document.getElementById('select_courier_code').value = code;
        document.getElementById('courier_list_dropdown').classList.add('hidden');
        
        document.getElementById('clear_courier_btn').classList.remove('hidden');
        document.getElementById('courier_arrow').classList.add('hidden');
        
        fetchShippingCost();
    }

    function fetchShippingCost() {
        const cityName = document.getElementById('select_city').value.trim();
        const matchingCity = allCities.find(c => {
            const fullName = (c.type ? c.type + " " : "") + c.city_name;
            return fullName.toUpperCase() === cityName.toUpperCase();
        });
        
        const cityId = matchingCity ? matchingCity.city_id : null;
        const postalCode = matchingCity ? (matchingCity.postal_code || "") : "";
        const courier = document.getElementById('select_courier_code').value;
        const serviceInput = document.getElementById('select_service');
        const serviceCodeHidden = document.getElementById('select_service_code');
        const serviceCostHidden = document.getElementById('select_service_cost');
        const loading = document.getElementById('shipping_loading');
        
        serviceInput.value = "";
        serviceInput.placeholder = "SELECT SERVICE";
        serviceInput.disabled = true;
        serviceCodeHidden.value = "";
        serviceCostHidden.value = "0";
        
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
                destination_postal_code: postalCode,
                courier: courier
            })
        })
        .then(res => res.json())
        .then(data => {
            loading.classList.add('hidden');
            if (data.success && data.services.length > 0) {
                let maxLeftLength = 0;
                const processedServices = data.services.map(s => {
                    const leftPart = s.service + " (" + (s.description || "") + ")";
                    if (leftPart.length > maxLeftLength) {
                        maxLeftLength = leftPart.length;
                    }
                    
                    let etdStr = '';
                    if (s.etd) {
                        let cleanEtd = s.etd.toString().toUpperCase().replace(/\b(DAY|DAYS|HARI|HARIS)\b/g, '').trim();
                        if (cleanEtd) {
                            etdStr = " (" + cleanEtd + " DAYS)";
                        }
                    }
                    
                    return {
                        service: s.service,
                        description: s.description || "",
                        cost: s.cost,
                        leftPart: leftPart,
                        etdStr: etdStr
                    };
                });

                const dropdown = document.getElementById('service_list_dropdown');
                dropdown.innerHTML = "";
                
                processedServices.forEach(s => {
                    const div = document.createElement('div');
                    div.className = "px-4 py-2.5 hover:bg-stone-100 hover:text-stone-900 cursor-pointer transition-colors border-b border-stone-100 last:border-0 flex flex-col gap-0.5 font-mono uppercase tracking-wider text-[10px]";
                    
                    const displayText = s.leftPart + " - IDR " + s.cost.toLocaleString('id-ID') + s.etdStr;
                    
                    div.innerHTML = `
                        <div class="font-semibold text-stone-800">${s.leftPart}</div>
                        <div class="text-[9px] text-stone-500">IDR ${s.cost.toLocaleString('id-ID')}${s.etdStr}</div>
                    `;
                    div.onmousedown = (e) => e.preventDefault();
                    div.onclick = () => selectService(s.service, s.cost, displayText);
                    dropdown.appendChild(div);
                });
                serviceInput.disabled = false;
            } else {
                const errMsg = data.message || "No shipping services available for the selected destination.";
                console.warn("Biteship rates calculation failed: " + errMsg);
                showVestaToast("Biteship Rate Calculation: " + errMsg, "error");
            }
        })
        .catch(err => {
            loading.classList.add('hidden');
            console.error(err);
            showVestaToast("An error occurred while calculating shipping cost. Please try again.", "error");
        });
    }

    function showServiceDropdown() {
        const input = document.getElementById('select_service');
        if (input.disabled) return;
        
        const dropdown = document.getElementById('service_list_dropdown');
        dropdown.classList.remove('hidden');
    }

    function selectService(code, cost, name) {
        const input = document.getElementById('select_service');
        input.value = name;
        document.getElementById('select_service_code').value = code;
        document.getElementById('select_service_cost').value = cost;
        document.getElementById('service_list_dropdown').classList.add('hidden');
        
        selectedShippingCost = parseInt(cost) || 0;
        
        const shippingVal = document.getElementById('summary_shipping_val');
        if (selectedShippingCost > 0) {
            shippingVal.textContent = 'IDR ' + selectedShippingCost.toLocaleString('id-ID');
            shippingVal.className = 'font-mono text-stone-800';
        } else {
            shippingVal.textContent = 'TBD';
            shippingVal.className = 'uppercase tracking-widest text-[9px] font-bold text-stone-400';
        }
        
        calculateTotal();
    }

    window.addEventListener('DOMContentLoaded', () => {
        onCartCheckboxChange();
        checkInitialSelection();
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#province_dropdown_container')) {
            const dropdown = document.getElementById('province_list_dropdown');
            if (dropdown) dropdown.classList.add('hidden');
        }
        if (!e.target.closest('#city_dropdown_container')) {
            const dropdown = document.getElementById('city_list_dropdown');
            if (dropdown) dropdown.classList.add('hidden');
        }
        if (!e.target.closest('#courier_dropdown_container')) {
            const dropdown = document.getElementById('courier_list_dropdown');
            if (dropdown) dropdown.classList.add('hidden');
        }
        if (!e.target.closest('#service_dropdown_container')) {
            const dropdown = document.getElementById('service_list_dropdown');
            if (dropdown) dropdown.classList.add('hidden');
        }
    });

    @if($isBuyNow)
    let targetUrlToNavigate = null;

    // Open exit confirmation modal
    function openExitModal(targetUrl) {
        targetUrlToNavigate = targetUrl;
        document.getElementById('exitConfirmModal').classList.remove('hidden');
    }

    // Close exit confirmation modal
    function closeExitModal() {
        document.getElementById('exitConfirmModal').classList.add('hidden');
        targetUrlToNavigate = null;
    }

    // Confirm and leave
    document.getElementById('btnConfirmLeave').addEventListener('click', function() {
        isLeavingConfirmed = true;
        
        // Call backend to clear the buy_now session first, then navigate
        const clearUrl = '/cart?cancel_buy_now=1';
        fetch(clearUrl)
            .then(() => {
                if (targetUrlToNavigate) {
                    window.location.href = targetUrlToNavigate;
                } else {
                    window.location.href = '{{ route("cart.view") }}';
                }
            })
            .catch(() => {
                window.location.href = targetUrlToNavigate || '{{ route("cart.view") }}';
            });
    });

    // Intercept clicks on page links
    document.addEventListener('click', function(e) {
        if (isLeavingConfirmed) return;

        // Find closest anchor tag
        const anchor = e.target.closest('a');
        if (!anchor) return;

        const href = anchor.getAttribute('href');
        const target = anchor.getAttribute('target');

        // Ignore hash links, javascript: links, empty links, or open in new tab links
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || target === '_blank') {
            return;
        }

        // Intercept navigation
        e.preventDefault();
        openExitModal(href);
    });

    // Intercept browser back / forward / reload / close tab
    window.addEventListener('beforeunload', function(e) {
        if (isBuyNowFlowActive && !isLeavingConfirmed) {
            e.preventDefault();
            e.returnValue = ''; // standard for showing browser prompt
        }
    });
    @endif
</script>
@endsection