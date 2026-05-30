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
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1a7a3e;
        }
        
        .logo {
            width: 70px;
            height: 70px;
            margin-bottom: 10px;
        }
        
        .university {
            font-size: 22px;
            font-weight: bold;
            color: #1a7a3e;
        }
        
        .subtitle {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
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
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
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
    <div class="header">
        <img src="{{ public_path('images/UCC_Logo.png') }}" class="logo" alt="UCC Logo">
        <div class="university">University of Caloocan City</div>
        <div class="subtitle">Event Reservation System - All Reservations Report</div>
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
                // Fix: Add null checks for relationships
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
                <td colspan="9" style="text-align: center;">No reservations found</td>
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
    
    <div class="footer">
        This is a system-generated report. For any concerns, please contact the UCC-ERS administrator.<br>
        &copy; {{ date('Y') }} University of Caloocan City. All rights reserved.
    </div>
</body>
</html>