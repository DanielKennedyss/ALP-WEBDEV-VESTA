@extends('base.base')

@section('content')
<style>
    .vesta-toast {
        position: fixed; top: 6rem; right: 1.5rem; z-index: 200;
        padding: 1.25rem 2rem; min-width: 320px; max-width: 420px;
        border: 1px solid; opacity: 0; transform: translateX(100%);
        animation: toastIn 0.5s ease forwards;
        font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase;
    }
    .vesta-toast.success { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
    .vesta-toast.error { background: #fef2f2; border-color: #fecaca; color: #dc2626; }
    .vesta-toast.pending { background: #fffbeb; border-color: #fde68a; color: #d97706; }
    @keyframes toastIn { to { opacity: 1; transform: translateX(0); } }
    @keyframes toastOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100%); } }
</style>

{{-- Toast Container --}}
<div id="toastContainer"></div>

<div class="min-h-screen flex items-center justify-center px-6 py-32">
    <div class="max-w-2xl w-full">
        {{-- Header --}}
        <div class="text-center mb-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-black mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            <span class="text-[10px] tracking-[0.3em] text-gray-400 uppercase">VESTA Payment</span>
            <h2 class="text-3xl md:text-4xl font-serif tracking-[0.1em] mt-4 mb-6">Complete Your Order</h2>
            <div class="w-16 h-px bg-black mx-auto"></div>
        </div>

        {{-- Order Info --}}
        <div class="bg-stone-50 border border-gray-100 py-6 px-8 mb-6 text-center">
            <p class="text-[10px] tracking-[0.2em] text-gray-400 uppercase mb-2">Invoice</p>
            <p class="text-lg font-serif tracking-wide mb-4">{{ $order->invoice_number }}</p>
            <p class="text-[10px] tracking-[0.2em] text-gray-400 uppercase mb-2">Total Amount</p>
            <p class="text-2xl font-light">IDR {{ number_format($order->total_price, 0, ',', '.') }}</p>
        </div>

        {{-- Order Items Detail --}}
        @if(!empty($cartItems))
        <div class="border border-gray-100 mb-6">
            <div class="px-8 py-4 border-b border-gray-100 bg-stone-50">
                <p class="text-[10px] tracking-[0.2em] text-gray-400 uppercase font-medium">Order Details</p>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($cartItems as $item)
                <div class="flex items-center gap-5 px-8 py-5">
                    {{-- Product Image --}}
                    <div class="w-16 h-20 bg-gray-100 shrink-0 overflow-hidden">
                        @if(!empty($item['image_path']))
                            <img src="{{ $item['image_path'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm tracking-wide font-medium truncate">{{ $item['name'] }}</h4>
                        <div class="flex items-center gap-3 mt-1">
                            @if(!empty($item['size']))
                                <span class="text-[10px] tracking-wider text-gray-400 uppercase">Size: {{ $item['size'] }}</span>
                                <span class="text-gray-200">·</span>
                            @endif
                            <span class="text-[10px] tracking-wider text-gray-400 uppercase">Qty: {{ $item['quantity'] }}</span>
                        </div>
                    </div>

                    {{-- Subtotal --}}
                    <div class="text-right shrink-0">
                        <span class="text-sm font-medium">IDR {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Status Badge --}}
        <div id="statusBadge" class="mb-8 text-center">
            <span class="inline-block border border-yellow-200 bg-yellow-50 text-yellow-700 px-4 py-2 text-[10px] tracking-[0.2em] uppercase">Status: Pending Payment</span>
        </div>

        {{-- Instructions --}}
        <p class="text-gray-500 text-sm leading-relaxed mb-8 text-center">
            Click the button below to complete your payment securely via Midtrans.
        </p>

        {{-- Pay Button --}}
        <button id="pay-button" class="w-full bg-black text-white text-xs tracking-[0.3em] py-5 hover:bg-gray-800 transition-colors duration-300">
            PROCEED TO PAYMENT
        </button>

        {{-- Back Links --}}
        <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('profile') }}" class="text-xs tracking-[0.2em] text-gray-400 hover:text-black transition-colors uppercase">
                ← Back to Profile
            </a>
            <span class="text-gray-300 hidden sm:inline">|</span>
            <a href="{{ route('collection') }}" class="text-xs tracking-[0.2em] text-gray-400 hover:text-black transition-colors uppercase">
                Continue Shopping
            </a>
        </div>

        {{-- Security Note --}}
        <p class="text-[10px] text-gray-300 mt-8 tracking-wider text-center">
            Secured by Midtrans · Your payment information is encrypted
        </p>
    </div>
</div>

<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
function showToast(message, type) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = 'vesta-toast ' + type;
    toast.innerHTML = '<div style="display:flex;align-items:center;gap:0.75rem;"><strong style="font-size:10px;letter-spacing:0.2em;">VESTA</strong><span>' + message + '</span></div>';
    container.appendChild(toast);
    setTimeout(function() {
        toast.style.animation = 'toastOut 0.4s ease forwards';
        setTimeout(function() { toast.remove(); }, 400);
    }, 5000);
}

function updateStatus(status) {
    const badge = document.getElementById('statusBadge');
    const btn = document.getElementById('pay-button');
    if (status === 'success') {
        badge.innerHTML = '<span class="inline-block border border-green-200 bg-green-50 text-green-700 px-4 py-2 text-[10px] tracking-[0.2em] uppercase">Status: Completed</span>';
        btn.textContent = 'PAYMENT COMPLETED';
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    } else if (status === 'failed') {
        badge.innerHTML = '<span class="inline-block border border-red-200 bg-red-50 text-red-700 px-4 py-2 text-[10px] tracking-[0.2em] uppercase">Status: Cancelled / Refunded</span>';
        btn.textContent = 'RETRY PAYMENT';
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        badge.innerHTML = '<span class="inline-block border border-yellow-200 bg-yellow-50 text-yellow-700 px-4 py-2 text-[10px] tracking-[0.2em] uppercase">Status: Pending Payment</span>';
        btn.textContent = 'PROCEED TO PAYMENT';
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

document.getElementById('pay-button').onclick = function(){
    window.snap.pay('{{ $snapToken }}', {
        onSuccess: function(result){
            showToast('Payment successful! Thank you for your purchase.', 'success');
            updateStatus('success');

            // Immediately update order status via AJAX
            fetch("{{ route('payment.callback', $order->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: 'success' })
            }).then(function() {
                setTimeout(function() {
                    window.location.href = "{{ route('profile') }}";
                }, 2000);
            }).catch(function() {
                // Fallback: redirect to payment_status to try API check
                setTimeout(function() {
                    window.location.href = "{{ route('payment_status', $order->id) }}";
                }, 2000);
            });
        },
        onPending: function(result){
            showToast('Payment is pending. Please complete it.', 'pending');
            updateStatus('pending');
        },
        onError: function(result){
            showToast('Payment failed. Please try again or use a different method.', 'error');
            updateStatus('failed');

            // Update status to failed
            fetch("{{ route('payment.callback', $order->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: 'failed' })
            });
        },
        onClose: function(){
            showToast('Payment window closed. You can retry anytime.', 'pending');
            updateStatus('pending');
        }
    });
};

window.onload = function() {
    document.getElementById('pay-button').click();
};
</script>
@endsection