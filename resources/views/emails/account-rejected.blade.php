<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Update - UCC-ERS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica Neue', sans-serif;
            background-color: #fef2f2;
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
            background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);
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
        
        .reject-icon {
            width: 80px;
            height: 80px;
            background: #dc2626;
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
            color: #991b1b;
            margin-bottom: 15px;
        }
        
        .message {
            color: #6e7f72;
            margin-bottom: 25px;
            font-size: 15px;
        }
        
        .reason-box {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 20px;
            margin: 25px 0;
            border-radius: 12px;
            text-align: left;
        }
        
        .reason-title {
            font-weight: 700;
            color: #991b1b;
            margin-bottom: 10px;
        }
        
        .reason-text {
            color: #6e7f72;
            line-height: 1.6;
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
            <div class="reject-icon">✗</div>
            <h1 class="greeting">Account Application Status</h1>
            
            <p class="message">
                Dear <strong>{{ $user->name }}</strong>,<br>
                We regret to inform you that your account application has been rejected.
            </p>
            
            <div class="reason-box">
                <div class="reason-title">📌 Reason for Rejection:</div>
                <div class="reason-text">{{ $reason }}</div>
            </div>
            
            <p class="message" style="font-size: 14px;">
                If you believe this is a mistake or would like to reapply,<br>
                please contact the UCC-ERS administrator.
            </p>
        </div>
        
        <div class="footer">
            <div class="footer-text">
                &copy; {{ date('Y') }} University of Caloocan City. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>