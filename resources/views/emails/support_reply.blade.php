<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>VESTA Concierge Response</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, Arial, sans-serif;
            background-color: #ffffff;
            color: #111111;
            margin: 0;
            padding: 40px 20px;
        }
        .email-wrapper {
            max-width: 560px;
            margin: 0 auto;
        }
        .brand-header {
            font-family: Georgia, serif;
            font-size: 24px;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            text-align: center;
            border-bottom: 1px solid #e9e9e6;
            padding-bottom: 25px;
            margin-bottom: 40px;
        }
        .salutation {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #111111;
        }
        .message-body {
            font-size: 14px;
            line-height: 1.7;
            color: #444440;
            white-space: pre-line;
            margin-bottom: 40px;
        }
        .footer-signature {
            border-top: 1px solid #e9e9e6;
            padding-top: 25px;
            font-size: 11px;
            letter-spacing: 0.05em;
            color: #888883;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <div class="email-wrapper">
        <div class="brand-header">
            VESTA
        </div>

        <div class="salutation">
            Dear {{ $customerName }},
        </div>

        <div class="message-body">
            {{ $replyMessage }}
        </div>

        <div class="footer-signature">
            <strong style="color: #111111; letter-spacing: 0.1em; text-transform: uppercase; display: block; margin-bottom: 4px;">VESTA Concierge Service</strong>
            Surabaya, Indonesia<br>
            <span style="font-family: monospace; font-size: 10px;">Ticket Reference: {{ strtoupper($originalSubject) }}</span>
        </div>
    </div>

</body>
</html> 