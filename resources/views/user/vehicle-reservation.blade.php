@extends('layouts.app')

@section('title', 'Pickup Vehicle')

@section('content')
<style>
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
        transition: margin-left 0.3s ease;
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
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .welcome-text {
        color: #3c4a3f;
        font-size: 15px;
    }

    .vehicle-layout {
        display: grid;
        grid-template-columns: 1.1fr 1fr;
        gap: 25px;
        align-items: start;
    }

    .panel-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .panel-title {
        font-size: 17px;
        font-weight: 700;
        color: #1a7a3e;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8eee9;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1a7a3e;
        font-size: 13px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        background: #fbfefc;
    }

    .form-group textarea {
        resize: vertical;
    }

    .form-hint {
        font-size: 11px;
        color: #6e7f72;
        margin-top: 5px;
        display: block;
    }

    .radio-group {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .radio-option {
        flex: 1;
        min-width: 140px;
        border: 1px solid #e8eee9;
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 13px;
        color: #3c4a3f;
        transition: all 0.2s;
    }

    .radio-option:hover {
        border-color: #2db84f;
    }

    .radio-option input {
        width: auto;
        accent-color: #1a7a3e;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 10px;
        cursor: pointer;
        width: 100%;
        font-size: 14px;
        font-weight: 700;
    }

    .btn-primary:hover {
        opacity: 0.92;
    }

    .alert {
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
    }

    .alert-success {
        background: #d4f5df;
        color: #1a7a3e;
    }

    .alert-error {
        background: #fef2f2;
        color: #dc2626;
    }

    .alert ul {
        margin: 5px 0 0 18px;
    }

    /* My Requests list */
    .request-card {
        background: #f7faf8;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 12px;
    }

    .request-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 10px;
    }

    .request-code {
        font-weight: 700;
        color: #1a7a3e;
        font-size: 13px;
    }

    .request-purpose {
        font-size: 12px;
        color: #6e7f72;
        margin-top: 2px;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
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

    .request-detail-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #3c4a3f;
        padding: 4px 0;
    }

    .request-detail-row span:first-child {
        color: #6e7f72;
    }

    .empty-state {
        text-align: center;
        padding: 40px 15px;
        color: #b0bdb3;
        font-size: 13px;
    }

    .pagination-wrap {
        margin-top: 15px;
    }

    .calendar-card {
        margin-top: 20px;
        border: 1px solid #e8eee9;
        border-radius: 16px;
        padding: 18px;
        background: #fcfdfc;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        gap: 10px;
        flex-wrap: wrap;
    }

    .calendar-month {
        font-size: 15px;
        font-weight: 700;
        color: #1a7a3e;
    }

    .calendar-nav {
        display: flex;
        gap: 8px;
    }

    .calendar-nav button {
        border: none;
        border-radius: 8px;
        padding: 8px 10px;
        background: #f0faf3;
        color: #1a7a3e;
        cursor: pointer;
        font-weight: 600;
    }

    .calendar-weekdays,
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 6px;
    }

    .calendar-weekdays div {
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        color: #6e7f72;
        padding: 6px 0;
    }

    .calendar-day {
        min-height: 56px;
        border-radius: 10px;
        padding: 8px;
        background: #f7faf8;
        border: 1px solid transparent;
        position: relative;
        font-size: 12px;
        color: #2a3f33;
    }

    .calendar-day.other-month {
        opacity: 0.5;
    }

    .calendar-day.has-reservation {
        border-color: #2db84f;
        background: #e9f8eb;
    }

    .calendar-day .day-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #2db84f;
        position: absolute;
        bottom: 8px;
        right: 8px;
    }

    .calendar-details {
        margin-top: 14px;
        padding: 12px;
        border-radius: 12px;
        background: #f7fbff;
        border: 1px solid #d8e7f2;
    }

    .calendar-details h4 {
        font-size: 13px;
        color: #1a7a3e;
        margin-bottom: 8px;
    }

    .calendar-details .detail-item {
        font-size: 12px;
        color: #3c4a3f;
        padding: 4px 0;
    }

    .date-picker-card {
        border: 1px solid #e8eee9;
        border-radius: 12px;
        padding: 12px;
        background: #fcfdfc;
    }

    .date-picker-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        gap: 8px;
    }

    .date-picker-nav {
        display: flex;
        gap: 6px;
    }

    .date-picker-nav button {
        border: none;
        background: #f0faf3;
        color: #1a7a3e;
        border-radius: 8px;
        width: 30px;
        height: 30px;
        cursor: pointer;
        font-weight: 700;
    }

    .date-picker-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 6px;
    }

    .date-picker-cell {
        border: 1px solid #e8eee9;
        border-radius: 8px;
        padding: 6px 0;
        text-align: center;
        font-size: 12px;
        cursor: pointer;
        color: #3c4a3f;
        background: white;
    }

    .date-picker-cell.other-month {
        opacity: 0.45;
    }

    .date-picker-cell.selected {
        background: #d4f5df;
        border-color: #2db84f;
        color: #1a7a3e;
        font-weight: 700;
    }

    .date-picker-cell.disabled {
        cursor: not-allowed;
        opacity: 0.45;
    }

    .selected-date-list {
        margin-top: 10px;
        padding: 10px 12px;
        background: #f7faf8;
        border-radius: 8px;
        font-size: 12px;
        color: #3c4a3f;
    }

    .selected-date-chip {
        display: inline-block;
        background: #e9f8eb;
        color: #1a7a3e;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 11px;
        margin: 3px 4px 0 0;
    }

    .date-picker-note {
        font-size: 11px;
        color: #6e7f72;
        margin-top: 6px;
    }

    @media (max-width: 992px) {
        .vehicle-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .user-main {
            margin-left: 0;
        }
        .form-row {
            grid-template-columns: 1fr;
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
                    🚐 Reserve a <strong>UCC Pickup Vehicle</strong> for transporting or item delivery needs.
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    Please fix the following:
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="vehicle-layout">
                <!-- Reservation Form -->
                <div class="panel-card">
                    <div class="panel-title">🚐 Reserve a Pickup Vehicle</div>

                    <form method="POST" action="{{ route('user.vehicle-reservations.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-row">
                            <div class="form-group">
                                <label>I am a</label>
                                <select name="requester_type" required>
                                    <option value="">Select...</option>
                                    <option value="student" {{ old('requester_type') == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="professor" {{ old('requester_type') == 'professor' ? 'selected' : '' }}>Professor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>From Campus (Origin)</label>
                                <select name="origin_campus_id" id="originCampusSelect" required onchange="loadVehiclesForCampus(this.value)">
                                    <option value="">Select campus...</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->id }}" {{ old('origin_campus_id') == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="vehicleSelectGroup" style="display: none;">
                            <label>Pickup Vehicle</label>
                            <select name="vehicle_id" id="vehicleSelect">
                                <option value="">Select a vehicle...</option>
                            </select>
                            <small class="form-hint" id="vehicleHint"></small>
                        </div>

                        <div class="form-group">
                            <label>Purpose of Reservation</label>
                            <select name="purpose" id="purposeSelect" required>
                                <option value="">Select purpose...</option>
                                <option value="transporting" {{ old('purpose') == 'transporting' ? 'selected' : '' }}>Transporting</option>
                                <option value="delivery" {{ old('purpose') == 'delivery' ? 'selected' : '' }}>Items Delivery</option>
                                <option value="other" {{ old('purpose') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="form-group" id="otherPurposeGroup" style="display: none;">
                            <label>Please specify purpose</label>
                            <input type="text" name="other_purpose" value="{{ old('other_purpose') }}" maxlength="255" placeholder="Specify your purpose">
                        </div>

                        <div class="form-group">
                            <label>Destination</label>
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="destination_type" value="campus" id="destTypeCampus" {{ old('destination_type', 'campus') == 'campus' ? 'checked' : '' }}>
                                    Within UCC Campus
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="destination_type" value="outside" id="destTypeOutside" {{ old('destination_type') == 'outside' ? 'checked' : '' }}>
                                    Outside Campus
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="destinationCampusGroup">
                            <label>Destination Campus</label>
                            <select name="destination_campus_id">
                                <option value="">Select campus...</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}" {{ old('destination_campus_id') == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="destinationLocationGroup" style="display: none;">
                            <label>Destination Location</label>
                            <input type="text" name="destination_location" value="{{ old('destination_location') }}" maxlength="255" placeholder="e.g. SM Caloocan, Barangay Hall 176...">
                        </div>

                        <div class="form-group">
                            <label>Trip Date(s)</label>
                            <div class="date-picker-card">
                                <div class="date-picker-header">
                                    <strong id="datePickerMonthLabel"></strong>
                                    <div class="date-picker-nav">
                                        <button type="button" onclick="changeTripDatePickerMonth(-1)">←</button>
                                        <button type="button" onclick="goToTripDatePickerToday()">Today</button>
                                        <button type="button" onclick="changeTripDatePickerMonth(1)">→</button>
                                    </div>
                                </div>
                                <div class="date-picker-grid" id="tripDatePickerGrid"></div>
                                <div class="date-picker-note">Click the calendar to select one or more dates. The selected dates will appear below.</div>
                                <div class="selected-date-list" id="selectedTripDatesList">No dates selected yet.</div>
                                <input type="hidden" name="trip_date" id="tripDate" value="{{ old('trip_date') }}">
                                <div id="tripDateInputs"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Pickup Time</label>
                            <input type="time" name="pickup_time" value="{{ old('pickup_time') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Additional Details (optional)</label>
                            <textarea name="notes" rows="3" placeholder="Any extra details the admin should know...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Attachment(s) (optional)</label>
                            <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                            <small class="form-hint">PDF, JPG, or PNG. Max 15MB per file.</small>
                        </div>

                        <button type="submit" class="btn-primary">🚀 Submit Reservation Request</button>
                        <small class="form-hint" style="text-align: center; display: block; margin-top: 10px;">Your request will be marked as <strong>Pending</strong> until reviewed by an Administrator.</small>
                    </form>
                </div>

                <!-- My Requests -->
                <div class="panel-card">
                    <div class="panel-title">📋 My Pickup Vehicle Requests</div>

                    <div class="calendar-card">
                        <div class="calendar-header">
                            <div class="calendar-month" id="vehicleCalendarMonth"></div>
                            <div class="calendar-nav">
                                <button type="button" onclick="changeVehicleMonth(-1)">←</button>
                                <button type="button" onclick="goToVehicleToday()">Today</button>
                                <button type="button" onclick="changeVehicleMonth(1)">→</button>
                            </div>
                        </div>
                        <div class="calendar-weekdays">
                            <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                        </div>
                        <div class="calendar-grid" id="vehicleCalendarDays"></div>
                        <div class="calendar-details" id="vehicleCalendarDetails">
                            <h4>Select a highlighted date to see pickup vehicle reservations.</h4>
                        </div>
                    </div>

                    @forelse($myReservations as $res)
                        <div class="request-card">
                            <div class="request-card-header">
                                <div>
                                    <div class="request-code">#{{ $res->reservation_code }}</div>
                                    <div class="request-purpose">{{ $res->purpose_label }}</div>
                                </div>
                                <span class="status-badge status-{{ $res->status }}">{{ strtoupper($res->status) }}</span>
                            </div>
                            <div class="request-detail-row">
                                <span>From:</span>
                                <span>{{ $res->originCampus->name ?? 'N/A' }}</span>
                            </div>
                            <div class="request-detail-row">
                                <span>Vehicle:</span>
                                <span>{{ $res->vehicle_label }}</span>
                            </div>
                            <div class="request-detail-row">
                                <span>To:</span>
                                <span>{{ $res->destination_label }}</span>
                            </div>
                            <div class="request-detail-row">
                                <span>Trip Date:</span>
                                <span>{{ $res->trip_dates_display }}</span>
                            </div>
                            @if(count($res->trip_dates) > 1)
                                <div class="request-detail-row" style="display:block;">
                                    <span>Selected Dates:</span>
                                    <div style="margin-top: 4px;">
                                        @foreach($res->trip_dates as $tripDate)
                                            <span class="selected-date-chip">{{ \Carbon\Carbon::parse($tripDate)->format('M d, Y') }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="request-detail-row">
                                <span>Pickup Time:</span>
                                <span>{{ \Carbon\Carbon::parse($res->pickup_time)->format('g:i A') }}</span>
                            </div>
                            @if($res->status === 'rejected' && $res->remarks)
                                <div class="request-detail-row" style="color: #dc2626;">
                                    <span>Reason:</span>
                                    <span>{{ $res->remarks }}</span>
                                </div>
                            @endif
                            @if($res->status === 'approved')
                                <div style="margin-top: 10px; text-align: right;">
                                    <a href="{{ route('report.vehicle.single', $res->id) }}" target="_blank" style="font-size: 12px; font-weight: 700; color: #1a7a3e; text-decoration: none;">📄 Generate Report</a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="empty-state">🚐 No pickup vehicle requests yet.</div>
                    @endforelse

                    <div class="pagination-wrap">
                        {{ $myReservations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const purposeSelect = document.getElementById('purposeSelect');
    const otherPurposeGroup = document.getElementById('otherPurposeGroup');
    const destTypeCampus = document.getElementById('destTypeCampus');
    const destTypeOutside = document.getElementById('destTypeOutside');
    const destinationCampusGroup = document.getElementById('destinationCampusGroup');
    const destinationLocationGroup = document.getElementById('destinationLocationGroup');
    const tripDate = document.getElementById('tripDate');

    function toggleOtherPurpose() {
        otherPurposeGroup.style.display = purposeSelect.value === 'other' ? 'block' : 'none';
    }

    function toggleDestinationFields() {
        if (destTypeOutside.checked) {
            destinationCampusGroup.style.display = 'none';
            destinationLocationGroup.style.display = 'block';
        } else {
            destinationCampusGroup.style.display = 'block';
            destinationLocationGroup.style.display = 'none';
        }
    }

    purposeSelect.addEventListener('change', toggleOtherPurpose);
    destTypeCampus.addEventListener('change', toggleDestinationFields);
    destTypeOutside.addEventListener('change', toggleDestinationFields);

    toggleOtherPurpose();
    toggleDestinationFields();

    const oldVehicleId = @json(old('vehicle_id', ''));

    function loadVehiclesForCampus(campusId) {
        const group = document.getElementById('vehicleSelectGroup');
        const select = document.getElementById('vehicleSelect');
        const hint = document.getElementById('vehicleHint');

        if (!campusId) {
            group.style.display = 'none';
            select.innerHTML = '<option value="">Select a vehicle...</option>';
            return;
        }

        group.style.display = 'block';
        select.innerHTML = '<option value="">Loading vehicles...</option>';
        hint.textContent = '';

        fetch(`/api/campuses/${campusId}/vehicles`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.vehicles.length > 0) {
                    select.innerHTML = '<option value="">Select a vehicle...</option>' +
                        data.vehicles.map(v => `<option value="${v.id}" ${String(v.id) === String(oldVehicleId) ? 'selected' : ''}>${v.name}${v.plate_number ? ' (' + v.plate_number + ')' : ''}</option>`).join('');
                    hint.textContent = '';
                } else {
                    select.innerHTML = '<option value="">No vehicles available for this campus yet</option>';
                    hint.textContent = 'No vehicles have been registered for this campus yet. Your request can still be submitted — the Admin will assign a vehicle upon approval.';
                }
            })
            .catch(() => {
                select.innerHTML = '<option value="">Unable to load vehicles</option>';
            });
    }

    if (document.getElementById('originCampusSelect').value) {
        loadVehiclesForCampus(document.getElementById('originCampusSelect').value);
    }

    const initialTripDates = @json(old('trip_dates', [old('trip_date')]));
    let selectedTripDates = (Array.isArray(initialTripDates) ? initialTripDates : [initialTripDates]).filter(Boolean);
    let tripDatePickerMonth = new Date().getMonth();
    let tripDatePickerYear = new Date().getFullYear();
    let bookedTripDates = {};

    function formatTripDatePickerMonthTitle(year, month) {
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return `${monthNames[month]} ${year}`;
    }

    function loadBookedTripDates() {
        fetch(`/availability/vehicles?campus_id=all&month=${tripDatePickerMonth + 1}&year=${tripDatePickerYear}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bookedTripDates = data.dates || {};
                }
                renderTripDatePicker();
            })
            .catch(() => {
                renderTripDatePicker();
            });
    }

    function renderTripDatePicker() {
        const firstDay = new Date(tripDatePickerYear, tripDatePickerMonth, 1);
        const lastDay = new Date(tripDatePickerYear, tripDatePickerMonth + 1, 0);
        const startingDay = firstDay.getDay();
        const daysInMonth = lastDay.getDate();
        const prevMonthLastDay = new Date(tripDatePickerYear, tripDatePickerMonth, 0).getDate();
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        let calendarHTML = '';
        let dayCounter = 1;

        for (let i = 0; i < 42; i++) {
            let dayNumber = '';
            let dateStr = '';
            let isCurrentMonth = true;
            let isDisabled = false;

            if (i < startingDay) {
                dayNumber = prevMonthLastDay - (startingDay - i) + 1;
                const prevDate = new Date(tripDatePickerYear, tripDatePickerMonth - 1, dayNumber);
                dateStr = formatVehicleDateKey(prevDate);
                isCurrentMonth = false;
            } else if (dayCounter > daysInMonth) {
                dayNumber = dayCounter - daysInMonth;
                const nextDate = new Date(tripDatePickerYear, tripDatePickerMonth + 1, dayNumber);
                dateStr = formatVehicleDateKey(nextDate);
                isCurrentMonth = false;
                dayCounter++;
            } else {
                const thisDate = new Date(tripDatePickerYear, tripDatePickerMonth, dayCounter);
                dayNumber = dayCounter;
                dateStr = formatVehicleDateKey(thisDate);
                dayCounter++;
                const compareDate = new Date(thisDate);
                compareDate.setHours(0, 0, 0, 0);
                isDisabled = compareDate < today;
            }

            const isSelected = selectedTripDates.includes(dateStr);
            const isBooked = isCurrentMonth && Boolean(bookedTripDates[dateStr] && bookedTripDates[dateStr].length > 0);
            const cellClasses = ['date-picker-cell', isCurrentMonth ? '' : 'other-month', isSelected ? 'selected' : '', isDisabled || isBooked ? 'disabled' : ''];
            const clickHandler = isCurrentMonth && !isDisabled && !isBooked ? `toggleTripDate('${dateStr}')` : '';
            calendarHTML += `
                <div class="${cellClasses.filter(Boolean).join(' ')}" onclick="${clickHandler}">
                    ${dayNumber}
                </div>
            `;
        }

        document.getElementById('tripDatePickerGrid').innerHTML = calendarHTML;
        document.getElementById('datePickerMonthLabel').textContent = formatTripDatePickerMonthTitle(tripDatePickerYear, tripDatePickerMonth);
        updateTripDateSummary();
    }

    function updateTripDateSummary() {
        const hiddenTripDate = document.getElementById('tripDate');
        const inputsContainer = document.getElementById('tripDateInputs');
        const details = document.getElementById('selectedTripDatesList');

        hiddenTripDate.value = selectedTripDates[0] || '';
        inputsContainer.innerHTML = '';

        selectedTripDates.forEach(date => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'trip_dates[]';
            input.value = date;
            inputsContainer.appendChild(input);
        });

        if (selectedTripDates.length === 0) {
            details.innerHTML = 'No dates selected yet.';
            return;
        }

        details.innerHTML = selectedTripDates.map(date => `<span class="selected-date-chip">${new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>`).join('');
    }

    function toggleTripDate(dateKey) {
        if (selectedTripDates.includes(dateKey)) {
            selectedTripDates = selectedTripDates.filter(item => item !== dateKey);
        } else {
            selectedTripDates = [...selectedTripDates, dateKey].sort((a, b) => new Date(a) - new Date(b));
        }
        updateTripDateSummary();
        renderTripDatePicker();
    }

    function changeTripDatePickerMonth(direction) {
        tripDatePickerMonth += direction;
        if (tripDatePickerMonth < 0) {
            tripDatePickerMonth = 11;
            tripDatePickerYear -= 1;
        } else if (tripDatePickerMonth > 11) {
            tripDatePickerMonth = 0;
            tripDatePickerYear += 1;
        }
        loadBookedTripDates();
    }

    function goToTripDatePickerToday() {
        const today = new Date();
        tripDatePickerMonth = today.getMonth();
        tripDatePickerYear = today.getFullYear();
        loadBookedTripDates();
    }

    let vehicleCalendarDate = new Date();
    let vehicleCalendarMonth = vehicleCalendarDate.getMonth();
    let vehicleCalendarYear = vehicleCalendarDate.getFullYear();
    let selectedVehicleDate = null;
    let vehicleReservationsByDate = {};

    function formatVehicleMonthTitle(year, month) {
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return `${monthNames[month]} ${year}`;
    }

    function formatVehicleDateKey(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function loadVehicleCalendar() {
        fetch(`/availability/vehicles?campus_id=all&month=${vehicleCalendarMonth + 1}&year=${vehicleCalendarYear}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    vehicleReservationsByDate = data.dates;
                    renderVehicleCalendar();
                }
            })
            .catch(error => console.error('Failed to load vehicle calendar:', error));
    }

    function renderVehicleCalendar() {
        const firstDay = new Date(vehicleCalendarYear, vehicleCalendarMonth, 1);
        const lastDay = new Date(vehicleCalendarYear, vehicleCalendarMonth + 1, 0);
        const startingDay = firstDay.getDay();
        const daysInMonth = lastDay.getDate();
        const prevMonthLastDay = new Date(vehicleCalendarYear, vehicleCalendarMonth, 0).getDate();
        const today = new Date();
        today.setHours(0,0,0,0);

        let calendarHTML = '';
        let dayCounter = 1;

        for (let i = 0; i < 42; i++) {
            let dayNumber = '';
            let dateStr = '';
            let isCurrentMonth = true;
            let hasReservation = false;

            if (i < startingDay) {
                dayNumber = prevMonthLastDay - (startingDay - i) + 1;
                const prevDate = new Date(vehicleCalendarYear, vehicleCalendarMonth - 1, dayNumber);
                dateStr = formatVehicleDateKey(prevDate);
                isCurrentMonth = false;
            } else if (dayCounter > daysInMonth) {
                dayNumber = dayCounter - daysInMonth;
                const nextDate = new Date(vehicleCalendarYear, vehicleCalendarMonth + 1, dayNumber);
                dateStr = formatVehicleDateKey(nextDate);
                isCurrentMonth = false;
                dayCounter++;
            } else {
                const thisDate = new Date(vehicleCalendarYear, vehicleCalendarMonth, dayCounter);
                dayNumber = dayCounter;
                dateStr = formatVehicleDateKey(thisDate);
                dayCounter++;
                hasReservation = Array.isArray(vehicleReservationsByDate[dateStr]) && vehicleReservationsByDate[dateStr].length > 0;
            }

            calendarHTML += `
                <div class="calendar-day ${isCurrentMonth ? '' : 'other-month'} ${hasReservation ? 'has-reservation' : ''}" onclick="${isCurrentMonth ? `selectVehicleDate('${dateStr}')` : ''}">
                    <div>${dayNumber}</div>
                    ${hasReservation ? '<div class="day-dot"></div>' : ''}
                </div>
            `;
        }

        document.getElementById('vehicleCalendarMonth').textContent = formatVehicleMonthTitle(vehicleCalendarYear, vehicleCalendarMonth);
        document.getElementById('vehicleCalendarDays').innerHTML = calendarHTML;

        if (!selectedVehicleDate) {
            const todayKey = formatVehicleDateKey(today);
            selectVehicleDate(todayKey);
        }
    }

    function selectVehicleDate(dateKey) {
        selectedVehicleDate = dateKey;
        const details = document.getElementById('vehicleCalendarDetails');
        fetch(`/availability/vehicles/day?campus_id=all&date=${dateKey}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success || !data.reservations || data.reservations.length === 0) {
                    details.innerHTML = '<h4>No pickup vehicle reservations</h4><div class="detail-item">This day is open for pickup vehicle requests.</div>';
                    return;
                }

                const html = data.reservations.map(item => `
                    <div class="detail-item"><strong>${item.time}</strong> — ${item.code} (${item.purpose})</div>
                `).join('');
                details.innerHTML = `<h4>${new Date(dateKey).toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}</h4>${html}`;
            })
            .catch(error => {
                console.error('Failed to load vehicle day details:', error);
            });
    }

    function changeVehicleMonth(direction) {
        vehicleCalendarMonth += direction;
        if (vehicleCalendarMonth < 0) {
            vehicleCalendarMonth = 11;
            vehicleCalendarYear -= 1;
        } else if (vehicleCalendarMonth > 11) {
            vehicleCalendarMonth = 0;
            vehicleCalendarYear += 1;
        }
        selectedVehicleDate = null;
        loadVehicleCalendar();
    }

    function goToVehicleToday() {
        vehicleCalendarDate = new Date();
        vehicleCalendarMonth = vehicleCalendarDate.getMonth();
        vehicleCalendarYear = vehicleCalendarDate.getFullYear();
        selectedVehicleDate = null;
        loadVehicleCalendar();
    }

    loadBookedTripDates();
    loadVehicleCalendar();
</script>
@endsection