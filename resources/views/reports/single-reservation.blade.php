<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reservation Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            padding: 40px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1a7a3e;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }
        
        .university {
            font-size: 24px;
            font-weight: bold;
            color: #1a7a3e;
        }
        
        .subtitle {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: #f0faf3;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a7a3e;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            padding: 8px 0;
            color: #555;
        }
        
        .info-value {
            display: table-cell;
            padding: 8px 0;
        }
        
        .equipment-list {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        .equipment-item {
            margin: 5px 0;
            padding-left: 20px;
        }
        
        .date-list {
            margin-top: 5px;
            margin-left: 20px;
        }
        
        .date-list li {
            margin: 5px 0;
        }
        
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .user-table th, .user-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .user-table th {
            background: #f0faf3;
            font-weight: bold;
            color: #1a7a3e;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #999;
        }
        
        .generated-date {
            text-align: right;
            font-size: 10px;
            color: #999;
            margin-bottom: 20px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-approved {
            background: #d4f5df;
            color: #1a7a3e;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-rejected {
            background: #fef2f2;
            color: #dc2626;
        }
        
        .multi-date-badge {
            background: #2db84f;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    @php
        $remarks = json_decode($reservation->remarks, true);
        $multipleDates = $remarks['multiple_dates'] ?? [$reservation->event_date];
        $isMultiDate = count($multipleDates) > 1;
        $equipment = $remarks['equipment'] ?? [];
        $userType = $remarks['user_type'] ?? 'N/A';
        $department = $remarks['department'] ?? 'N/A';
    @endphp

    <div class="header">
        <img src="{{ public_path('images/UCC_Logo.png') }}" class="logo" alt="UCC Logo">
        <div class="university">University of Caloocan City</div>
        <div class="subtitle">Event Reservation System</div>
    </div>
    
    <div class="report-title">
        EVENT RESERVATION REPORT
        @if($isMultiDate)
            <span class="multi-date-badge">{{ count($multipleDates) }} DATES</span>
        @endif
    </div>
    
    <div class="generated-date">
        Generated on: {{ $generated_date }}
    </div>
    
    <!-- Reservation Details -->
    <div class="section">
        <div class="section-title">RESERVATION DETAILS</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Event Name:</div>
                <div class="info-value">{{ $reservation->event_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Event Objectives:</div>
                <div class="info-value">{{ $reservation->description ?? 'No objectives provided' }}</div>
            </div>
            
            <!-- Multiple Dates Display -->
            <div class="info-row">
                <div class="info-label">Event Date(s):</div>
                <div class="info-value">
                    @if($isMultiDate)
                        <strong>{{ count($multipleDates) }} DATES:</strong>
                        <ul class="date-list">
                            @foreach($multipleDates as $date)
                                <li>📌 {{ \Carbon\Carbon::parse($date)->format('F d, Y') }} from {{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} to {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ \Carbon\Carbon::parse($reservation->event_date)->format('F d, Y') }} from {{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} to {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}
                    @endif
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $reservation->status }}">
                        {{ strtoupper($reservation->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Requested Equipment -->
    <div class="section">
        <div class="section-title">REQUESTED EQUIPMENTS</div>
        <div class="equipment-list">
            @if(count($equipment) > 0)
                @foreach($equipment as $item)
                    <div class="equipment-item">• {{ $item }}</div>
                @endforeach
            @else
                <div class="equipment-item">No equipment requested</div>
            @endif
        </div>
    </div>
    
    <!-- Venue -->
    <div class="section">
        <div class="section-title">VENUE</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Campus:</div>
                <div class="info-value">{{ $reservation->campus ? $reservation->campus->name : 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Establishment:</div>
                <div class="info-value">{{ $reservation->establishment ? $reservation->establishment->name : 'N/A' }}</div>
            </div>
        </div>
    </div>
    
    <!-- Requested By -->
    <div class="section">
        <div class="section-title">REQUESTED BY</div>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>User Type</th>
                    <th>Department</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $reservation->user ? $reservation->user->name : 'Unknown User' }}</td>
                    <td>{{ ucfirst($userType) }}</td>
                    <td>{{ $department }}</td>
                    <td>{{ $reservation->user ? $reservation->user->email : 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Request Information -->
    <div class="section">
        <div class="section-title">REQUEST INFORMATION</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Request ID:</div>
                <div class="info-value">#{{ $reservation->id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Request Made On:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($reservation->created_at)->format('F d, Y \a\t h:i A') }}</div>
            </div>
            @if($reservation->approved_at)
            <div class="info-row">
                <div class="info-label">Approved On:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($reservation->approved_at)->format('F d, Y \a\t h:i A') }}</div>
            </div>
            @endif
        </div>
    </div>
    
    <div class="footer">
        This is a system-generated report. For any concerns, please contact the UCC-ERS administrator.<br>
        &copy; {{ date('Y') }} University of Caloocan City. All rights reserved.
    </div>
</body>
</html>