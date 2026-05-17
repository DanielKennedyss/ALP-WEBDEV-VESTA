@extends('base.base')

@section('content')
<div class="min-h-screen flex items-center justify-center px-6">
    <div class="max-w-lg w-full text-center">
        <!-- Icon -->
        <div class="mb-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
        </div>

        <!-- Title -->
        <span class="text-xs tracking-[0.3em] text-gray-400 uppercase">VESTA Payment</span>
        <h2 class="text-3xl md:text-4xl font-serif tracking-[0.1em] mt-4 mb-6">Complete Your Order</h2>
        <div class="w-16 h-px bg-black mx-auto mb-8"></div>

        <!-- Order Info -->
        <div class="bg-gray-50 border border-gray-100 py-6 px-8 mb-10">
            <p class="text-xs tracking-[0.2em] text-gray-500 uppercase mb-2">Invoice</p>
            <p class="text-lg font-serif tracking-wide mb-4">{{ $order->invoice_number }}</p>
            <p class="text-xs tracking-[0.2em] text-gray-500 uppercase mb-2">Total Amount</p>
            <p class="text-2xl font-light">IDR {{ number_format($order->total_price, 0, ',', '.') }}</p>
        </div>

        <!-- Instructions -->
        <p class="text-gray-500 text-sm leading-relaxed mb-10">
            Click the button below to complete your payment securely via Midtrans. You will be redirected to complete the payment.
        </p>

        <!-- Pay Button -->
        <button id="pay-button" class="w-full bg-black text-white text-xs tracking-[0.3em] py-5 hover:bg-gray-800 transition-colors duration-300">
            PROCEED TO PAYMENT
        </button>

        <!-- Security Note -->
        <p class="text-xs text-gray-400 mt-6">
            Secured by Midtrans. Your payment information is encrypted.
        </p>
    </div>
</div>

<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script type="text/javascript">
  document.getElementById('pay-button').onclick = function(){
    window.snap.pay('{{ $snapToken }}', {
      onSuccess: function(result){
        window.location.href = "{{ route('payment_status', $order->id) }}";
      },
      onPending: function(result){
        window.location.href = "{{ route('payment_status', $order->id) }}";
      },
      onError: function(result){
        alert("Payment failed!");
        window.location.href = "{{ route('payment_status', $order->id) }}";
      },
      onClose: function(){
        alert('You closed the popup without finishing the payment');
        window.location.href = "{{ route('payment_status', $order->id) }}";
      }
    });
  };

  window.onload = function() {
      document.getElementById('pay-button').click();
  };
</script>
@endsection