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
            @php $total = 0; @endphp
            <div class="space-y-0">
                {{-- Table Header --}}
                <div class="hidden md:grid grid-cols-12 gap-4 pb-4 border-b border-black">
                    <div class="col-span-6 text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium">Product</div>
                    <div class="col-span-2 text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium text-center">Size</div>
                    <div class="col-span-2 text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium text-center">Quantity</div>
                    <div class="col-span-2 text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium text-right">Subtotal</div>
                </div>

                @foreach($cart as $cart_key => $item)
                    @php
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                        $product = $cartProducts[$cart_key] ?? null;
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 py-8 border-b border-gray-100 items-center">
                        {{-- Product Info (Image + Name) --}}
                        <div class="col-span-6 flex items-center gap-5">
                            {{-- Product Image --}}
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
                                {{-- Mobile: Remove button --}}
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

                        {{-- Subtotal --}}
                        <div class="col-span-2 text-right">
                            <span class="text-sm font-medium">IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Cart Summary --}}
            <div class="mt-12 border-t border-black pt-8">
                <div class="flex flex-col items-end gap-6">
                    <div class="flex items-baseline gap-6">
                        <span class="text-xs tracking-[0.2em] text-gray-400 uppercase">Total</span>
                        <span class="text-2xl font-serif">IDR {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('collection') }}" class="text-xs tracking-[0.2em] border border-black px-8 py-4 hover:bg-black hover:text-white transition-colors">
                            CONTINUE SHOPPING
                        </a>
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-black text-white text-xs tracking-[0.2em] px-8 py-4 hover:bg-gray-800 transition-colors">
                                CHECKOUT
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection