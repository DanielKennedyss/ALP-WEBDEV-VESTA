@extends('base.base')

@section('content')
<style>
    .vesta-toast {
        position: fixed; top: 2rem; right: 2rem; z-index: 200;
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

<div class="min-h-screen flex items-center justify-center px-6">
    <div class="max-w-lg w-full text-center">
        {{-- Icon --}}
        <div class="mb-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
        </div>

        {{-- Title --}}
        <span class="text-[10px] tracking-[0.3em] text-gray-400 uppercase">VESTA Payment</span>
        <h2 class="text-3xl md:text-4xl font-serif tracking-[0.1em] mt-4 mb-6">Complete Your Order</h2>
        <div class="w-16 h-px bg-black mx-auto mb-8"></div>

        {{-- Order Info --}}
        <div class="bg-stone-50 border border-gray-100 py-6 px-8 mb-8">
            <p class="text-[10px] tracking-[0.2em] text-gray-400 uppercase mb-2">Invoice</p>
            <p class="text-lg font-serif tracking-wide mb-4">{{ $order->invoice_number }}</p>
            <p class="text-[10px] tracking-[0.2em] text-gray-400 uppercase mb-2">Total Amount</p>
            <p class="text-2xl font-light">IDR {{ number_format($order->total_price, 0, ',', '.') }}</p>
        </div>

        {{-- Status Badge --}}
        <div id="statusBadge" class="mb-8">
            <span class="inline-block border border-yellow-200 bg-yellow-50 text-yellow-700 px-4 py-2 text-[10px] tracking-[0.2em] uppercase">Status: Pending</span>
        </div>

        {{-- Instructions --}}
        <p class="text-gray-500 text-sm leading-relaxed mb-8">
            Click the button below to complete your payment securely via Midtrans.
        </p>

        {{-- Pay Button --}}
        <button id="pay-button" class="w-full bg-black text-white text-xs tracking-[0.3em] py-5 hover:bg-gray-800 transition-colors duration-300">
            PROCEED TO PAYMENT
        </button>

        {{-- Back to Profile --}}
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
        <p class="text-[10px] text-gray-300 mt-8 tracking-wider">
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
        badge.innerHTML = '<span class="inline-block border border-red-200 bg-red-50 text-red-700 px-4 py-2 text-[10px] tracking-[0.2em] uppercase">Status: Failed</span>';
        btn.textContent = 'RETRY PAYMENT';
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        badge.innerHTML = '<span class="inline-block border border-yellow-200 bg-yellow-50 text-yellow-700 px-4 py-2 text-[10px] tracking-[0.2em] uppercase">Status: Pending</span>';
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
            setTimeout(function() {
                window.location.href = "{{ route('payment_status', $order->id) }}";
            }, 3000);
        },
        onPending: function(result){
            showToast('Payment is pending. Please complete it.', 'pending');
            updateStatus('pending');
        },
        onError: function(result){
            showToast('Payment failed. Please try again or use a different method.', 'error');
            updateStatus('failed');
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