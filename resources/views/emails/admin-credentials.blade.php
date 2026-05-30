<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account Credentials - UCC-ERS</title>
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
        .credentials-card {
            background: #f7faf8;
            border: 2px solid #d4f5df;
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-row {
            display: flex;
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
        }
        .credential-value {
            font-family: monospace;
            font-size: 16px;
            font-weight: 600;
            color: #22913f;
        }
        .info-box {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 12px;
            margin: 20px 0;
        }
        .info-title {
            color: #856404;
            font-weight: 700;
            margin-bottom: 8px;
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
        .role-badge {
            display: inline-block;
            background: #1a7a3e;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/UCC_Logo.png')) }}" class="logo" alt="UCC Logo">
            <div class="university-name">University of Caloocan City</div>
            <div class="system-name">Event Reservation System (UCC-ERS)</div>
        </div>
        
        <div class="content">
            <h1 class="greeting">Welcome to the Admin Team, {{ $admin->name }}! 👋</h1>
            
            <p class="message">
                You have been granted administrator access to the UCC Event Reservation System.
                Below are your login credentials.
            </p>
            
            <div class="credentials-card">
                <div class="credential-row">
                    <span class="credential-label">📧 Email</span>
                    <span class="credential-value">{{ $admin->email }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">🔐 Password</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">🏛️ Campus</span>
                    <span class="credential-value">{{ $admin->campus->name ?? 'All Campuses' }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">👑 Role</span>
                    <span class="credential-value">
                        <span class="role-badge">{{ ucfirst($admin->role) }}</span>
                    </span>
                </div>
            </div>
            
            <div class="info-box">
                <div class="info-title">⚠️ Important Security Notice</div>
                <div style="font-size: 13px; color: #856404;">
                    Please change your password immediately after your first login for security purposes.
                    You can do this by going to Settings → Change Password.
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/admin/login') }}" class="button">🔑 Go to Admin Login</a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} University of Caloocan City. All rights reserved.</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>