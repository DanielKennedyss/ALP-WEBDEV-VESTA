<!DOCTYPE html>
<html>
<head>
    <title>Kode Verifikasi VESTA</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; margin: 0;">
    <div style="max-width: 500px; margin: 40px auto; background-color: #ffffff; padding: 40px; text-align: center; border: 1px solid #dee2e6;">
        <h1 style="font-family: 'Playfair Display', ui-serif, serif; font-weight: 300; font-size: 32px; letter-spacing: 0.2em; margin-bottom: 30px; color: #000000; text-transform: uppercase;">VESTA</h1>
        
        <div style="height: 1px; background-color: #eee; width: 80%; margin: 0 auto 30px auto;"></div>
        
        <p style="color: #333333; font-size: 14px; line-height: 1.6; letter-spacing: 0.05em;">Gunakan kode OTP di bawah ini untuk melanjutkan proses verifikasi keamanan akun Anda.</p>
        
        <div style="font-size: 36px; font-weight: 700; letter-spacing: 6px; margin: 40px 0; padding: 20px; background: #f8f9fa; border: 1px solid #f1f3f5; color: #000000; display: inline-block; width: 80%; box-sizing: border-box;">
            {{ $otp }}
        </div>
        
        <p style="color: #868e96; font-size: 11px; line-height: 1.5; letter-spacing: 0.05em; margin-top: 30px;">
            Kode ini hanya berlaku selama **15 menit**. Demi keamanan, mohon jangan bagikan atau berikan kode ini kepada pihak mana pun termasuk tim VESTA.
        </p>
        
        <div style="height: 1px; background-color: #eee; width: 40%; margin: 30px auto 0 auto;"></div>
    </div>
</body>
</html>