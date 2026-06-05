<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>We Received Your Inquiry - VESTA</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
        }
        p {
            font-size: 15px;
            color: #334155;
            margin-bottom: 16px;
        }
        .badge {
            display: inline-block;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            margin: 20px 0;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>VESTA</h1>
        </div>
        <div class="content">
            <div class="greeting">Hi {{ $customerName }},</div>
            <p>Thank you for contacting us! We have successfully received your inquiry and our team is currently reviewing it.</p>
            <p>We aim to respond to all inquiries within 24 to 48 business hours. In the meantime, feel free to browse our latest collection on our website.</p>
            
            <div style="text-align: center;">
                <a href="{{ url('/') }}" style="display: inline-block; background-color: #0f172a; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; margin: 15px 0;">Visit VESTA Store</a>
            </div>

            <p style="margin-top: 24px;">Best regards,<br><strong>VESTA Team</strong></p>
        </div>
        <div class="footer">
            <p>This is an automated reply. Please do not reply directly to this email.</p>
            <p>&copy; {{ date('Y') }} VESTA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
