<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved - UCC-ERS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica Neue', sans-serif;
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
            padding: 40px 30px;
            text-align: center;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            padding: 12px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .university-name {
            color: white;
            font-size: 24px;
            font-weight: 700;
        }
        
        .system-name {
            color: rgba(255,255,255,0.9);
            font-size: 13px;
            margin-top: 5px;
        }
        
        .content {
            padding: 40px 35px;
            text-align: center;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: #4cca68;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: white;
        }
        
        .greeting {
            font-size: 28px;
            font-weight: 700;
            color: #1a7a3e;
            margin-bottom: 15px;
        }
        
        .message {
            color: #6e7f72;
            margin-bottom: 25px;
            font-size: 15px;
        }
        
        .info-card {
            background: #f7faf8;
            border-radius: 16px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }
        
        .info-row {
            padding: 10px 0;
            border-bottom: 1px solid #e8eee9;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 700;
            color: #1a7a3e;
            display: inline-block;
            width: 100px;
        }
        
        .button-container {
            margin: 30px 0 20px;
        }
        
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #1a7a3e 0%, #2db84f 100%);
            color: white;
            text-decoration: none;
            padding: 14px 35px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 15px rgba(34,145,63,0.3);
        }
        
        .footer {
            background: #f7faf8;
            padding: 25px 35px;
            text-align: center;
            border-top: 1px solid #e8eee9;
        }
        
        .footer-text {
            color: #b0bdb3;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/UCC_Logo.png')) }}" alt="UCC Logo" class="logo">
            <div class="university-name">University of Caloocan City</div>
            <div class="system-name">Event Reservation System (UCC-ERS)</div>
        </div>
        
        <div class="content">
            <div class="success-icon">✓</div>
            <h1 class="greeting">Account Approved! 🎉</h1>
            
            <p class="message">
                Dear <strong>{{ $user->name }}</strong>,<br>
                Great news! Your account has been approved by the administrator.
                You can now access the UCC Event Reservation System.
            </p>
            
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">📧 Email:</span>
                    <span>{{ $user->email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🏛️ Campus:</span>
                    <span>{{ $user->campus->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">✅ Approved on:</span>
                    <span>{{ now()->format('F d, Y h:i A') }}</span>
                </div>
            </div>
            
            <div class="button-container">
                <a href="{{ url('/login') }}" class="login-button">🔑 Login to Your Account</a>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-text">
                &copy; {{ date('Y') }} University of Caloocan City. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>