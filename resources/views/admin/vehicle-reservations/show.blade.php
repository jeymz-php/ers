@extends('layouts.admin')

@section('title', 'Vehicle Reservation Details')
@section('page-title', 'Pickup Vehicle Reservation Details')

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
        gap: 15px;
    }

    .detail-label { font-weight: 600; color: #6e7f72; font-size: 13px; }
    .detail-value { color: #3c4a3f; font-weight: 500; text-align: right; }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-approved { background: #d4f5df; color: #1a7a3e; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-rejected { background: #fef2f2; color: #dc2626; }
    .status-cancelled { background: #e8eee9; color: #6e7f72; }

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

    .reject-form.active { display: block; }

    .attachment-list {
        background: #f7faf8;
        border-radius: 8px;
        padding: 10px;
        margin-top: 10px;
    }

    .attachment-item {
        padding: 5px 10px;
        margin: 5px 0;
        background: white;
        border-radius: 6px;
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .detail-container { grid-template-columns: 1fr; }
        .action-buttons { flex-direction: column; }
        .btn-approve, .btn-reject { width: 100%; text-align: center; }
        .detail-value { text-align: left; }
    }
</style>

@if(session('highlight') == $reservation->id)
    <div id="highlightToast" style="position: fixed; top: 80px; right: 20px; background: #2db84f; color: white; padding: 12px 20px; border-radius: 8px; z-index: 1000;">
        🔔 You have a new notification!
    </div>
    <style>
        .highlight-reservation { animation: highlightPulse 1s ease-in-out 3; background: #d4f5df !important; }
        @keyframes highlightPulse { 0% { background: #d4f5df; } 50% { background: #ffeb3b; } 100% { background: #d4f5df; } }
    </style>
    <script>
        setTimeout(() => { const t = document.getElementById('highlightToast'); if (t) t.remove(); }, 3000);
        const detailCard = document.querySelector('.detail-card');
        if (detailCard) {
            detailCard.classList.add('highlight-reservation');
            setTimeout(() => detailCard.classList.remove('highlight-reservation'), 3000);
        }
    </script>
@endif

<div class="detail-container">
    <!-- Reservation Details -->
    <div class="detail-card">
        <div class="card-title">
            🚐 Reservation Information
            @if($reservation->is_revised)
                <span class="status-badge" style="background:#fff3cd; color:#856404; margin-left:8px; vertical-align:middle;">✏️ REVISED</span>
            @endif
        </div>

        <div class="detail-row">
            <span class="detail-label">Reservation ID:</span>
            <span class="detail-value">#{{ $reservation->reservation_code }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Origin Campus:</span>
            <span class="detail-value">{{ $reservation->originCampus->name ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Assigned Vehicle:</span>
            <span class="detail-value">{{ $reservation->vehicle_label }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Purpose:</span>
            <span class="detail-value">{{ $reservation->purpose_label }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Destination:</span>
            <span class="detail-value">
                {{ $reservation->destination_type === 'campus' ? 'Within Campus — ' : 'Outside Campus — ' }}{{ $reservation->destination_label }}
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Trip Date:</span>
            <span class="detail-value">{{ $reservation->trip_dates_display }}</span>
        </div>
        @if(count($reservation->trip_dates) > 1)
        <div class="detail-row">
            <span class="detail-label">Selected Dates:</span>
            <span class="detail-value">
                @foreach($reservation->trip_dates as $tripDate)
                    <span class="status-badge" style="background: #f0faf3; color: #1a7a3e; margin: 2px;">{{ \Carbon\Carbon::parse($tripDate)->format('M d, Y') }}</span>
                @endforeach
            </span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Pickup Time:</span>
            <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->pickup_time)->format('g:i A') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span class="status-badge status-{{ $reservation->status }}">{{ strtoupper($reservation->status) }}</span>
        </div>
        @if($reservation->approved_at)
        <div class="detail-row">
            <span class="detail-label">Approved On:</span>
            <span class="detail-value">{{ \Carbon\Carbon::parse($reservation->approved_at)->format('F d, Y g:i A') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Approved By:</span>
            <span class="detail-value">{{ $reservation->approver->name ?? 'N/A' }}</span>
        </div>
        @endif
        @if($reservation->status === 'rejected' && $reservation->remarks)
        <div class="detail-row">
            <span class="detail-label">Rejection Reason:</span>
            <span class="detail-value" style="color: #dc2626;">{{ $reservation->remarks }}</span>
        </div>
        @endif
    </div>

    <!-- Requester Information -->
    <div class="detail-card">
        <div class="card-title">👤 Requester Information</div>

        <div class="detail-row">
            <span class="detail-label">Name:</span>
            <span class="detail-value">{{ $reservation->user->name ?? 'Unknown' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Email:</span>
            <span class="detail-value">{{ $reservation->user->email ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Phone:</span>
            <span class="detail-value">{{ $reservation->user->phone_number ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Requester Type:</span>
            <span class="detail-value">{{ $reservation->requester_type_label }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Submitted On:</span>
            <span class="detail-value">{{ $reservation->created_at->format('F d, Y g:i A') }}</span>
        </div>
    </div>

    <!-- Additional Details -->
    <div class="detail-card" style="grid-column: 1/-1;">
        <div class="card-title">📝 Additional Details</div>
        <p>{{ $reservation->notes ?? 'No additional details provided.' }}</p>
    </div>

    <!-- Revision History -->
    @if($reservation->is_revised)
    <div class="detail-card" style="grid-column: 1/-1; background: #fffaf0;">
        <div class="card-title">✏️ Revision History</div>
        <p style="font-size: 12px; color: #856404; margin-bottom: 12px;">
            Last revised by <strong>{{ $reservation->revision_info['last_revision_by'] ?? 'Admin' }}</strong>
            on {{ $reservation->revision_info['last_revision_at'] ?? 'Unknown' }}
        </p>
        @foreach($reservation->revision_info['updated_fields'] as $field)
            <div class="detail-row">
                <span class="detail-label">{{ $field['label'] }}:</span>
                <span class="detail-value">
                    <span style="color:#dc2626; text-decoration: line-through;">{{ $field['old'] }}</span>
                    &rarr;
                    <span style="color:#1a7a3e; font-weight:700;">{{ $field['new'] }}</span>
                </span>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Attachments -->
    @if(!empty($reservation->attachments))
    <div class="detail-card" style="grid-column: 1/-1;">
        <div class="card-title">📎 Attachments</div>
        <div class="attachment-list">
            @foreach($reservation->attachments as $attachment)
                <div class="attachment-item">
                    📄 <a href="{{ Storage::url($attachment) }}" target="_blank">{{ basename($attachment) }}</a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Action Buttons -->
<div class="action-buttons">
    <a href="{{ route('admin.vehicle-reservations.edit', $reservation->id) }}" class="btn-approve" style="background: #f5a524; text-decoration: none;">✏️ Edit Reservation</a>

    @if($reservation->status === 'approved')
        <a href="{{ route('report.vehicle.single', $reservation->id) }}" class="btn-approve" style="background: #2db84f; text-decoration: none;" target="_blank">📄 Generate Report</a>
    @endif

    @if(in_array($reservation->status, ['pending', 'rejected']))
        <form method="POST" action="{{ route('admin.vehicle-reservations.approve', $reservation->id) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn-approve" onclick="return confirm('Approve this pickup vehicle reservation?')">✅ Approve Reservation</button>
        </form>
    @endif

    @if(in_array($reservation->status, ['pending', 'approved']))
        <button type="button" class="btn-reject" onclick="showRejectForm()">❌ Reject Reservation</button>
    @endif
</div>

<div id="rejectForm" class="reject-form">
    <form method="POST" action="{{ route('admin.vehicle-reservations.reject', $reservation->id) }}">
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

        @if($reservation->status === 'approved' && $reservation->remarks)
        <div class="detail-row">
            <span class="detail-label">Admin Notes:</span>
            <span class="detail-value">{{ $reservation->remarks }}</span>
        </div>
        @endif

<div style="text-align: center; margin-top: 20px;">
    <a href="{{ route('admin.vehicle-reservations.index') }}" style="color: #1a7a3e; text-decoration: none;">← Back to Vehicle Reservations</a>
</div>

<script>
    function showRejectForm() { document.getElementById('rejectForm').classList.add('active'); }
    function hideRejectForm() { document.getElementById('rejectForm').classList.remove('active'); }
</script>
@endsection