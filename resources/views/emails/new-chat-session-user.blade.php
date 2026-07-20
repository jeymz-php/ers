<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Message from Admin - UCC-ERS</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0faf3;
            line-height: 1.6;
            color: #3c4a3f;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(10, 61, 31, 0.15);
        }
        .header {
            background: linear-gradient(135deg, #1a7a3e 0%, #2db84f 100%);
            padding: 30px;
            text-align: center;
        }
        .logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: white;
            padding: 10px;
            margin-bottom: 15px;
        }
        .university-name {
            color: white;
            font-size: 22px;
            font-weight: 700;
        }
        .system-name {
            color: rgba(255,255,255,0.9);
            font-size: 12px;
            margin-top: 5px;
        }
        .content {
            padding: 35px;
        }
        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            background: #d4f5df;
            color: #1a7a3e;
        }
        .greeting {
            font-size: 22px;
            font-weight: 700;
            color: #1a7a3e;
            margin-bottom: 15px;
            text-align: center;
        }
        .message {
            color: #6e7f72;
            margin-bottom: 25px;
            text-align: center;
        }
        .details-card {
            background: #f7faf8;
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e8eee9;
            gap: 15px;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 700;
            color: #1a7a3e;
            white-space: nowrap;
        }
        .detail-value {
            text-align: right;
        }
        .message-quote {
            background: #eaf6ee;
            border-left: 4px solid #2db84f;
            padding: 15px 18px;
            margin: 20px 0;
            border-radius: 8px;
            font-style: italic;
            color: #1a7a3e;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #1a7a3e, #2db84f);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            margin-top: 10px;
        }
        .footer {
            background: #f7faf8;
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #b0bdb3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/UCC_Logo.png')) }}" class="logo" alt="UCC Logo">
            <div class="university-name">University of Caloocan City</div>
            <div class="system-name">Event Reservation System &mdash; Messages</div>
        </div>

        <div class="content">
            <div class="status-icon">💬</div>
            <h1 class="greeting">New Message from Admin</h1>
            <p class="message">
                <strong>{{ $admin->name }}</strong> ({{ ucfirst(str_replace('_', ' ', $admin->role)) }}) has started a conversation with you.
            </p>

            <div class="details-card">
                <div class="detail-row">
                    <span class="detail-label">From:</span>
                    <span class="detail-value">{{ $admin->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Sent On:</span>
                    <span class="detail-value">{{ now()->format('F d, Y g:i A') }}</span>
                </div>
            </div>

            <div class="message-quote">
                &ldquo;{{ $firstMessage }}&rdquo;
            </div>

            <div style="text-align: center;">
                <a href="{{ route('user.chat') }}" class="button">Reply in Messages</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} University of Caloocan City. All rights reserved.</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>