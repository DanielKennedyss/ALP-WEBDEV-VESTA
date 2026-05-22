@extends('base.base')

@section('content')
{{-- Animasi Fade In Smooth --}}
<div class="min-h-screen px-6 py-32 bg-stone-50/30 animate-fade-in">
    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-12">
        
        {{-- Sisi Kiri: Loyalty & Promo --}}
        <div class="md:col-span-7 space-y-8">
            <div class="border-b border-stone-200 pb-8">
                <span class="text-[10px] tracking-[0.3em] text-stone-400 uppercase">Step 1 of 2</span>
                <h2 class="text-3xl font-serif tracking-wide mt-2 text-stone-900">Checkout Details</h2>
            </div>

            {{-- Loyalty Rewards Section --}}
            <div class="bg-white border border-stone-200 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs tracking-[0.2em] font-serif uppercase text-stone-900">VESTA Loyalty Rewards</h3>
                    <span class="text-[10px] bg-stone-100 px-3 py-1 text-stone-500 rounded-full font-medium">
                        {{ number_format(auth()->user()->loyalty_points ?? 0, 0) }} PTS
                    </span>
                </div>
                
                @if(auth()->user()->loyalty_points > 0)
                <div class="flex items-center gap-4 bg-stone-50 p-5 border border-stone-100 transition-all duration-300 hover:border-black">
                    <input type="checkbox" id="use_points" name="use_points" value="1" 
                           class="w-5 h-5 text-black border-stone-300 focus:ring-black cursor-pointer"
                           onchange="calculateTotal()">
                    <label for="use_points" class="text-xs tracking-wide text-stone-700 cursor-pointer select-none">
                        Apply my points for this transaction
                    </label>
                </div>
                @else
                <p class="text-[11px] text-stone-400 italic">No points available for redemption.</p>
                @endif
            </div>

            {{-- Voucher Section --}}
            <div class="bg-white border border-stone-200 p-8 shadow-sm">
                <h3 class="text-xs tracking-[0.2em] font-serif uppercase mb-6 text-stone-900">Promo Voucher</h3>
                <div class="flex gap-2">
                    <input type="text" id="voucher_code" placeholder="ENTER VOUCHER CODE"
                           class="w-full border border-stone-200 px-5 py-4 text-[11px] tracking-widest uppercase focus:outline-none focus:border-black transition-all">
                    <button type="button" class="bg-stone-900 text-white text-[10px] tracking-[0.2em] px-8 py-4 hover:bg-black transition-all uppercase">
                        Apply
                    </button>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Order Summary --}}
        <div class="md:col-span-5 bg-white border border-stone-200 p-8 h-fit shadow-sm">
            <h3 class="text-xs tracking-[0.2em] font-serif uppercase mb-8 text-stone-900">Order Summary</h3>
            
            <div class="divide-y divide-stone-100 mb-8">
                @foreach($cartItems as $item)
                <div class="flex items-center gap-5 py-5 text-xs">
                    <div class="w-16 h-20 bg-stone-100 shrink-0">
                        <img src="{{ $item['image_path'] }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-stone-800">{{ $item['name'] }}</p>
                        <p class="text-[10px] text-stone-400 mt-1">Qty: {{ $item['quantity'] }}</p>
                    </div>
                    <span class="font-medium text-stone-700">IDR {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>

            <div class="space-y-4 text-xs border-t border-stone-100 pt-6">
                <div class="flex justify-between text-stone-500">
                    <span>Subtotal</span>
                    <span>IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div id="points_discount_row" class="flex justify-between text-emerald-600 font-medium hidden">
                    <span>Points Discount</span>
                    <span>-IDR <span id="points_discount_val">0</span></span>
                </div>
                <div class="flex justify-between font-serif text-lg font-medium pt-4 text-stone-900">
                    <span>Total</span>
                    <span id="final_total_val">IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
            </div>

            <form action="{{ route('checkout.process') }}" method="POST" class="mt-8">
                @csrf
                <input type="hidden" id="points_input_hidden" name="points_to_redeem" value="0">
                <button type="submit" class="w-full bg-black text-white text-[11px] tracking-[0.3em] py-5 hover:bg-stone-800 transition-all uppercase shadow-lg shadow-stone-200">
                    Place Order & Pay
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .hidden { display: none; }
</style>

<script>
    const baseSubtotal = {{ $subtotal }};
    const userPoints = {{ auth()->user()->loyalty_points ?? 0 }};

    function calculateTotal() {
        const checkbox = document.getElementById('use_points');
        const discountRow = document.getElementById('points_discount_row');
        const discountVal = document.getElementById('points_discount_val');
        const finalTotalVal = document.getElementById('final_total_val');
        const hiddenInput = document.getElementById('points_input_hidden');

        let pointsToUse = 0;
        let discountAmount = 0;

        if (checkbox && checkbox.checked) {
            let maxAllowedDiscount = Math.max(0, baseSubtotal - 1000);
            let maxPointsNeeded = Math.floor(maxAllowedDiscount / 1000);
            pointsToUse = Math.min(userPoints, maxPointsNeeded);
            discountAmount = pointsToUse * 1000;
            
            discountRow.classList.remove('hidden');
            hiddenInput.value = pointsToUse;
        } else {
            discountRow.classList.add('hidden');
            hiddenInput.value = 0;
        }

        let finalTotal = baseSubtotal - discountAmount;
        discountVal.textContent = discountAmount.toLocaleString('id-ID');
        finalTotalVal.textContent = 'IDR ' + finalTotal.toLocaleString('id-ID');
    }
</script>
@endsection