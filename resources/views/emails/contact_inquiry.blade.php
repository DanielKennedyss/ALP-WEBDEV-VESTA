<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Inquiry - VESTA</title>
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
        }
        .intro {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .details-table th, .details-table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .details-table th {
            width: 30%;
            font-weight: 600;
            color: #64748b;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .details-table td {
            color: #334155;
            font-size: 15px;
        }
        .message-box {
            background-color: #f8fafc;
            border-left: 4px solid #0f172a;
            padding: 16px;
            border-radius: 0 8px 8px 0;
            margin-top: 10px;
            font-style: italic;
            color: #334155;
            white-space: pre-line;
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
            <p class="intro">You have received a new contact inquiry from the website. Details are provided below:</p>
            
            <table class="details-table">
                <tr>
                    <th>Name</th>
                    <td>{{ $data['first_name'] }} {{ $data['last_name'] }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><a href="mailto:{{ $data['email'] }}" style="color: #0f172a; text-decoration: underline;">{{ $data['email'] }}</a></td>
                </tr>
                @if(!empty($data['phone']))
                <tr>
                    <th>Phone</th>
                    <td>{{ $data['phone'] }}</td>
                </tr>
                @endif
                <tr>
                    <th>Subject</th>
                    <td><strong>{{ $data['subject'] }}</strong></td>
                </tr>
            </table>

            <h3 style="margin-bottom: 8px; color: #0f172a;">Message:</h3>
            <div class="message-box">
                {{ $data['message'] }}
            </div>
        </div>
        <div class="footer">
            <p>This is an automated notification from the VESTA store system.</p>
        </div>
    </div>
</body>
</html>
