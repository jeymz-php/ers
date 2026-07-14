<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pickup Vehicle Reservation Update - UCC-ERS</title>
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
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background: #d4f5df;
            color: #1a7a3e;
        }
        .status-rejected {
            background: #fef2f2;
            color: #dc2626;
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
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 700;
            color: #1a7a3e;
        }
        .reason-box {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .reason-title {
            color: #991b1b;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .attachment-note {
            background: #eaf6ee;
            border-left: 4px solid #2db84f;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-size: 13px;
            color: #1a7a3e;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #1a7a3e, #2db84f);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            margin-top: 20px;
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
            <div class="system-name">Event Reservation System &mdash; Pickup Vehicle</div>
        </div>

        <div class="content">
            @if($status === 'pending')
                <div class="status-icon status-pending">🕓</div>
                <h1 class="greeting">Reservation Received</h1>
                <p class="message">
                    Dear <strong>{{ $reservation->user->name }}</strong>,<br>
                    Your pickup vehicle reservation request has been received and is now <strong>pending</strong> Admin/Super Admin approval.
                </p>
            @elseif($status === 'approved')
                <div class="status-icon status-approved">✓</div>
                <h1 class="greeting">Reservation Approved! 🎉</h1>
                <p class="message">
                    Dear <strong>{{ $reservation->user->name }}</strong>,<br>
                    Great news! Your pickup vehicle reservation has been approved.
                </p>
            @else
                <div class="status-icon status-rejected">✗</div>
                <h1 class="greeting">Reservation Update</h1>
                <p class="message">
                    Dear <strong>{{ $reservation->user->name }}</strong>,<br>
                    We regret to inform you that your pickup vehicle reservation has been rejected.
                </p>
            @endif

            <div class="details-card">
                <div class="detail-row">
                    <span class="detail-label">Reservation ID:</span>
                    <span>#{{ $reservation->reservation_code }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Origin Campus:</span>
                    <span>{{ $reservation->originCampus->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Assigned Vehicle:</span>
                    <span>{{ $reservation->vehicle_label }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Purpose:</span>
                    <span>{{ $reservation->purpose_label }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Destination:</span>
                    <span>{{ $reservation->destination_label }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">{{ $reservation->is_multi_date ? 'Trip Dates:' : 'Trip Date:' }}</span>
                    <span>{{ $reservation->trip_dates_display }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Pickup Time:</span>
                    <span>{{ \Carbon\Carbon::parse($reservation->pickup_time)->format('g:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span style="color: {{ $status === 'approved' ? '#1a7a3e' : ($status === 'rejected' ? '#dc2626' : '#856404') }}; font-weight: bold;">
                        {{ strtoupper($status) }}
                    </span>
                </div>
            </div>

            @if($status === 'rejected' && $reason)
            <div class="reason-box">
                <div class="reason-title">📌 Reason for Rejection:</div>
                <p style="margin-top: 5px;">{{ $reason }}</p>
            </div>
            @endif

            <div class="attachment-note">
                📎 A PDF copy of this reservation report is attached to this email for your reference.
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">View My Reservations</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} University of Caloocan City. All rights reserved.</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>