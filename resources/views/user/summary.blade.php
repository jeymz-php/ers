@extends('layouts.app')

@section('title', 'My Reservations')

@section('content')
<style>
    /* Your existing styles remain the same */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f0faf3;
        font-family: 'Arial', sans-serif;
    }

    .user-container {
        display: flex;
        min-height: 100vh;
    }

    .user-main {
        flex: 1;
        margin-left: 280px;
        background: #f0faf3;
        min-height: 100vh;
    }

    .content-area {
        padding: 25px 30px;
    }

    .welcome-banner {
        background: white;
        border-radius: 16px;
        padding: 18px 25px;
        margin-bottom: 25px;
        border-left: 4px solid #2db84f;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .welcome-text {
        font-size: 16px;
        color: #3c4a3f;
    }

    .welcome-text strong {
        color: #1a7a3e;
    }

    .section-badge {
        background: #f0faf3;
        color: #1a7a3e;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .section {
        background: white;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8eee9;
        flex-wrap: wrap;
        gap: 10px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a7a3e;
    }

    .section-count {
        background: #2db84f;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead th {
        text-align: left;
        padding: 12px;
        background: #f7faf8;
        color: #1a7a3e;
        font-weight: 700;
        font-size: 13px;
        border-bottom: 2px solid #e8eee9;
    }

    .data-table tbody td {
        padding: 12px;
        border-bottom: 1px solid #e8eee9;
        color: #3c4a3f;
        font-size: 13px;
    }

    .data-table tbody tr:hover {
        background: #f7faf8;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
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

    .status-cancelled {
        background: #e8eee9;
        color: #6e7f72;
    }

    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #e8eee9;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pagination-info {
        font-size: 12px;
        color: #6e7f72;
    }

    .pagination-controls {
        display: flex;
        gap: 8px;
    }

    .page-btn {
        background: #f0faf3;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        color: #1a7a3e;
        transition: all 0.3s;
    }

    .page-btn:hover {
        background: #1a7a3e;
        color: white;
    }

    .page-btn.active {
        background: #2db84f;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #b0bdb3;
    }

    .btn-view {
        background: #2db84f;
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 11px;
        display: inline-block;
        margin: 2px;
    }

    .btn-view:hover {
        background: #1a7a3e;
        color: white;
    }

    /* Modal Styles - Fixed Width & Centered */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        width: 90%;
        max-width: 650px;
        margin: 20px auto;
        position: relative;
        animation: slideIn 0.3s ease;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        padding: 20px 25px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 20px;
    }

    .close-modal {
        background: none;
        border: none;
        color: white;
        font-size: 28px;
        cursor: pointer;
        line-height: 1;
    }

    .modal-body {
        padding: 25px;
        max-height: 80vh;
        overflow-y: auto;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Modal content styling */
    .modal-body .detail-row {
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e8eee9;
    }

    .modal-body .detail-label {
        font-weight: 600;
        color: #1a7a3e;
        margin-bottom: 5px;
        font-size: 13px;
    }

    .modal-body .detail-value {
        color: #3c4a3f;
        font-size: 14px;
        margin-top: 3px;
    }

    @media (max-width: 768px) {
        .modal-content {
            width: 95%;
            margin: 10px;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-header h3 {
            font-size: 18px;
        }
    }

    @media (max-width: 768px) {
        .section {
            padding: 15px;
            overflow-x: auto;
        }
        .data-table {
            min-width: 600px;
        }
        .data-table th,
        .data-table td {
            padding: 8px;
            font-size: 11px;
        }
        .status-badge {
            padding: 3px 8px;
            font-size: 9px;
        }
        .btn-view {
            padding: 3px 8px;
            font-size: 10px;
        }
        .pagination {
            flex-direction: column;
            gap: 10px;
        }
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 480px) {
        .section-title {
            font-size: 14px;
        }
        .section-count {
            font-size: 10px;
            padding: 2px 8px;
        }
    }
</style>

<div class="user-container">
    @include('partials.user-sidebar')

    <main class="user-main">
        @include('partials.user-topbar')

        <div class="content-area">
            <div class="welcome-banner">
                <div class="welcome-text">
                    Welcome back, <strong>{{ Auth::user()->name }}</strong>!
                </div>
                <div class="section-badge">My Reservations Dashboard</div>
            </div>

            <!-- Latest Reservations Section -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title">🆕 Latest Reservations</div>
                    <div class="section-count">{{ $latestReservations->total() }} recent</div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Event Date</th>
                                <th>Time</th>
                                <th>Venue</th>
                                <th>Campus</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestReservations as $res)
                            @php
                                $submittedDate = \Carbon\Carbon::parse($res->created_at);
                                $isNew = $submittedDate->diffInHours(now()) < 24;
                                $remarks = json_decode($res->remarks, true);
                                $multipleDates = $remarks['multiple_dates'] ?? [$res->event_date];
                                $isMultiDate = count($multipleDates) > 1;
                                $venueName = $res->establishment ? $res->establishment->name : 'N/A';
                                $campusName = $res->campus ? $res->campus->name : 'N/A';
                            @endphp
                            <tr style="{{ $isNew ? 'background: #f0faf3;' : '' }}">
                                <td>
                                    {{ Str::limit($res->event_name, 30) }}
                                    @if($isMultiDate)
                                        <span style="background: #2db84f; color: white; padding: 2px 6px; border-radius: 10px; font-size: 9px; margin-left: 5px;">{{ count($multipleDates) }} dates</span>
                                    @endif
                                    @if($isNew)
                                        <span style="background: #2db84f; color: white; padding: 2px 6px; border-radius: 10px; font-size: 9px; margin-left: 5px;">NEW</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isMultiDate)
                                        <strong>{{ count($multipleDates) }} dates</strong><br>
                                        <small style="font-size: 10px; color: #2db84f;">
                                            {{ \Carbon\Carbon::parse($multipleDates[0])->format('M d') }} - {{ \Carbon\Carbon::parse(end($multipleDates))->format('M d, Y') }}
                                        </small>
                                    @else
                                        {{ \Carbon\Carbon::parse($res->event_date)->format('M d, Y') }}
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($res->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($res->end_time)->format('g:i A') }}</td>
                                <td>{{ $venueName }}</td>
                                <td>{{ $campusName }}</td>
                                <td>
                                    <span class="status-badge status-{{ $res->status }}">
                                        {{ strtoupper($res->status) }}
                                    </span>
                                </td>
                                <td>{{ $submittedDate->format('M d, Y') }}</td>
                                <td>
                                    <a href="#" class="btn-view" onclick="showReservationDetails({{ $res->id }})">View</a>
                                    <a href="{{ route('report.single', $res->id) }}" class="btn-view" style="background: #dc2626;" target="_blank">📄 View Report</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="empty-state">No reservations found. Start by making a reservation!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <div class="pagination-info">
                        Showing {{ $latestReservations->firstItem() }} to {{ $latestReservations->lastItem() }} of {{ $latestReservations->total() }} reservations
                    </div>
                    <div class="pagination-controls">
                        {{ $latestReservations->links() }}
                    </div>
                </div>
            </div>

            <!-- Active Reservations Section -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title">✅ Active Reservations</div>
                    <div class="section-count">{{ $activeReservations->total() }} active</div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Event Date</th>
                                <th>Time</th>
                                <th>Venue</th>
                                <th>Campus</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeReservations as $res)
                            @php
                                $venueName = $res->establishment ? $res->establishment->name : 'N/A';
                                $campusName = $res->campus ? $res->campus->name : 'N/A';
                            @endphp
                            <tr>
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
                                <td>
                                    <a href="#" class="btn-view" onclick="showReservationDetails({{ $res->id }})">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="empty-state">No active reservations found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <div class="pagination-info">
                        Showing {{ $activeReservations->firstItem() }} to {{ $activeReservations->lastItem() }} of {{ $activeReservations->total() }} active reservations
                    </div>
                    <div class="pagination-controls">
                        {{ $activeReservations->links() }}
                    </div>
                </div>
            </div>

            <!-- Reservation History Section -->
            <div class="section">
                <div class="section-header">
                    <div class="section-title">📜 Reservation History</div>
                    <div class="section-count">{{ $historyReservations->total() }} records</div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Event Date</th>
                                <th>Time</th>
                                <th>Venue</th>
                                <th>Campus</th>
                                <th>Status</th>
                                <th>Approved/Rejected</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historyReservations as $res)
                            @php
                                $venueName = $res->establishment ? $res->establishment->name : 'N/A';
                                $campusName = $res->campus ? $res->campus->name : 'N/A';
                            @endphp
                            <tr>
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
                                <td>
                                    @if($res->approved_at)
                                        {{ \Carbon\Carbon::parse($res->approved_at)->format('M d, Y') }}
                                    @else
                                        ---
                                    @endif
                                </td>
                                <td>
                                    <a href="#" class="btn-view" onclick="showReservationDetails({{ $res->id }})">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="empty-state">No reservation history found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <div class="pagination-info">
                        Showing {{ $historyReservations->firstItem() }} to {{ $historyReservations->lastItem() }} of {{ $historyReservations->total() }} records
                    </div>
                    <div class="pagination-controls">
                        {{ $historyReservations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Reservation Details Modal -->
<div id="reservationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>📋 Reservation Details</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalContent" class="modal-body">
            <div style="text-align: center; padding: 40px;">Loading...</div>
        </div>
    </div>
</div>

<script>
    function showReservationDetails(id) {
        const modal = document.getElementById('reservationModal');
        const modalContent = document.getElementById('modalContent');
        
        modal.style.display = 'flex';
        modalContent.innerHTML = '<div style="text-align: center; padding: 40px;">Loading reservation details...</div>';
        
        fetch(`/user/reservations/${id}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const r = data.reservation;
                    const remarks = r.remarks ? JSON.parse(r.remarks) : {};
                    const equipment = remarks.equipment || [];
                    const attachments = remarks.attachments || [];
                    const multipleDates = remarks.multiple_dates || [r.event_date];
                    const isMultiDate = multipleDates.length > 1;
                    
                    const startTime = r.start_time || formatTime(r.start_time_raw || '');
                    const endTime = r.end_time || formatTime(r.end_time_raw || '');
                    
                    let equipmentHtml = '';
                    if (equipment.length > 0) {
                        equipmentHtml = `
                            <div class="detail-row">
                                <div class="detail-label">🔧 Requested Equipment:</div>
                                <div class="detail-value">
                                    <ul style="margin-top: 5px; margin-left: 20px;">
                                        ${equipment.map(e => `<li style="margin: 3px 0;">${escapeHtml(e)}</li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        `;
                    }
                    
                    let attachmentsHtml = '';
                    if (attachments.length > 0) {
                        attachmentsHtml = `
                            <div class="detail-row">
                                <div class="detail-label">📎 Attachments:</div>
                                <div class="detail-value">
                                    ${attachments.map(a => `<div style="margin: 5px 0;"><a href="/storage/${a}" target="_blank" style="color: #2db84f;">📄 ${a.split('/').pop()}</a></div>`).join('')}
                                </div>
                            </div>
                        `;
                    }
                    
                    let dateTimeHtml = '';
                    if (isMultiDate) {
                        dateTimeHtml = `
                            <div class="detail-row">
                                <div class="detail-label">📅 Event Dates & Times:</div>
                                <div class="detail-value">
                                    <ul style="margin-top: 5px; margin-left: 20px;">
                                        ${multipleDates.map(date => `<li style="margin: 8px 0;">${new Date(date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}<br><span style="color: #2db84f;">⏰ ${startTime} - ${endTime}</span></li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        `;
                    } else {
                        dateTimeHtml = `
                            <div class="detail-row">
                                <div class="detail-label">📅 Event Date:</div>
                                <div class="detail-value">${new Date(r.event_date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">⏰ Time:</div>
                                <div class="detail-value">${startTime} - ${endTime}</div>
                            </div>
                        `;
                    }
                    
                    modalContent.innerHTML = `
                        <div class="detail-row">
                            <div class="detail-label">📌 Event Name:</div>
                            <div class="detail-value"><strong>${escapeHtml(r.event_name)}</strong></div>
                        </div>
                        ${dateTimeHtml}
                        <div class="detail-row">
                            <div class="detail-label">📍 Venue:</div>
                            <div class="detail-value">${r.establishment ? escapeHtml(r.establishment.name) : 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">🏛️ Campus:</div>
                            <div class="detail-value">${r.campus ? escapeHtml(r.campus.name) : 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">📊 Status:</div>
                            <div class="detail-value">
                                <span class="status-badge status-${r.status}" style="display: inline-block;">${r.status.toUpperCase()}</span>
                            </div>
                        </div>
                        ${r.description ? `
                        <div class="detail-row">
                            <div class="detail-label">📝 Event Objectives:</div>
                            <div class="detail-value">${escapeHtml(r.description)}</div>
                        </div>
                        ` : ''}
                        ${equipmentHtml}
                        ${attachmentsHtml}
                        ${r.approved_at ? `
                        <div class="detail-row">
                            <div class="detail-label">✅ Approved on:</div>
                            <div class="detail-value">${new Date(r.approved_at).toLocaleString()}</div>
                        </div>
                        ` : ''}
                        ${remarks.rejection_reason ? `
                        <div class="detail-row">
                            <div class="detail-label">❌ Rejection Reason:</div>
                            <div class="detail-value" style="color: #dc2626;">${escapeHtml(remarks.rejection_reason)}</div>
                        </div>
                        ` : ''}
                    `;
                } else {
                    modalContent.innerHTML = '<div style="text-align: center; padding: 40px;">Error loading reservation details.</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalContent.innerHTML = '<div style="text-align: center; padding: 40px;">Error loading reservation details.</div>';
            });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function closeModal() {
        document.getElementById('reservationModal').style.display = 'none';
    }

    function formatTime(timeString) {
        if (!timeString) return 'Invalid Time';
        try {
            // If time is already formatted like "12:00 PM", return as is
            if (timeString.includes('AM') || timeString.includes('PM')) {
                return timeString;
            }
            
            // Handle time in format "HH:MM:SS" or "HH:MM"
            let timePart = timeString;
            if (timeString.includes(' ')) {
                timePart = timeString.split(' ')[1];
            }
            
            const parts = timePart.split(':');
            let hours = parseInt(parts[0]);
            const minutes = parts[1];
            
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            
            return `${hours}:${minutes} ${ampm}`;
        } catch (e) {
            console.error('Time parsing error:', e);
            return timeString;
        }
    }
    
    window.onclick = function(event) {
        const modal = document.getElementById('reservationModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>
@endsection