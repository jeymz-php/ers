@extends('layouts.admin')

@section('title', 'Edit Vehicle Reservation')
@section('page-title', 'Edit Pickup Vehicle Reservation')

@section('content')
<style>
    .settings-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .settings-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a7a3e;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8eee9;
    }

    .form-group { margin-bottom: 20px; }

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
        padding: 10px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        resize: vertical;
    }

    .requester-readonly {
        background: #f7faf8;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 13px;
        color: #3c4a3f;
    }

    .requester-readonly strong {
        color: #1a7a3e;
    }

    .radio-group { display: flex; gap: 15px; flex-wrap: wrap; }

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
    }

    .radio-option input { width: auto; accent-color: #1a7a3e; }

    .btn-primary {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
    }

    .btn-cancel-link {
        display: block;
        text-align: center;
        margin-top: 12px;
        color: #6e7f72;
        text-decoration: none;
        font-size: 13px;
    }

    .alert-error {
        background: #fef2f2;
        color: #dc2626;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
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

    .existing-attachment-item {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f7faf8;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 6px;
        font-size: 12px;
    }

    .existing-attachment-item a {
        color: #1a7a3e;
        text-decoration: none;
        flex: 1;
    }

    .existing-attachment-item input {
        width: auto;
    }

    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
    }
</style>

<div class="settings-container">
    <div class="settings-card">
        <div class="card-title">✏️ Edit Pickup Vehicle Reservation — #{{ $reservation->reservation_code }}</div>

        @if($errors->any())
            <div class="alert-error">
                <ul style="margin-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label>Requester</label>
            <div class="requester-readonly">
                <strong>{{ $reservation->user->name ?? 'Unknown' }}</strong> ({{ $reservation->user->email ?? 'N/A' }})
                <br><small>The requester cannot be changed. Delete and create a new reservation if this needs to be reassigned.</small>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.vehicle-reservations.update', $reservation->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Requester Type</label>
                <select name="requester_type" required>
                    <option value="student" {{ old('requester_type', $reservation->requester_type) == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="professor" {{ old('requester_type', $reservation->requester_type) == 'professor' ? 'selected' : '' }}>Professor</option>
                    <option value="admin" {{ old('requester_type', $reservation->requester_type) == 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>

            <div class="form-group">
                <label>From Campus (Origin)</label>
                <select name="origin_campus_id" id="originCampusSelect" required onchange="loadVehiclesForCampus(this.value)">
                    <option value="">Select campus...</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ old('origin_campus_id', $reservation->origin_campus_id) == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" id="vehicleSelectGroup" style="display: none;">
                <label>Pickup Vehicle</label>
                <select name="vehicle_id" id="vehicleSelect">
                    <option value="">Select a vehicle...</option>
                </select>
                <small style="display:block; margin-top:4px; color:#6e7f72; font-size:11px;" id="vehicleHint"></small>
            </div>

            <div class="form-group">
                <label>Purpose of Reservation</label>
                <select name="purpose" id="purposeSelect" required>
                    <option value="">Select purpose...</option>
                    <option value="transporting" {{ old('purpose', $reservation->purpose) == 'transporting' ? 'selected' : '' }}>Transporting</option>
                    <option value="delivery" {{ old('purpose', $reservation->purpose) == 'delivery' ? 'selected' : '' }}>Items Delivery</option>
                    <option value="other" {{ old('purpose', $reservation->purpose) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="form-group" id="otherPurposeGroup" style="display: none;">
                <label>Please specify purpose</label>
                <input type="text" name="other_purpose" value="{{ old('other_purpose', $reservation->other_purpose) }}" maxlength="255">
            </div>

            <div class="form-group">
                <label>Destination</label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="destination_type" value="campus" id="destTypeCampus" {{ old('destination_type', $reservation->destination_type) == 'campus' ? 'checked' : '' }}>
                        Within UCC Campus
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="destination_type" value="outside" id="destTypeOutside" {{ old('destination_type', $reservation->destination_type) == 'outside' ? 'checked' : '' }}>
                        Outside Campus
                    </label>
                </div>
            </div>

            <div class="form-group" id="destinationCampusGroup">
                <label>Destination Campus</label>
                <select name="destination_campus_id">
                    <option value="">Select campus...</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ old('destination_campus_id', $reservation->destination_campus_id) == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" id="destinationLocationGroup" style="display: none;">
                <label>Destination Location</label>
                <input type="text" name="destination_location" value="{{ old('destination_location', $reservation->destination_location) }}" maxlength="255" placeholder="e.g. SM Caloocan, Barangay Hall 176...">
            </div>

            <div class="form-group">
                <label>Trip Date(s)</label>
                <div class="date-picker-card">
                    <div class="date-picker-header">
                        <strong id="adminDatePickerMonthLabel"></strong>
                        <div class="date-picker-nav">
                            <button type="button" onclick="changeAdminTripDatePickerMonth(-1)">←</button>
                            <button type="button" onclick="goToAdminTripDatePickerToday()">Today</button>
                            <button type="button" onclick="changeAdminTripDatePickerMonth(1)">→</button>
                        </div>
                    </div>
                    <div class="date-picker-grid" id="adminTripDatePickerGrid"></div>
                    <div class="selected-date-list" id="adminSelectedTripDatesList">No dates selected yet.</div>
                    <input type="hidden" name="trip_date" id="adminTripDate" value="{{ old('trip_date', $reservation->trip_date->format('Y-m-d')) }}">
                    <div id="adminTripDateInputs"></div>
                </div>
            </div>

            <div class="form-group">
                <label>Pickup Time</label>
                <input type="time" name="pickup_time" value="{{ old('pickup_time', $reservation->pickup_time) }}" required>
            </div>

            <div class="form-group">
                <label>Additional Details (optional)</label>
                <textarea name="notes" rows="3">{{ old('notes', $reservation->notes) }}</textarea>
            </div>

            @if(!empty($reservation->attachments))
            <div class="form-group">
                <label>Existing Attachment(s)</label>
                @foreach($reservation->attachments as $attachment)
                    <div class="existing-attachment-item">
                        <input type="checkbox" name="remove_attachments[]" value="{{ $attachment }}" id="attach-{{ $loop->index }}">
                        <a href="{{ Storage::url($attachment) }}" target="_blank">📄 {{ basename($attachment) }}</a>
                        <label for="attach-{{ $loop->index }}" style="margin:0; color:#dc2626; font-weight:400; cursor:pointer;">Remove</label>
                    </div>
                @endforeach
            </div>
            @endif

            <div class="form-group">
                <label>Add More Attachment(s) (optional)</label>
                <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <button type="submit" class="btn-primary">💾 Update Reservation</button>
        </form>
        <a href="{{ route('admin.vehicle-reservations.show', $reservation->id) }}" class="btn-cancel-link">← Cancel and go back</a>
    </div>
</div>

<script>
    const purposeSelect = document.getElementById('purposeSelect');
    const otherPurposeGroup = document.getElementById('otherPurposeGroup');
    const destTypeCampus = document.getElementById('destTypeCampus');
    const destTypeOutside = document.getElementById('destTypeOutside');
    const destinationCampusGroup = document.getElementById('destinationCampusGroup');
    const destinationLocationGroup = document.getElementById('destinationLocationGroup');

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

    const currentVehicleId = @json(old('vehicle_id', $reservation->vehicle_id));

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

        fetch(`/admin/vehicles/by-campus/${campusId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.vehicles.length > 0) {
                    select.innerHTML = '<option value="">Select a vehicle...</option>' +
                        data.vehicles.map(v => `<option value="${v.id}" ${String(v.id) === String(currentVehicleId) ? 'selected' : ''}>${v.name}${v.plate_number ? ' (' + v.plate_number + ')' : ''}</option>`).join('');
                } else {
                    select.innerHTML = '<option value="">No vehicles available for this campus yet</option>';
                    hint.textContent = 'No vehicles registered for this campus yet. Use "Add Vehicle" on the Vehicle Reservations page to add one.';
                }
            })
            .catch(() => {
                select.innerHTML = '<option value="">Unable to load vehicles</option>';
            });
    }

    if (document.getElementById('originCampusSelect').value) {
        loadVehiclesForCampus(document.getElementById('originCampusSelect').value);
    }

    const reservationId = {{ $reservation->id }};
    let selectedAdminTripDates = @json(old('trip_dates', $reservation->trip_dates));
    selectedAdminTripDates = (Array.isArray(selectedAdminTripDates) ? selectedAdminTripDates : [selectedAdminTripDates]).filter(Boolean);
    let adminTripDatePickerMonth = new Date().getMonth();
    let adminTripDatePickerYear = new Date().getFullYear();
    let bookedAdminTripDates = {};

    if (selectedAdminTripDates.length > 0) {
        const firstSelected = new Date(selectedAdminTripDates[0]);
        adminTripDatePickerMonth = firstSelected.getMonth();
        adminTripDatePickerYear = firstSelected.getFullYear();
    }

    function formatAdminTripDatePickerMonthTitle(year, month) {
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return `${monthNames[month]} ${year}`;
    }

    function formatAdminTripDateKey(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function loadBookedAdminTripDates() {
        fetch(`/availability/vehicles?campus_id=all&month=${adminTripDatePickerMonth + 1}&year=${adminTripDatePickerYear}&exclude_id=${reservationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bookedAdminTripDates = data.dates || {};
                }
                renderAdminTripDatePicker();
            })
            .catch(() => {
                renderAdminTripDatePicker();
            });
    }

    function renderAdminTripDatePicker() {
        const firstDay = new Date(adminTripDatePickerYear, adminTripDatePickerMonth, 1);
        const lastDay = new Date(adminTripDatePickerYear, adminTripDatePickerMonth + 1, 0);
        const startingDay = firstDay.getDay();
        const daysInMonth = lastDay.getDate();
        const prevMonthLastDay = new Date(adminTripDatePickerYear, adminTripDatePickerMonth, 0).getDate();

        let calendarHTML = '';
        let dayCounter = 1;

        for (let i = 0; i < 42; i++) {
            let dayNumber = '';
            let dateStr = '';
            let isCurrentMonth = true;

            if (i < startingDay) {
                dayNumber = prevMonthLastDay - (startingDay - i) + 1;
                const prevDate = new Date(adminTripDatePickerYear, adminTripDatePickerMonth - 1, dayNumber);
                dateStr = formatAdminTripDateKey(prevDate);
                isCurrentMonth = false;
            } else if (dayCounter > daysInMonth) {
                dayNumber = dayCounter - daysInMonth;
                const nextDate = new Date(adminTripDatePickerYear, adminTripDatePickerMonth + 1, dayNumber);
                dateStr = formatAdminTripDateKey(nextDate);
                isCurrentMonth = false;
                dayCounter++;
            } else {
                const thisDate = new Date(adminTripDatePickerYear, adminTripDatePickerMonth, dayCounter);
                dayNumber = dayCounter;
                dateStr = formatAdminTripDateKey(thisDate);
                dayCounter++;
            }

            const isSelected = selectedAdminTripDates.includes(dateStr);
            const isBooked = isCurrentMonth && !isSelected && Boolean(bookedAdminTripDates[dateStr] && bookedAdminTripDates[dateStr].length > 0);
            const cellClasses = ['date-picker-cell', isCurrentMonth ? '' : 'other-month', isSelected ? 'selected' : '', isBooked ? 'disabled' : ''];
            const clickHandler = isBooked ? '' : `toggleAdminTripDate('${dateStr}')`;
            calendarHTML += `<div class="${cellClasses.filter(Boolean).join(' ')}" onclick="${clickHandler}">${dayNumber}</div>`;
        }

        document.getElementById('adminTripDatePickerGrid').innerHTML = calendarHTML;
        document.getElementById('adminDatePickerMonthLabel').textContent = formatAdminTripDatePickerMonthTitle(adminTripDatePickerYear, adminTripDatePickerMonth);
        updateAdminTripDateSummary();
    }

    function updateAdminTripDateSummary() {
        const hiddenTripDate = document.getElementById('adminTripDate');
        const inputsContainer = document.getElementById('adminTripDateInputs');
        const details = document.getElementById('adminSelectedTripDatesList');

        hiddenTripDate.value = selectedAdminTripDates[0] || '';
        inputsContainer.innerHTML = '';

        selectedAdminTripDates.forEach(date => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'trip_dates[]';
            input.value = date;
            inputsContainer.appendChild(input);
        });

        if (selectedAdminTripDates.length === 0) {
            details.innerHTML = 'No dates selected yet.';
            return;
        }

        details.innerHTML = selectedAdminTripDates.map(date => `<span class="selected-date-chip">${new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>`).join('');
    }

    function toggleAdminTripDate(dateKey) {
        if (selectedAdminTripDates.includes(dateKey)) {
            selectedAdminTripDates = selectedAdminTripDates.filter(item => item !== dateKey);
        } else {
            selectedAdminTripDates = [...selectedAdminTripDates, dateKey].sort((a, b) => new Date(a) - new Date(b));
        }
        updateAdminTripDateSummary();
        renderAdminTripDatePicker();
    }

    function changeAdminTripDatePickerMonth(direction) {
        adminTripDatePickerMonth += direction;
        if (adminTripDatePickerMonth < 0) {
            adminTripDatePickerMonth = 11;
            adminTripDatePickerYear -= 1;
        } else if (adminTripDatePickerMonth > 11) {
            adminTripDatePickerMonth = 0;
            adminTripDatePickerYear += 1;
        }
        loadBookedAdminTripDates();
    }

    function goToAdminTripDatePickerToday() {
        const today = new Date();
        adminTripDatePickerMonth = today.getMonth();
        adminTripDatePickerYear = today.getFullYear();
        loadBookedAdminTripDates();
    }

    toggleOtherPurpose();
    toggleDestinationFields();
    loadBookedAdminTripDates();
</script>
@endsection