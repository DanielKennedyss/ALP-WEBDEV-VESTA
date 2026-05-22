@extends('base.base')

@section('content')
<div class="pt-32 pb-24 px-6 lg:px-8 bg-white min-h-screen dynamic-fade-in">
    <div class="max-w-5xl mx-auto">
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
            <form action="{{ route('checkout.view') }}" method="GET" id="cartForm">
                <div class="space-y-0 border-t border-stone-900">
                    @foreach($cart as $cart_key => $item)
                        @php $product = $cartProducts[$cart_key] ?? null; @endphp
                        <div class="grid grid-cols-12 gap-4 py-8 border-b border-stone-100 items-center cart-item transition-all hover:bg-stone-50/50" 
                             data-key="{{ $cart_key }}" data-price="{{ $item['price'] }}" data-qty="{{ $item['quantity'] }}">
                            
                            <div class="col-span-1">
                                <input type="checkbox" name="selected_items[]" value="{{ $cart_key }}" 
                                       class="item-checkbox w-4 h-4 text-stone-900 border-stone-300 focus:ring-0 cursor-pointer" 
                                       onchange="updateTotal()">
                            </div>

                            <div class="col-span-5 flex items-center gap-6">
                                <img src="{{ $item['image_path'] }}" class="w-16 h-20 object-cover bg-stone-50 shadow-sm">
                                <div>
                                    <h3 class="text-xs font-medium text-stone-800 tracking-wide">{{ $item['name'] }}</h3>
                                    <p class="text-[10px] text-stone-400 mt-1">IDR {{ number_format($item['price'], 0) }}</p>
                                    <button type="button" onclick="removeItem('{{ $cart_key }}')" class="text-[9px] tracking-[0.2em] text-stone-400 hover:text-red-700 uppercase mt-2 transition-all">Remove</button>
                                </div>
                            </div>

                            {{-- Enhanced Luxury Dropdown --}}
<div class="col-span-2 flex justify-center">
    <div class="relative w-28 group">
        <select onchange="updateSize('{{ $cart_key }}', this.value)" 
                class="w-full text-[10px] tracking-[0.1em] uppercase bg-transparent border-b border-stone-200 py-2 focus:border-stone-900 focus:outline-none transition-all duration-300 cursor-pointer appearance-none text-stone-700 hover:text-stone-900">
            @foreach($product->variants as $v)
                <option value="{{ $v->size_label }}" {{ $item['size'] == $v->size_label ? 'selected' : '' }}>
                    SIZE {{ $v->size_label }}
                </option>
            @endforeach
        </select>
        {{-- Custom Elegant Arrow Icon --}}
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-stone-400 group-hover:text-stone-900 transition-colors">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
</div>

                            <div class="col-span-2 flex justify-center">
                                <div class="flex items-center border border-stone-200 bg-white">
                                    <button type="button" onclick="updateQty('{{ $cart_key }}', -1)" class="w-8 h-8 hover:bg-stone-50 transition-colors">−</button>
                                    <span class="w-8 text-center text-xs">{{ $item['quantity'] }}</span>
                                    <button type="button" onclick="updateQty('{{ $cart_key }}', 1)" class="w-8 h-8 hover:bg-stone-50 transition-colors">+</button>
                                </div>
                            </div>

                            <div class="col-span-2 text-right text-xs font-serif text-stone-800">
                                IDR {{ number_format($item['price'] * $item['quantity'], 0) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-16 border-t border-stone-900 pt-8 flex justify-end">
                    <div class="flex flex-col items-end gap-5">
                        <div class="flex items-baseline gap-6">
                            <span class="text-[11px] tracking-[0.2em] text-stone-400 uppercase">Selected Total</span>
                            <span id="grand_total" class="text-2xl font-serif text-stone-900">IDR 0</span>
                        </div>
                        <button type="submit" id="checkoutBtn" disabled class="bg-stone-900 text-white text-[10px] tracking-[0.2em] px-10 py-4 opacity-50 cursor-not-allowed uppercase transition-all duration-300 hover:bg-stone-800">
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

<style>
    .dynamic-fade-in { animation: smoothFade 0.8s ease forwards; }
    @keyframes smoothFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
    function updateQty(key, delta) {
        fetch(`/cart/update/${key}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ quantity: delta })
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

    function updateTotal() {
        let total = 0;
        let checkedCount = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
            if(item.querySelector('.item-checkbox').checked) {
                total += (parseFloat(item.dataset.price) * parseFloat(item.dataset.qty));
                checkedCount++;
            }
        });
        document.getElementById('grand_total').textContent = 'IDR ' + total.toLocaleString('id-ID');
        document.getElementById('checkoutBtn').disabled = checkedCount === 0;
        document.getElementById('checkoutBtn').style.opacity = checkedCount === 0 ? '0.5' : '1';
    }
</script>
@endsection