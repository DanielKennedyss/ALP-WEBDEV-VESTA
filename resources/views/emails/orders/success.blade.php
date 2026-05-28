@component('mail::message')
# Payment Confirmed

Dear {{ $order->customer_name }},

We are pleased to inform you that your payment for Invoice **#{{ $order->invoice_number }}** has been securely verified and processed.

Our fulfillment team is currently preparing your exquisite package with strict quality control. You will receive another notification email containing the tracking gateway once the parcel is handed over to the courier dispatch.

### Transaction Details:
* **Amount Paid:** IDR {{ number_format($order->total_price, 0, ',', '.') }}
* **Payment Timestamp:** {{ $order->paid_at ?? now()->format('Y-m-d H:i:s') }}
* **Fulfillment Status:** PREPARING / PACKING

@component('mail::button', ['url' => route('profile')])
Track My Wardrobe
@endcomponent

Thank you for your patronage. Your discerning style is our pride.

Regards,<br>
**VESTA Atelier**

@component('mail::subcopy')
This is an automated formal digital receipt. You do not need to reply directly to this email gateway.
@endcomponent
@endcomponent