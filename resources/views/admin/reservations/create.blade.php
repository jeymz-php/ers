@extends('layouts.admin')

@section('title', 'Add Reservation')
@section('page-title', 'Add Reservation')

@section('content')
<style>
    .reservation-create {
        max-width: 1300px;
        margin: 0 auto;
        background: #f1f6f3;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 18px 60px rgba(23, 69, 34, 0.12);
    }

    .reservation-header {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        padding: 24px 32px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .reservation-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
    }

    .reservation-header p {
        margin: 8px 0 0;
        color: rgba(255,255,255,0.85);
        max-width: 640px;
    }

    .reservation-body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        padding: 28px;
    }

    .panel-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(17, 65, 26, 0.08);
    }

    .calendar-card {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .calendar-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .calendar-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a7a3e;
    }

    .calendar-controls {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .calendar-controls button {
        border: 1px solid #d8e6d6;
        background: white;
        color: #1a7a3e;
        padding: 10px 14px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 700;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 8px;
    }

    .weekday {
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        color: #1a7a3e;
    }

    .calendar-day {
        min-height: 74px;
        background: #f7faf8;
        border: 1px solid #dbeada;
        border-radius: 14px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .calendar-day:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(28, 128, 72, 0.08);
    }

    .calendar-day.booked {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #9b1c1c;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .calendar-day.selected {
        border-color: #2db84f;
        background: #d4f5df;
    }

    .calendar-day.other-month {
        opacity: 0.35;
        color: #68776a;
    }

    .day-number {
        font-weight: 700;
        font-size: 14px;
    }

    .day-note {
        font-size: 11px;
        color: #4b5f4a;
    }

    .note-box {
        border-radius: 14px;
        background: #f0faf1;
        border: 1px solid #d8e6d6;
        padding: 16px;
        font-size: 13px;
        color: #4b5f4a;
    }

    .form-card {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .venue-summary {
        background: #f4fdf4;
        border: 1px solid #d4ecd9;
        border-radius: 20px;
        padding: 18px 20px;
        color: #1a7a3e;
    }

    .venue-summary h2 {
        margin: 0 0 8px;
        font-size: 18px;
    }

    .venue-summary p {
        margin: 4px 0;
        font-size: 13px;
        color: #4f6c55;
    }

    .field-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .field-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .field-group label {
        color: #1a7a3e;
        font-weight: 700;
        font-size: 13px;
    }

    .field-group input,
    .field-group select,
    .field-group textarea {
        width: 100%;
        border: 1px solid #d8e6d6;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 14px;
        background: #fbfdf9;
        color: #2f4334;
    }

    .field-group textarea {
        min-height: 150px;
        resize: vertical;
    }

    .selected-dates-box {
        background: #e8f7e7;
        border: 1px solid #d4ecd9;
        border-radius: 14px;
        padding: 18px;
        color: #25502f;
        font-size: 13px;
        min-height: 82px;
        word-break: break-word;
    }

    .form-actions {
        display: flex;
        gap: 16px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-secondary,
    .btn-primary {
        border: none;
        border-radius: 14px;
        padding: 14px 28px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-secondary {
        background: #e9f2ea;
        color: #1a7a3e;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
    }

    .error-box {
        background: #fdecea;
        border: 1px solid #f5c6cb;
        color: #842029;
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 20px;
    }

    @media (max-width: 1080px) {
        .reservation-body {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 720px) {
        .reservation-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="reservation-create">
    <div class="reservation-header">
        <div>
            <h1>Schedule of Events</h1>
            <p>Use the calendar to choose reservation dates and complete the event details on the right. Admin and Super Admin entries are approved immediately.</p>
        </div>
        <a href="{{ route('admin.reservations.index') }}" class="btn-secondary">Back to Reservations</a>
    </div>

    @if($errors->any())
        <div class="error-box">
            <strong>There were some problems with your submission:</strong>
            <ul style="margin: 12px 0 0 18px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.reservations.store') }}" class="reservation-body">
        @csrf
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
        <input type="hidden" id="selected_dates" name="event_dates" value="{{ old('event_dates') }}">

        <div class="panel-card calendar-card">
            <div class="calendar-toolbar">
                <div class="calendar-title"><strong id="calendarMonthLabel"></strong></div>
                <div class="calendar-controls">
                    <button type="button" onclick="previousMonth()">←</button>
                    <button type="button" onclick="goToToday()">Today</button>
                    <button type="button" onclick="nextMonth()">→</button>
                </div>
            </div>

            <div class="calendar-grid" id="calendarWeekdays"></div>
            <div class="calendar-grid" id="calendarDays"></div>

            <div class="note-box">
                Click on a future date to request a reservation. Red dates are booked.
            </div>
        </div>

        <div class="form-card">
            <div class="venue-summary" id="venueSummary">
                <h2>Selected Venue Details</h2>
                <p id="campusName">Campus: —</p>
                <p id="venueName">Venue: No venue selected yet</p>
                <p id="venueInfo">Capacity: — | Type: —</p>
            </div>

            <div class="field-grid">
                <div class="field-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="{{ auth()->user()->name }}" readonly>
                </div>
                <div class="field-group">
                    <label for="user_type">I am a</label>
                    <select id="user_type" name="user_type" required>
                        <option value="student" {{ old('user_type') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="professor" {{ old('user_type') === 'professor' ? 'selected' : '' }}>Professor</option>
                        <option value="admin" {{ old('user_type') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
            </div>

            <div class="field-grid">
                <div class="field-group">
                    <label for="department">Department (Optional)</label>
                    <input type="text" id="department" name="department" value="{{ old('department') }}" placeholder="e.g., College of Computer Studies">
                </div>
                <div class="field-group">
                    <label for="event_name">Event Name</label>
                    <input type="text" id="event_name" name="event_name" value="{{ old('event_name') }}" placeholder="Enter event name" required>
                </div>
            </div>

            <div class="field-group">
                <label for="event_objectives">Event Objectives</label>
                <textarea id="event_objectives" name="event_objectives" placeholder="Describe your event objectives...">{{ old('event_objectives') }}</textarea>
            </div>

            <div class="field-group">
                <label>Reservation Dates</label>
                <div class="selected-dates-box" id="selectedDatesDisplay">No dates selected. Click on dates in the calendar.</div>
            </div>

            <div class="field-grid">
                <div class="field-group">
                    <label for="start_time">Event Start Time</label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                </div>
                <div class="field-group">
                    <label for="end_time">Event End Time</label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                </div>
            </div>

            <div class="field-group">
                <label for="campus_id">Campus</label>
                <select id="campus_id" name="campus_id" required onchange="updateEstablishments()">
                    <option value="">Select campus</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field-group">
                <label for="establishment_id">Venue</label>
                <select id="establishment_id" name="establishment_id" required onchange="updateVenueSummary()">
                    <option value="">Select venue</option>
                    @foreach($campuses as $campus)
                        @foreach($campus->establishments as $est)
                            <option value="{{ $est->id }}" data-campus="{{ $campus->id }}" data-name="{{ $est->name }}" data-capacity="{{ $est->capacity }}" data-type="{{ $est->type }}" {{ old('establishment_id') == $est->id ? 'selected' : '' }}>
                                {{ $campus->name }} / {{ $est->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.reservations.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Create Reservation</button>
            </div>
        </div>
    </form>
</div>

<script>
    const initialDatesValue = "{{ old('event_dates', '') }}".trim();
    const selectedDates = initialDatesValue ? initialDatesValue.split(',').map(date => date.trim()).filter(Boolean) : [];
    const currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    const venueSelect = document.getElementById('establishment_id');
    const allVenueOptions = Array.from(venueSelect.querySelectorAll('option')).map(option => ({
        value: option.value,
        text: option.textContent,
        campus: option.dataset.campus,
        name: option.dataset.name,
        capacity: option.dataset.capacity,
        type: option.dataset.type,
        selected: option.selected
    }));

    function getMonthName(month) {
        return new Date(currentYear, month).toLocaleString('default', { month: 'long' });
    }

    function formatDisplayDate(date) {
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function updateSelectedDatesDisplay() {
        const display = document.getElementById('selectedDatesDisplay');
        const hiddenInput = document.getElementById('selected_dates');

        if (selectedDates.length === 0) {
            display.textContent = 'No dates selected. Click on dates in the calendar.';
            hiddenInput.value = '';
        } else {
            display.innerHTML = selectedDates.map(date => `<div>${formatDisplayDate(new Date(date))}</div>`).join('');
            hiddenInput.value = selectedDates.join(',');
        }
    }

    function renderCalendar() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const weekdaysContainer = document.getElementById('calendarWeekdays');
        const daysContainer = document.getElementById('calendarDays');
        const monthLabel = document.getElementById('calendarMonthLabel');

        weekdaysContainer.innerHTML = '';
        daysContainer.innerHTML = '';
        monthLabel.textContent = `${getMonthName(currentMonth)} ${currentYear}`;

        const weekdayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        weekdayNames.forEach(name => {
            const weekday = document.createElement('div');
            weekday.className = 'weekday';
            weekday.textContent = name;
            weekdaysContainer.appendChild(weekday);
        });

        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const prevDays = new Date(currentYear, currentMonth, 0).getDate();

        for (let i = firstDay - 1; i >= 0; i--) {
            const item = document.createElement('div');
            item.className = 'calendar-day other-month';
            item.innerHTML = `<span class="day-number">${prevDays - i}</span>`;
            daysContainer.appendChild(item);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(currentYear, currentMonth, day);
            date.setHours(0, 0, 0, 0);
            const item = document.createElement('div');
            item.className = 'calendar-day';
            const dateString = date.toISOString().split('T')[0];

            item.innerHTML = `<div class="day-number">${day}</div><div class="day-note"></div>`;

            if (date < today) {
                item.classList.add('other-month');
                item.style.cursor = 'default';
            } else {
                item.addEventListener('click', () => toggleDateSelection(dateString, item));
            }

            if (selectedDates.includes(dateString)) {
                item.classList.add('selected');
            }

            daysContainer.appendChild(item);
        }

        const nextMonthDays = (7 - (firstDay + daysInMonth) % 7) % 7;
        for (let i = 1; i <= nextMonthDays; i++) {
            const item = document.createElement('div');
            item.className = 'calendar-day other-month';
            item.innerHTML = `<span class="day-number">${i}</span>`;
            daysContainer.appendChild(item);
        }

        updateSelectedDatesDisplay();
    }

    function toggleDateSelection(dateString, element) {
        const index = selectedDates.indexOf(dateString);
        if (index === -1) {
            selectedDates.push(dateString);
            element.classList.add('selected');
        } else {
            selectedDates.splice(index, 1);
            element.classList.remove('selected');
        }
        updateSelectedDatesDisplay();
    }

    function previousMonth() {
        currentMonth -= 1;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear -= 1;
        }
        renderCalendar();
    }

    function nextMonth() {
        currentMonth += 1;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear += 1;
        }
        renderCalendar();
    }

    function goToToday() {
        const now = new Date();
        currentMonth = now.getMonth();
        currentYear = now.getFullYear();
        renderCalendar();
    }

    function updateEstablishments() {
        const campusSelect = document.getElementById('campus_id');
        const venueSelect = document.getElementById('establishment_id');
        const selectedCampus = campusSelect.value;
        const selectedVenue = venueSelect.value;

        venueSelect.innerHTML = '<option value="">Select venue</option>';

        allVenueOptions.forEach(optionData => {
            if (!optionData.value) {
                return;
            }

            if (selectedCampus === '' || optionData.campus === selectedCampus) {
                const option = document.createElement('option');
                option.value = optionData.value;
                option.textContent = optionData.text;
                option.dataset.campus = optionData.campus;
                option.dataset.name = optionData.name;
                option.dataset.capacity = optionData.capacity;
                option.dataset.type = optionData.type;
                if (optionData.value === selectedVenue || optionData.selected) {
                    option.selected = true;
                }
                venueSelect.appendChild(option);
            }
        });

        if (!Array.from(venueSelect.options).some(option => option.selected && option.value !== '')) {
            venueSelect.value = '';
        }

        updateVenueSummary();
    }

    function updateVenueSummary() {
        const campusSelect = document.getElementById('campus_id');
        const venueSelect = document.getElementById('establishment_id');
        const selectedCampus = campusSelect.selectedOptions[0];
        const selectedVenue = venueSelect.selectedOptions[0];
        const campusName = document.getElementById('campusName');
        const venueName = document.getElementById('venueName');
        const venueInfo = document.getElementById('venueInfo');

        campusName.textContent = selectedCampus && selectedCampus.value !== ''
            ? `Campus: ${selectedCampus.textContent}`
            : 'Campus: —';

        if (selectedVenue && selectedVenue.value !== '') {
            venueName.textContent = `Venue: ${selectedVenue.dataset.name || 'Unknown venue'}`;
            venueInfo.textContent = `Capacity: ${selectedVenue.dataset.capacity || '—'} | Type: ${selectedVenue.dataset.type || '—'}`;
        } else {
            venueName.textContent = 'Venue: No venue selected yet';
            venueInfo.textContent = 'Capacity: — | Type: —';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderCalendar();
        updateEstablishments();
        updateVenueSummary();
    });
</script>
@endsection
