@component('mail::message')
# Your Order Has Been Shipped

Dear {{ $order->customer_name }},

Koleksi pakaian eksklusif Anda dari **VESTA** telah selesai dikemas dan resmi diserahkan ke pihak ekspedisi untuk proses pengiriman menuju destinasi Anda.

Paket Anda dikirim menggunakan layanan **{{ strtoupper($order->shipping_courier) }}** dan status pergerakannya saat ini sudah dapat dipantau langsung melalui dasbor akun Anda.

### Shipment Log:
* **Invoice Reference:** #{{ $order->invoice_number }}
* **Courier Service:** {{ strtoupper($order->shipping_courier) }} - {{ $order->shipping_service }}
* **Fulfillment Status:** DISPATCHED / SHIPPED

@component('mail::button', ['url' => route('profile')])
Track Shipment Status
@endcomponent

Pastikan Anda memeriksa kelengkapan paket serta melakukan video unboxing saat kiriman tiba sebelum menandai pesanan sebagai selesai.

Regards,<br>
**VESTA Logistics Team**
@endcomponent