@extends('base.base')

@section('content')
<div class="pt-32 pb-24 px-6 lg:px-8 bg-white min-h-screen">
    <div class="max-w-5xl mx-auto">
        {{-- Page Header --}}
        <div class="text-center mb-12">
            <span class="text-[10px] tracking-[0.3em] text-gray-400 uppercase">Shopping</span>
            <h1 class="text-3xl md:text-4xl font-serif tracking-[0.15em] mt-3">YOUR CART</h1>
            <div class="w-12 h-px bg-black mx-auto mt-6"></div>
        </div>

        @if(empty($cart))
            <div class="text-center py-24">
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-sm tracking-widest text-gray-400 uppercase mb-6">Your cart is empty</p>
                <a href="{{ route('collection') }}" class="inline-block border border-black text-xs tracking-[0.2em] px-8 py-4 hover:bg-black hover:text-white transition-colors">
                    BROWSE COLLECTION
                </a>
            </div>
        @else
            @php $subtotal = 0; @endphp
            <div class="space-y-0">
                {{-- Table Header --}}
                <div class="hidden md:grid grid-cols-12 gap-4 pb-4 border-b border-black">
                    <div class="col-span-6 text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium">Product</div>
                    <div class="col-span-2 text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium text-center">Size</div>
                    <div class="col-span-2 text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium text-center">Quantity</div>
                    <div class="col-span-2 text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium text-right">Subtotal</div>
                </div>

                {{-- Cart Items --}}
                @foreach($cart as $cart_key => $item)
                    @php
                        $itemSubtotal = $item['price'] * $item['quantity'];
                        $subtotal += $itemSubtotal;
                        $product = $cartProducts[$cart_key] ?? null;
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 py-8 border-b border-gray-100 items-center">
                        {{-- Product Info (Image + Name) --}}
                        <div class="col-span-6 flex items-center gap-5">
                            <div class="w-24 h-32 bg-gray-100 shrink-0 overflow-hidden">
                                @if($product && $product->image_path)
                                    <img src="{{ $product->image_path }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-sm tracking-wide font-medium">{{ $item['name'] }}</h3>
                                <p class="text-xs text-gray-400 mt-1">IDR {{ number_format($item['price'], 0, ',', '.') }}</p>
                                <form action="{{ route('cart.remove', $cart_key) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="submit" class="text-[10px] tracking-widest text-gray-400 hover:text-red-500 uppercase transition-colors">Remove</button>
                                </form>
                            </div>
                        </div>

                        {{-- Size Selector --}}
                        <div class="col-span-2 flex justify-center">
                            @if($product && $product->variants->count() > 0)
                                <form action="{{ route('cart.update.size', $cart_key) }}" method="POST" class="flex items-center">
                                    @csrf
                                    <select name="new_size" onchange="this.form.submit()" class="border border-gray-200 text-xs tracking-wider pr-8 pl-4 py-2 w-24 focus:outline-none focus:border-black bg-white cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23000000%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.75rem top 50%; background-size: 0.65rem auto;">
                                        @foreach($product->variants as $v)
                                            <option value="{{ $v->size_label }}" {{ ($item['size'] ?? '') == $v->size_label ? 'selected' : '' }} {{ $v->stock <= 0 ? 'disabled' : '' }}>
                                                {{ $v->size_label }}{{ $v->stock <= 0 ? ' (Out)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 tracking-wider">—</span>
                            @endif
                        </div>

                        {{-- Quantity Controls --}}
                        <div class="col-span-2 flex justify-center">
                            <div class="flex items-center border border-gray-200">
                                <form action="{{ route('cart.update', $cart_key) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="quantity" value="{{ max(0, $item['quantity'] - 1) }}">
                                    <button type="submit" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-black hover:bg-gray-50 transition-colors text-lg">−</button>
                                </form>
                                <span class="w-12 h-10 flex items-center justify-center text-sm border-x border-gray-200">{{ $item['quantity'] }}</span>
                                <form action="{{ route('cart.update', $cart_key) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                    <button type="submit" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-black hover:bg-gray-50 transition-colors text-lg">+</button>
                                </form>
                            </div>
                        </div>

                        {{-- Subtotal per Item --}}
                        <div class="col-span-2 text-right">
                            <span class="text-sm font-medium">IDR {{ number_format($itemSubtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Cart Summary & Discounts --}}
            <div class="mt-12 border-t border-black pt-8 grid grid-cols-1 md:grid-cols-2 gap-12">
                
                {{-- Left Side: Vouchers & Points --}}
                <div class="space-y-8">
                    {{-- Voucher Code --}}
                    <div>
                        <label class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium block mb-3">Promo Code</label>
                        <div class="flex gap-2">
                            <input type="text" id="voucher_code_input" placeholder="ENTER CODE" class="w-full border border-gray-200 px-4 py-3 text-xs tracking-widest uppercase focus:outline-none focus:border-black">
                            <button type="button" id="apply_voucher_btn" class="bg-black text-white text-[10px] tracking-widest uppercase px-6 py-3 hover:bg-gray-800 transition-colors">
                                Apply
                            </button>
                        </div>
                    </div>

                    {{-- Loyalty Points --}}
                    @if(auth()->check() && auth()->user()->loyalty_points > 0)
                    <div class="bg-stone-50 p-6 border border-gray-100">
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-[10px] tracking-[0.2em] text-gray-800 uppercase font-bold">Privilege Points</label>
                            <span class="text-[10px] text-gray-500 tracking-wider">Available: {{ number_format(auth()->user()->loyalty_points) }} PTS</span>
                        </div>
                        <div class="flex gap-2">
                            <input type="number" id="points_input" min="0" max="{{ auth()->user()->loyalty_points }}" placeholder="0" class="w-full border border-gray-200 px-4 py-3 text-xs tracking-widest focus:outline-none focus:border-black" oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.min(Math.abs(this.value), {{ auth()->user()->loyalty_points }}) : null">
                            <button type="button" id="apply_points_btn" class="bg-white text-black border border-black text-[10px] tracking-widest uppercase px-6 py-3 hover:bg-black hover:text-white transition-colors">
                                Redeem
                            </button>
                        </div>
                        <span class="text-[9px] text-gray-400 tracking-wider block mt-2">*1 Point = IDR 1.000 discount</span>
                        <span id="points_message" class="text-[10px] tracking-wider block mt-2 hidden"></span>
                    </div>
                    @endif
                </div>

                {{-- Right Side: Totals & Checkout --}}
                <div class="flex flex-col items-end gap-4">
                    <div class="w-full max-w-sm space-y-3 mb-4">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Subtotal</span>
                            <span>IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div id="row_discount_points" class="flex justify-between text-sm text-green-600 hidden">
                            <span>Points Redeemed</span>
                            <span id="display_discount_points">- IDR 0</span>
                        </div>
                    </div>

                    <div class="flex items-baseline gap-6 mb-2">
                        <span class="text-xs tracking-[0.2em] text-gray-400 uppercase">Grand Total</span>
                        <span id="display_grand_total" class="text-3xl font-serif">IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex gap-4 w-full justify-end mt-4">
                        <a href="{{ route('collection') }}" class="text-xs tracking-[0.2em] border border-black px-8 py-4 hover:bg-black hover:text-white transition-colors text-center">
                            CONTINUE SHOPPING
                        </a>
                        
                        {{-- Final Checkout Form --}}
                        <form action="{{ route('checkout') }}" method="POST" id="checkout_form">
                            @csrf
                            {{-- Hidden inputs untuk mengirim data diskon ke Controller --}}
                            <input type="hidden" name="points_to_redeem" id="hidden_points" value="0">
                            <input type="hidden" name="voucher_code" id="hidden_voucher" value="">
                            
                            <button type="submit" class="bg-black text-white text-xs tracking-[0.2em] px-8 py-4 hover:bg-gray-800 transition-colors w-full">
                                CHECKOUT
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Script Kalkulasi Realtime --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const subtotal = {{ $subtotal ?? 0 }}; 
        let pointsDiscount = 0;

        const pointsInput = document.getElementById('points_input');
        const applyPointsBtn = document.getElementById('apply_points_btn');
        const pointsMessage = document.getElementById('points_message');
        const displayDiscountPoints = document.getElementById('display_discount_points');
        const rowDiscountPoints = document.getElementById('row_discount_points');
        const displayGrandTotal = document.getElementById('display_grand_total');
        
        // Target input tersembunyi di form checkout
        const hiddenPoints = document.getElementById('hidden_points');

        if(applyPointsBtn) {
            applyPointsBtn.addEventListener('click', function() {
                let points = parseInt(pointsInput.value) || 0;
                
                if (points <= 0) {
                    pointsDiscount = 0;
                    hiddenPoints.value = 0;
                    rowDiscountPoints.classList.add('hidden');
                    pointsMessage.classList.add('hidden');
                    updateGrandTotal();
                    return;
                }

                // Kalkulasi 1 Poin = Rp 1.000
                pointsDiscount = points * 1000;

                if (pointsDiscount > subtotal) {
                    pointsMessage.textContent = "Discount cannot exceed the subtotal.";
                    pointsMessage.className = "text-[10px] text-red-600 tracking-wider block mt-2";
                    pointsMessage.classList.remove('hidden');
                    return;
                }

                pointsMessage.textContent = `Successfully applied ${points} points (IDR ${pointsDiscount.toLocaleString('id-ID')})`;
                pointsMessage.className = "text-[10px] text-green-600 tracking-wider block mt-2";
                pointsMessage.classList.remove('hidden');

                displayDiscountPoints.textContent = `- IDR ${pointsDiscount.toLocaleString('id-ID')}`;
                rowDiscountPoints.classList.remove('hidden');
                
                // Set value ke hidden input agar dikirim ke backend saat submit
                hiddenPoints.value = points;

                updateGrandTotal();
            });
        }

        function updateGrandTotal() {
            let finalTotal = subtotal - pointsDiscount;
            if (finalTotal < 0) finalTotal = 0;
            displayGrandTotal.textContent = `IDR ${finalTotal.toLocaleString('id-ID')}`;
        }
    });
</script>
@endsection