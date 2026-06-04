<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password OTP - VESTA</title>
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
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
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
            letter-spacing: 0.1em;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
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
            color: #475569;
            margin-bottom: 16px;
        }
        .otp-box {
            display: inline-block;
            background: #f1f5f9;
            border: 2px dashed #94a3b8;
            border-radius: 12px;
            padding: 20px 40px;
            margin: 24px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 0.3em;
            color: #0f172a;
            font-family: 'Courier New', Courier, monospace;
        }
        .expiry-note {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 8px;
        }
        .warning {
            background: #fefce8;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            color: #92400e;
            margin-top: 24px;
            text-align: left;
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
            <div class="greeting">Hi {{ $userName }},</div>
            <p>We received a request to reset your password. Use the OTP code below to proceed with resetting your password:</p>
            
            <div class="otp-box">
                <div class="otp-code">{{ $otpCode }}</div>
            </div>
            <div class="expiry-note">This code expires in <strong>10 minutes</strong>.</div>

            <div class="warning">
                ⚠️ If you did not request a password reset, please ignore this email. Your account remains secure.
            </div>
        </div>
        <div class="footer">
            <p>This is an automated email from VESTA. Please do not reply.</p>
            <p>&copy; {{ date('Y') }} VESTA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
