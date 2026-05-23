<!DOCTYPE html>
<html>
<head>
    <title>New Contact Inquiry - VESTA</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 40px; border: 1px solid #dee2e6;">
        <h2 style="font-family: 'Playfair Display', ui-serif, serif; font-weight: 300; font-size: 24px; letter-spacing: 0.1em; color: #000000; text-transform: uppercase; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px;">
            VESTA SUPPORT TICKET
        </h2>
        
        <p style="font-size: 14px; color: #333;">Halo Tim VESTA, Anda menerima pesan bantuan baru dari halaman website.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px;">
            <tr>
                <td style="padding: 8px 0; font-weight: bold; width: 30%; color: #666;">Nama Pengirim:</td>
                <td style="padding: 8px 0; color: #000;">{{ $data['first_name'] }} {{ $data['last_name'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #666;">Email Customer:</td>
                <td style="padding: 8px 0; color: #000;">{{ $data['email'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #666;">Nomor Telepon:</td>
                <td style="padding: 8px 0; color: #000;">{{ $data['phone'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold; color: #666;">Kategori Subjek:</td>
                <td style="padding: 8px 0; color: #d9383a; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">{{ $data['subject'] }}</td>
            </tr>
        </table>

        <div style="background-color: #f8f9fa; border-left: 3px solid #000; padding: 20px; margin-top: 20px;">
            <h4 style="margin: 0 0 10px 0; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #555;">Isi Pesan Detail:</h4>
            <p style="margin: 0; font-size: 14px; color: #333; line-height: 1.6; white-space: pre-line;">{{ $data['message'] }}</p>
        </div>

        <p style="color: #868e96; font-size: 11px; margin-top: 40px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
            Sistem Notifikasi Otomatis VESTA Clothing. Anda dapat langsung membalas email ini untuk merespons customer.
        </p>
    </div>
</body>
</html>