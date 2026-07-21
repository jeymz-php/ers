@extends('layouts.admin')

@section('title', 'Edit Reservation')
@section('page-title', 'Edit Reservation')

@section('content')
<style>
    .edit-card {
        max-width: 920px;
        margin: 0 auto;
        background: white;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 2px 20px rgba(0,0,0,0.06);
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 20px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .form-group label {
        color: #1a7a3e;
        font-weight: 600;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #d7e2db;
        border-radius: 10px;
        background: #f7faf8;
        color: #2f4334;
        font-size: 14px;
    }
    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }
    .form-note {
        color: #6e7f72;
        font-size: 13px;
    }
    .button-group {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 20px;
        justify-content: flex-end;
    }
    .button-primary,
    .button-secondary {
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
    }
    .button-primary {
        background: #1a7a3e;
        color: white;
    }
    .button-secondary {
        background: #f1f6f3;
        color: #1a7a3e;
    }
    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        background: #f7faf8;
        border-radius: 999px;
        color: #1a7a3e;
        font-weight: 700;
        margin-bottom: 12px;
    }
    .existing-attachment-item {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f7faf8;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 8px;
        font-size: 13px;
    }
    .existing-attachment-item input {
        width: auto;
    }
    .existing-attachment-item a {
        color: #1a7a3e;
        text-decoration: none;
        flex: 1;
    }
    @media (max-width: 900px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $multipleDates = $remarks['multiple_dates'] ?? [$reservation->event_date];
    $eventDatesText = implode("\n", $multipleDates);
    $equipmentText = is_array($remarks['equipment'] ?? null) ? implode(', ', $remarks['equipment']) : ($remarks['equipment'] ?? '');
    $existingAttachments = $remarks['attachments'] ?? [];
@endphp

<div class="edit-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <div class="status-badge">Reservation ID: #{{ $reservation->reservation_code }}</div>
            <h2 style="margin: 0; font-size: 28px; color: #1a7a3e;">Edit Reservation Details</h2>
            <p style="margin: 8px 0 0; color: #6e7f72;">Update the event details, venue, dates, or schedule. Conflicts are checked automatically before saving.</p>
        </div>
        <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="button-secondary">Back to Details</a>
    </div>

    @if($errors->any())
        <div style="background: #fdecea; border: 1px solid #f5c6cb; color: #842029; padding: 16px; border-radius: 12px; margin-bottom: 20px;">
            <strong>There were some problems with your submission:</strong>
            <ul style="margin: 10px 0 0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.reservations.update', $reservation->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label for="event_name">Event Name</label>
                <input type="text" id="event_name" name="event_name" value="{{ old('event_name', $reservation->event_name) }}" required>
            </div>
            <div class="form-group">
                <label for="user_type">User Type</label>
                <select id="user_type" name="user_type" required>
                    <option value="student" {{ old('user_type', $remarks['user_type'] ?? '') === 'student' ? 'selected' : '' }}>Student</option>
                    <option value="professor" {{ old('user_type', $remarks['user_type'] ?? '') === 'professor' ? 'selected' : '' }}>Professor</option>
                    <option value="admin" {{ old('user_type', $remarks['user_type'] ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="campus_id">Campus</label>
                <select id="campus_id" name="campus_id" required onchange="updateEstablishments()">
                    <option value="">Select campus</option>
                    @foreach($campuses as $campusItem)
                        <option value="{{ $campusItem->id }}" {{ old('campus_id', $reservation->campus_id) == $campusItem->id ? 'selected' : '' }}>{{ $campusItem->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="establishment_id">Venue</label>
                <select id="establishment_id" name="establishment_id" required>
                    <option value="">Select venue</option>
                    @foreach($campuses as $campusItem)
                        @foreach($campusItem->establishments as $est)
                            <option value="{{ $est->id }}" data-campus="{{ $campusItem->id }}" {{ old('establishment_id', $reservation->establishment_id) == $est->id ? 'selected' : '' }}>
                                {{ $campusItem->name }} / {{ $est->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="event_dates">Event Dates</label>
                <textarea id="event_dates" name="event_dates" placeholder="YYYY-MM-DD, one date per line" required>{{ old('event_dates', $eventDatesText) }}</textarea>
                <span class="form-note">Enter one date per line or separated by commas.</span>
            </div>
            <div class="form-group">
                <label for="department">Department</label>
                <input type="text" id="department" name="department" value="{{ old('department', $remarks['department'] ?? '') }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="start_time">Start Time</label>
                <input type="time" id="start_time" name="start_time" value="{{ old('start_time', optional($reservation->start_time)->format('H:i')) }}" required>
            </div>
            <div class="form-group">
                <label for="end_time">End Time</label>
                <input type="time" id="end_time" name="end_time" value="{{ old('end_time', optional($reservation->end_time)->format('H:i')) }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="event_objectives">Event Objectives / Description</label>
                <textarea id="event_objectives" name="event_objectives">{{ old('event_objectives', $reservation->description) }}</textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="equipment">Equipment Requested</label>
                <input type="text" id="equipment" name="equipment" value="{{ old('equipment', $equipmentText) }}" placeholder="Separate items with commas">
                <span class="form-note">Use commas to separate multiple equipment items.</span>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Existing Attachment(s)</label>
                @if(count($existingAttachments) > 0)
                    @foreach($existingAttachments as $attachment)
                        <div class="existing-attachment-item">
                            <input type="checkbox" name="remove_attachments[]" value="{{ $attachment }}" id="attach-{{ $loop->index }}">
                            <a href="{{ Storage::url($attachment) }}" target="_blank">📄 {{ basename($attachment) }}</a>
                            <label for="attach-{{ $loop->index }}" style="margin:0; color:#dc2626; font-weight:400; cursor:pointer;">Remove</label>
                        </div>
                    @endforeach
                @else
                    <span class="form-note">No attachments uploaded for this reservation yet.</span>
                @endif
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="attachments">Add More Attachment(s) (optional)</label>
                <input type="file" id="attachments" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                <span class="form-note">PDF, JPG, or PNG. Max 15MB per file.</span>
            </div>
        </div>

        <div class="button-group">
            <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="button-secondary">Cancel</a>
            <button type="submit" class="button-primary">Save Changes</button>
        </div>
    </form>
</div>

<script>
    function updateEstablishments() {
        const campusSelect = document.getElementById('campus_id');
        const venueSelect = document.getElementById('establishment_id');
        const selectedCampus = campusSelect.value;

        Array.from(venueSelect.options).forEach(option => {
            const campusId = option.dataset.campus;
            option.style.display = campusId === selectedCampus || option.value === '' ? 'block' : 'none';
        });

        if (!Array.from(venueSelect.options).some(option => option.selected && option.style.display === 'block')) {
            venueSelect.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateEstablishments();
    });
</script>
@endsection