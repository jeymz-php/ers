<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCC-ERS Account Credentials</title>
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
            position: relative;
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
            margin-top: 10px;
        }
        
        .system-name {
            color: rgba(255,255,255,0.9);
            font-size: 13px;
            margin-top: 5px;
            letter-spacing: 1px;
        }
        
        .content {
            padding: 40px 35px;
            background: white;
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
            line-height: 1.6;
        }
        
        .credentials-card {
            background: linear-gradient(135deg, #f7faf8 0%, #ffffff 100%);
            border: 2px solid #d4f5df;
            border-radius: 16px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .credential-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e8eee9;
        }
        
        .credential-row:last-child {
            border-bottom: none;
        }
        
        .credential-label {
            font-weight: 700;
            color: #1a7a3e;
            font-size: 13px;
        }
        
        .credential-value {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: 600;
            color: #22913f;
            background: #f0faf3;
            padding: 4px 12px;
            border-radius: 8px;
            letter-spacing: 1px;
        }
        
        .campus-badge {
            display: inline-block;
            background: #2db84f;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .warning-box {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            border-radius: 12px;
            margin: 25px 0;
        }
        
        .warning-title {
            color: #856404;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .warning-text {
            color: #856404;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .button-container {
            text-align: center;
            margin: 35px 0 25px;
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
            transition: transform 0.3s ease;
            box-shadow: 0 4px 15px rgba(34,145,63,0.3);
        }
        
        .login-button:hover {
            transform: translateY(-2px);
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
            margin-bottom: 8px;
        }
        
        @media (max-width: 480px) {
            .container {
                margin: 20px;
                border-radius: 16px;
            }
            .content {
                padding: 25px 20px;
            }
            .greeting {
                font-size: 24px;
            }
            .credential-value {
                font-size: 13px;
            }
            .credential-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
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
            <h1 class="greeting">Welcome, {{ $user->name }}! 👋</h1>
            
            <p class="message">
                Your account has been successfully created in the UCC Event Reservation System. 
                Below are your login credentials.
            </p>
            
            <div class="credentials-card">
                <div class="credential-row">
                    <span class="credential-label">📧 Email Address</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                
                <div class="credential-row">
                    <span class="credential-label">🔐 Password</span>
                    <span class="credential-value">{{ $plainPassword }}</span>
                </div>
                
                <div class="credential-row">
                    <span class="credential-label">🏛️ Campus</span>
                    <span class="campus-badge">{{ $user->campus->name ?? 'Not Assigned' }}</span>
                </div>
            </div>
            
            <div class="warning-box">
                <div class="warning-title">⚠️ Important Security Notice</div>
                <div class="warning-text">
                    Your account is pending admin approval. You will receive another email once your account is approved.
                    You cannot login until your account is approved.
                </div>
            </div>
            
            <div class="button-container">
                <a href="{{ url('/login') }}" class="login-button">🔑 Go to Login Page</a>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-text">
                &copy; {{ date('Y') }} University of Caloocan City. All rights reserved.
            </div>
            <div class="footer-text">
                This is an automated message, please do not reply.
            </div>
        </div>
    </div>
</body>
</html>