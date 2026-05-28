@component('mail::message')
# Order Awaiting Payment

Dear {{ $order->customer_name }},

Thank you for choosing VESTA. Your order **#{{ $order->invoice_number }}** has been successfully logged into our concierge queue system. 

Please complete your secure payment within **24 hours** to prevent your luxury item allocation from expiring and returning to our main inventory.

### Order Summary:
* **Total Payment:** IDR {{ number_format($order->total_price, 0, ',', '.') }}
* **Delivery Destination:** {{ $order->shipping_address }}
* **Shipping Courier:** {{ strtoupper($order->shipping_courier) }} ({{ $order->shipping_service }})

@component('mail::button', ['url' => route('profile')])
Complete Secure Payment
@endcomponent

If you require any tailored assistance regarding your transaction, please connect instantly via our concierge desk.

Regards,<br>
**VESTA Concierge Service**
@endcomponent