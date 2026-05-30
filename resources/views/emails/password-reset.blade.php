<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - UCC-ERS</title>
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
        .greeting {
            font-size: 24px;
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
        .temp-password-box {
            background: #f7faf8;
            border: 2px solid #d4f5df;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .temp-password-label {
            font-size: 12px;
            color: #6e7f72;
            margin-bottom: 10px;
        }
        .temp-password {
            font-size: 28px;
            font-weight: 700;
            color: #1a7a3e;
            font-family: monospace;
            letter-spacing: 2px;
        }
        .instruction-box {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            border-radius: 12px;
            margin: 20px 0;
        }
        .instruction-title {
            color: #856404;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #1a7a3e, #2db84f);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            margin: 20px 0;
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
            <div class="system-name">Event Reservation System</div>
        </div>
        
        <div class="content">
            <h1 class="greeting">Password Reset Request</h1>
            <p class="message">
                Dear <strong>{{ $user->name }}</strong>,<br>
                We received a request to reset your password for your UCC-ERS account.
            </p>
            
            <div class="temp-password-box">
                <div class="temp-password-label">Your Temporary Password</div>
                <div class="temp-password">{{ $tempPassword }}</div>
            </div>
            
            <div class="instruction-box">
                <div class="instruction-title">📌 Instructions:</div>
                <p style="font-size: 13px; color: #856404;">
                    1. Use the temporary password above to log in to your account.<br>
                    2. After logging in, you will be redirected to change your password.<br>
                    3. Create a new password that you will remember.<br>
                    4. This temporary password will expire after you change your password.
                </p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">Go to Login Page</a>
            </div>
            
            <p style="font-size: 12px; color: #6e7f72; text-align: center; margin-top: 20px;">
                If you did not request a password reset, please ignore this email or contact support.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} University of Caloocan City. All rights reserved.</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>