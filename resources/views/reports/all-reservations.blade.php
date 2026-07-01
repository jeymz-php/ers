<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>All Reservations Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            padding: 30px;
            color: #333;
        }
        
        .header {
            display: table;
            width: 100%;
            padding-bottom: 10px;
            border-bottom: 2.5px solid #1a7a3e;
            margin-bottom: 20px;
        }

        .header-logo {
            display: table-cell;
            width: 64px;
            vertical-align: middle;
        }

        .header-logo img {
            width: 60px;
            height: 60px;
        }

        .header-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 10px;
        }

        .university {
            font-size: 17px;
            font-weight: bold;
            color: #1a7a3e;
            line-height: 1.2;
        }

        .subtitle {
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        
        .filter-info {
            background: #f0faf3;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        .filter-info span {
            font-weight: bold;
            color: #1a7a3e;
        }
        
        .generated-date {
            text-align: right;
            font-size: 10px;
            color: #999;
            margin-bottom: 20px;
        }
        
        .reservations-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        .reservations-table th {
            background: #1a7a3e;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        
        .reservations-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        
        .reservations-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
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
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            vertical-align: middle;
            font-size: 9px;
            color: #999;
            width: 70%;
        }

        .footer-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 30%;
        }

        .footer-right img {
            width: 130px;
            opacity: 0.75;
        }
        
        .summary {
            margin-top: 20px;
            padding: 10px;
            background: #f0faf3;
            border-radius: 8px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('images/UCC_Logo.png') }}" alt="UCC Logo">
        </div>
        <div class="header-text">
            <div class="university">University of Caloocan City</div>
            <div class="subtitle">Event Reservation System &nbsp;|&nbsp; Biglang Awa Street, Cor 11th Ave Catleya, Caloocan City</div>
        </div>
    </div>
    
    <div class="report-title">COMPLETE RESERVATIONS LIST</div>
    
    <div class="generated-date">
        Generated on: {{ $generated_date }}
    </div>
    
    <div class="filter-info">
        <strong>Filters Applied:</strong><br>
        Status: <span>{{ ucfirst($filters['status']) }}</span> | 
        Campus: <span>{{ $filters['campus'] == 'all' ? 'All Campuses' : $filters['campus'] }}</span> | 
        Date Range: <span>{{ $filters['date_range'] }}</span>
    </div>
    
    <table class="reservations-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Requestor</th>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Time</th>
                <th>Venue</th>
                <th>Campus</th>
                <th>Status</th>
                <th>Request Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $res)
            @php
                $requestorName = $res->user ? $res->user->name : 'Unknown User';
                $venueName = $res->establishment ? $res->establishment->name : 'N/A';
                $campusName = $res->campus ? $res->campus->name : 'N/A';
            @endphp
            <tr>
                <td>#{{ $res->id }}</td>
                <td>{{ $requestorName }}</td>
                <td>{{ Str::limit($res->event_name, 30) }}</td>
                <td>{{ \Carbon\Carbon::parse($res->event_date)->format('M d, Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($res->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('g:i A') }}</td>
                <td>{{ $venueName }}</td>
                <td>{{ $campusName }}</td>
                <td>
                    <span class="status-badge status-{{ $res->status }}">
                        {{ strtoupper($res->status) }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($res->created_at)->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 40px;">No reservations found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="summary">
        <strong>Summary:</strong><br>
        Total Reservations: {{ $reservations->count() }}<br>
        Approved: {{ $reservations->where('status', 'approved')->count() }} | 
        Pending: {{ $reservations->where('status', 'pending')->count() }} | 
        Rejected: {{ $reservations->where('status', 'rejected')->count() }}
    </div>
    
    <!-- FOOTER with Caloocan logo on right -->
    <div class="footer">
        <div class="footer-left">
            This is a system-generated report. For any concerns, please contact the UCC-ERS Administrator.<br>
            &copy; {{ date('Y') }} University of Caloocan City. All rights reserved.
        </div>
        <div class="footer-right">
            <img src="{{ public_path('images/CALOOCAN_Logo.png') }}" alt="Caloocan City Logo">
        </div>
    </div>
</body>
</html>