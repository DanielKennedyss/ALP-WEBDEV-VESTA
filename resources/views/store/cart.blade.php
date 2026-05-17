@extends('base.base')

@section('content')
<div class="container mx-auto px-6 py-32">
    <h1 class="text-3xl font-serif tracking-[0.1em] mb-12 text-center">YOUR CART</h1>

    @if(empty($cart))
        <div class="text-center py-20">
            <p class="text-gray-500 mb-8">Your cart is empty</p>
            <a href="/" class="text-xs tracking-[0.2em] border border-current px-8 py-4 hover:bg-black hover:text-white transition-colors">
                CONTINUE SHOPPING
            </a>
        </div>
    @else
        <div class="max-w-4xl mx-auto">
            @php $total = 0; @endphp
            @foreach($cart as $product_id => $item)
                @php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; @endphp
                <div class="flex items-center justify-between py-8 border-b border-gray-100">
                    <div>
                        <h3 class="text-sm tracking-wide">{{ $item['name'] }}</h3>
                        <p class="text-gray-500 text-sm mt-1">IDR {{ number_format($item['price'], 0, ',', '.') }} × {{ $item['quantity'] }}</p>
                    </div>
                    <div class="flex items-center gap-8">
                        <span class="text-sm">IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                        <form action="{{ route('cart.remove', $product_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-black text-sm">REMOVE</button>
                        </form>
                    </div>
                </div>
            @endforeach

            <div class="py-8 border-t border-gray-100">
                <div class="flex justify-between items-center">
                    <span class="text-xs tracking-[0.2em]">TOTAL</span>
                    <span class="text-xl font-serif">IDR {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="/" class="text-xs tracking-[0.2em] border border-current px-8 py-4 hover:bg-black hover:text-white transition-colors">
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
    @endif
</div>
@endsection