@extends('layouts.admin')

@section('title', 'Reservation Details')
@section('page-title', 'Reservation Details')

@section('content')
<style>
    .detail-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }
    
    .detail-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a7a3e;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e8eee9;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0faf3;
    }
    
    .detail-label {
        font-weight: 600;
        color: #6e7f72;
        font-size: 13px;
    }
    
    .detail-value {
        color: #3c4a3f;
        font-weight: 500;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
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
    .badge-revised {
        display: inline-block;
        background: #fff4cc;
        color: #9a6400;
        border: 1px solid #f7e1a0;
        padding: 4px 12px;
        border-radius: 18px;
        font-size: 12px;
        font-weight: 700;
        margin-left: 10px;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 25px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn-approve {
        background: #2db84f;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
    }
    
    .btn-reject {
        background: #dc2626;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }
    
    .reject-form {
        margin-top: 20px;
        padding: 15px;
        background: #fef2f2;
        border-radius: 12px;
        display: none;
    }
    
    .reject-form.active {
        display: block;
    }
    
    .equipment-list {
        background: #f7faf8;
        border-radius: 8px;
        padding: 10px;
        margin-top: 10px;
    }
    
    .equipment-item {
        padding: 5px 10px;
        margin: 5px 0;
        background: white;
        border-radius: 6px;
        font-size: 13px;
    }
    
    .date-list {
        background: #f7faf8;
        border-radius: 8px;
        padding: 10px;
        margin-top: 5px;
    }
    
    .date-list li {
        padding: 5px 0;
        list-style: none;
        border-bottom: 1px solid #e8eee9;
    }
    
    .date-list li:last-child {
        border-bottom: none;
    }
    
    @media (max-width: 768px) {
        .detail-container {
            grid-template-columns: 1fr;
        }
        .action-buttons {
            flex-direction: column;
        }
        .btn-approve, .btn-reject {
            width: 100%;
            text-align: center;
        }
    }
</style>

@php
    $remarks = json_decode($reservation->remarks, true);
    $equipment = $remarks['equipment'] ?? [];
    $attachments = $remarks['attachments'] ?? [];
    $userType = $remarks['user_type'] ?? 'N/A';
    $department = $remarks['department'] ?? 'N/A';
    $multipleDates = $remarks['multiple_dates'] ?? [$reservation->event_date];
    $isMultiDate = count($multipleDates) > 1;
@endphp

@if(session('highlight') == $reservation->id)
    <div id="highlightToast" style="position: fixed; top: 80px; right: 20px; background: #2db84f; color: white; padding: 12px 20px; border-radius: 8px; z-index: 1000; animation: slideInRight 0.3s ease;">
        🔔 You have a new notification!
    </div>
    <style>
        .highlight-reservation {
            animation: highlightPulse 1s ease-in-out 3;
            background: #d4f5df !important;
        }
        @keyframes highlightPulse {
            0% { background: #d4f5df; }
            50% { background: #ffeb3b; }
            100% { background: #d4f5df; }
        }
    </style>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('highlightToast');
            if (toast) toast.remove();
        }, 3000);
        
        // Highlight the reservation card
        const detailCard = document.querySelector('.detail-card');
        if (detailCard) {
            detailCard.classList.add('highlight-reservation');
            setTimeout(() => {
                detailCard.classList.remove('highlight-reservation');
            }, 3000);
        }
    </script>
@endif

<div class="detail-container">
    <!-- Left Column - Reservation Details -->
    <div class="detail-card">
        <div class="card-title">
            📋 Reservation Information
            @if(count($remarks['updated_fields'] ?? []) > 0)
                <span class="badge-revised">REVISED</span>
            @endif
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Reservation ID:</span>
            <span class="detail-value">#{{ $reservation->reservation_code }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Event Name:</span>
            <span class="detail-value">{{ $reservation->event_name }}</span>
        </div>
        
        <!-- Multiple Dates Display -->
        <div class="detail-row">
            <span class="detail-label">Event Date(s):</span>
            <span class="detail-value">
                @if($isMultiDate)
                    <span class="status-badge status-approved" style="background: #2db84f; color: white; margin-bottom: 8px; display: inline-block;">
                        📅 {{ count($multipleDates) }} DATES
                    </span>
                    <ul class="date-list" style="margin-top: 8px;">
                        @foreach($multipleDates as $date)
                            <li>📌 {{ \Carbon\Carbon::parse($date)->format('F d, Y (l)') }}</li>
                        @endforeach
                    </ul>
                @else
                    {{ \Carbon\Carbon::parse($reservation->event_date)->format('F d, Y') }}
                @endif
            </span>
        </div>
        
        <div class="detail-row">
            <span class="detail-label">Time:</span>
            <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('g:i A') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Venue:</span>
            <span class="detail-value">{{ $reservation->establishment->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Campus:</span>
            <span class="detail-value">{{ $reservation->campus->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="status-badge status-{{ $reservation->status }}">
                {{ strtoupper($reservation->status) }}
            </span>
        </div>
        @if($reservation->approved_at)
        <div class="detail-row">
            <span class="detail-label">Approved On:</span>
            <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->approved_at)->format('F d, Y g:i A') }}</span>
        </div>
        @endif
    </div>
    
    <!-- Right Column - User Information -->
    <div class="detail-card">
        <div class="card-title">👤 User Information</div>
        
        <div class="detail-row">
            <span class="detail-label">Name:</span>
            <span class="detail-value">{{ $reservation->user->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Email:</span>
            <span class="detail-value">{{ $reservation->user->email }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Phone:</span>
            <span class="detail-value">{{ $reservation->user->phone_number ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">User Type:</span>
            <span class="detail-value">{{ ucfirst($userType) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Department:</span>
            <span class="detail-value">{{ $department }}</span>
        </div>
    </div>
    
    <!-- Full Width - Event Objectives -->
    <div class="detail-card" style="grid-column: 1/-1;">
        <div class="card-title">📝 Event Objectives</div>
        <p>{{ $reservation->description ?? 'No description provided.' }}</p>
    </div>
    
    <!-- Equipment Requested -->
    @if(count($equipment) > 0)
    <div class="detail-card">
        <div class="card-title">🔧 Equipment Requested</div>
        <div class="equipment-list">
            @foreach($equipment as $item)
                <div class="equipment-item">• {{ $item }}</div>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Attachments -->
    @if(count($attachments) > 0)
    <div class="detail-card">
        <div class="card-title">📎 Attachments</div>
        <div class="equipment-list">
            @foreach($attachments as $attachment)
                @php
                    $filename = basename($attachment);
                    $fileUrl = asset('uploads/reservations/2026/06/' . $filename);
                @endphp
                <div class="equipment-item">
                    📄 <a href="{{ Storage::url($attachment) }}" target="_blank">{{ basename($attachment) }}</a>
                    <span style="margin-left: 10px; font-size: 11px; color: #6e7f72;">(Click to view)</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Action Buttons -->
<div class="action-buttons">
    <a href="{{ route('report.single', $reservation->id) }}" class="btn-approve" style="background: #2db84f; text-decoration: none;" target="_blank">📄 View Report</a>
    <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn-approve" style="background: #1a7a3e; text-decoration: none;">✏️ Edit Reservation</a>
    
    @if(in_array($reservation->status, ['pending', 'approved', 'rejected']))
        <form method="POST" action="{{ route('admin.reservations.approve', $reservation->id) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn-approve" onclick="return confirm('Approve this reservation?')">✅ Approve Reservation</button>
        </form>
        
        <button type="button" class="btn-reject" onclick="showRejectForm()">❌ Reject Reservation</button>
    @endif
</div>

<div id="rejectForm" class="reject-form">
    <form method="POST" action="{{ route('admin.reservations.reject', $reservation->id) }}">
        @csrf
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1a7a3e;">Reason for Rejection:</label>
            <textarea name="rejection_reason" rows="4" placeholder="Please provide a detailed reason for rejecting this reservation..." style="width: 100%; padding: 10px; border: 1px solid #e8eee9; border-radius: 8px;" required></textarea>
            <small style="font-size: 11px; color: #6e7f72;">Minimum 10 characters</small>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn-reject" style="background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">Confirm Rejection</button>
            <button type="button" class="btn-approve" onclick="hideRejectForm()" style="background: #6e7f72; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">Cancel</button>
        </div>
    </form>
</div>

<div style="text-align: center; margin-top: 20px;">
    <a href="{{ route('admin.reservations.index') }}" style="color: #1a7a3e; text-decoration: none;">← Back to Reservations</a>
</div>

<script>
    function showRejectForm() {
        document.getElementById('rejectForm').classList.add('active');
    }
    
    function hideRejectForm() {
        document.getElementById('rejectForm').classList.remove('active');
    }
</script>
@endsection