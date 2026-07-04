@extends('layouts.app')

@section('title', 'Public Availability')

@section('content')
<style>
    .availability-page {
        padding: 30px;
        min-height: 100vh;
        background: #f0faf3;
    }

    .availability-top {
        max-width: 1040px;
        margin: 0 auto 25px;
        background: linear-gradient(180deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 100%), url('{{ asset('images/UCC_South.png') }}');
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        border-radius: 18px;
        padding: 24px 28px;
        box-shadow: 0 14px 40px rgba(10, 61, 31, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .availability-heading {
        max-width: 720px;
    }

    .availability-heading h1 {
        font-size: 24px;
        margin-bottom: 12px;
        color: #1a7a3e;
        text-align: center;
    }

    .availability-heading h2 {
        font-size: 18px;
        margin-bottom: 16px;
        color: #1a7a3e;
        text-align: center;
    }

    .availability-heading p {
        font-size: 15px;
        line-height: 1.7;
        color: #4a5f51;
    }

    .availability-logos {
        display: flex;
        gap: 18px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: space-around;
    }

    .availability-logo {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 14px;
        background: #f7faf8;
        border-radius: 18px;
        border: 1px solid #e8eee9;
    }

    .availability-logo img {
        max-height: 80px;
        width: auto;
        display: block;
    }

    .campus-select {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 18px;
    }

    .campus-select label {
        font-size: 13px;
        color: #4a5f51;
        font-weight: 600;
    }

    .campus-select select {
        width: 100%;
        border-radius: 12px;
        border: 1px solid #d4dbd6;
        padding: 12px 14px;
        background: #f7faf8;
        color: #1a3d29;
        font-size: 14px;
        font-weight: 600;
    }

    .availability-grid {
        max-width: 1040px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 24px;
    }

    .calendar-panel,
    .availability-panel {
        background: white;
        border-radius: 22px;
        padding: 24px;
        box-shadow: 0 10px 32px rgba(10, 61, 31, 0.07);
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .calendar-title {
        font-size: 20px;
        font-weight: 700;
        color: #1a7a3e;
    }

    .calendar-nav {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

.calendar-nav button {
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            background: #f0faf3;
            color: #1a7a3e;
            cursor: pointer;
            transition: all 0.25s ease;
            font-weight: 600;
        }

        .calendar-nav button:hover {
            background: #1a7a3e;
            color: white;
        }

        .campus-select {
            max-width: 280px;
    }

    .weekdays {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 6px;
        margin-bottom: 10px;
    }

    .weekday {
        text-align: center;
        padding: 10px 0;
        background: #eff7ed;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
        color: #1a7a3e;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 8px;
    }

    .calendar-day {
        min-height: 86px;
        padding: 12px;
        border-radius: 16px;
        background: #f7faf8;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .calendar-day:hover {
        transform: translateY(-1px);
        background: #e8eee9;
    }

    .calendar-day.selected {
        border-color: #2db84f;
        background: #e9f8eb;
    }

    .calendar-day.has-event {
        box-shadow: inset 0 0 0 1px rgba(45, 184, 79, 0.24);
    }

    .calendar-day .day-number {
        font-size: 13px;
        font-weight: 700;
        color: #2a3f33;
    }

    .calendar-day .event-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #2db84f;
        position: absolute;
        bottom: 11px;
        right: 11px;
    }

    .availability-panel {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .selected-info {
        padding: 20px;
        background: #f4ffef;
        border-radius: 18px;
        border: 1px solid #d4ead1;
    }

    .selected-info h2 {
        font-size: 18px;
        margin-bottom: 10px;
        color: #18562f;
    }

    .selected-info p {
        color: #486952;
        line-height: 1.6;
    }

    .event-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e5eee7;
        padding: 16px;
        display: grid;
        gap: 10px;
    }

    .vehicle-card {
        background: #f7fbff;
        border-radius: 16px;
        border: 1px solid #d8e7f2;
        padding: 16px;
        display: grid;
        gap: 10px;
    }

    .event-card strong {
        color: #1a7a3e;
        font-size: 15px;
    }

    .event-meta {
        display: grid;
        gap: 8px;
        color: #4f6658;
        font-size: 13px;
    }

    .cta-box {
        background: #f7faf8;
        border-radius: 18px;
        padding: 18px;
        border: 1px solid #d9e7d7;
    }

    .cta-box h3 {
        margin-bottom: 10px;
        font-size: 17px;
        color: #163c2b;
    }

    .cta-buttons {
        display: grid;
        gap: 12px;
    }

    .cta-buttons a {
        display: block;
        text-align: center;
        padding: 14px 16px;
        border-radius: 14px;
        font-weight: 700;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .cta-login {
        background: #ffffff;
        color: #1a7a3e;
        border-color: #c6ddca;
    }

    .cta-register {
        background: #2db84f;
        color: white;
    }

    .cta-login:hover {
        background: #e8f3e7;
    }

    .cta-register:hover {
        background: #1a7a3e;
    }

    .no-events-box {
        padding: 18px;
        border-radius: 16px;
        background: #fff9e8;
        border: 1px solid #f3e3c4;
        color: #6f5c2d;
        font-size: 14px;
    }

    @media (max-width: 980px) {
        .availability-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 720px) {
        .availability-page {
            padding: 20px;
        }

        .availability-top {
            padding: 20px;
        }

        .calendar-grid,
        .weekdays {
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }

        .calendar-panel,
        .availability-panel {
            padding: 18px;
        }

        .calendar-day {
            min-height: 78px;
            padding: 10px;
        }

        .calendar-nav {
            width: 100%;
            justify-content: flex-start;
        }

        .calendar-title {
            font-size: 18px;
        }
    }

    @media (max-width: 560px) {
        .availability-top {
            flex-direction: column;
            align-items: stretch;
        }

        .calendar-grid,
        .weekdays {
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }

        .calendar-day {
            min-height: 70px;
        }

        .calendar-nav button,
        .campus-select select {
            width: 100%;
        }

        .calendar-nav {
            justify-content: space-between;
        }
    }
</style>

<div class="availability-page">
    <div class="availability-top">
        <div class="availability-logos">
            <div class="availability-logo">
                <img src="{{ asset('images/UCC_Logo.png') }}" alt="UCC Logo">
            </div>
        </div>
        <div class="availability-heading">
            <h1>University of Caloocan City</h1>
            <h2>Event Reservation System</h2>
            <p>See which campus dates already have scheduled events and which days are still open. Select a date to view details, then log in or register to make a reservation.</p>
        </div>
    </div>

    <div class="availability-grid">
        <div class="calendar-panel">
<div class="campus-select">
                <label for="campusSelect">Select Campus</label>
                <select id="campusSelect" onchange="setCampus(this.value)">
                    <option value="all" {{ $selectedCampus === 'all' ? 'selected' : '' }}>All Campuses</option>
                    @foreach ($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ $selectedCampus == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="calendar-header">
                <div class="calendar-title" id="monthTitle"></div>
                <div class="calendar-nav">
                    <button type="button" onclick="changeMonth(-1)">←</button>
                    <button type="button" onclick="goToToday()">Today</button>
                    <button type="button" onclick="changeMonth(1)">→</button>
                </div>
            </div>

            <div class="weekdays">
                <div class="weekday">Sun</div>
                <div class="weekday">Mon</div>
                <div class="weekday">Tue</div>
                <div class="weekday">Wed</div>
                <div class="weekday">Thu</div>
                <div class="weekday">Fri</div>
                <div class="weekday">Sat</div>
            </div>

            <div class="calendar-grid" id="calendarDays"></div>
        </div>

        <div class="availability-panel">
            <div class="selected-info" id="selectedInfo">
                <h2>Select a date to view scheduled events</h2>
                <p>Choose a campus and tap any highlighted day to see both event and pickup-vehicle availability for that day.</p>
            </div>

            <div id="eventDetails"></div>
            <div id="vehicleDetails"></div>

            <div class="cta-box">
                <h3>Want to make a reservation?</h3>
                <p>Reserve a campus space once you have an account. Use the buttons below to log in or register before you continue.</p>
                <div class="cta-buttons">
                    <a class="cta-login" href="{{ route('login') }}">Login to proceed</a>
                    <a class="cta-register" href="{{ route('register') }}">Create an account</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const campusSelect = document.getElementById('campusSelect');
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let selectedCampus = 'all';
    let selectedDate = null;
    let eventsData = {};
    let vehicleData = {};

    function formatMonthTitle(year, month) {
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return `${monthNames[month]} ${year}`;
    }

    function setCampus(campusId) {
        selectedCampus = campusId;
        if (campusSelect) {
            campusSelect.value = campusId;
        }
        selectedDate = null;
        loadEvents();
    }

    function loadEvents() {
        const eventsUrl = `/availability/events?campus_id=${selectedCampus}&month=${currentMonth + 1}&year=${currentYear}`;
        const vehicleUrl = `/availability/vehicles?campus_id=${selectedCampus}&month=${currentMonth + 1}&year=${currentYear}`;

        return Promise.all([
            fetch(eventsUrl).then(response => response.json()),
            fetch(vehicleUrl).then(response => response.json())
        ])
            .then(([eventData, vehicleResponse]) => {
                if (eventData.success) {
                    eventsData = eventData.events;
                }
                if (vehicleResponse.success) {
                    vehicleData = vehicleResponse.dates;
                }
                renderCalendar();
            })
            .catch(error => console.error('Failed to load availability:', error));
    }

    function renderCalendar() {
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const startingDay = firstDay.getDay();
        const daysInMonth = lastDay.getDate();
        const prevMonthLastDay = new Date(currentYear, currentMonth, 0).getDate();
        const today = new Date();
        today.setHours(0,0,0,0);

        let calendarHTML = '';
        let dayCounter = 1;

        for (let i = 0; i < 42; i++) {
            let dayNumber = '';
            let dateStr = '';
            let isCurrentMonth = true;
            let isToday = false;
            let isSelected = false;
            let hasEvent = false;
            let hasVehicle = false;

            if (i < startingDay) {
                dayNumber = prevMonthLastDay - (startingDay - i) + 1;
                const prevDate = new Date(currentYear, currentMonth - 1, dayNumber);
                dateStr = formatDateKey(prevDate);
                isCurrentMonth = false;
            } else if (dayCounter > daysInMonth) {
                dayNumber = dayCounter - daysInMonth;
                const nextDate = new Date(currentYear, currentMonth + 1, dayNumber);
                dateStr = formatDateKey(nextDate);
                isCurrentMonth = false;
                dayCounter++;
            } else {
                const thisDate = new Date(currentYear, currentMonth, dayCounter);
                dayNumber = dayCounter;
                dateStr = formatDateKey(thisDate);
                dayCounter++;

                if (thisDate.getTime() === today.getTime()) {
                    isToday = true;
                }
                if (selectedDate === dateStr) {
                    isSelected = true;
                }
                hasEvent = Array.isArray(eventsData[dateStr]) && eventsData[dateStr].length > 0;
                hasVehicle = Array.isArray(vehicleData[dateStr]) && vehicleData[dateStr].length > 0;
            }

            calendarHTML += `
                <div class="calendar-day ${isCurrentMonth ? '' : 'other-month'} ${isSelected ? 'selected' : ''} ${hasEvent || hasVehicle ? 'has-event' : ''}"
                     onclick="${isCurrentMonth ? `selectDate('${dateStr}')` : ''}">
                    <div class="day-number">${dayNumber}</div>
                    ${hasEvent || hasVehicle ? '<div class="event-dot"></div>' : ''}
                </div>
            `;
        }

        document.getElementById('monthTitle').textContent = formatMonthTitle(currentYear, currentMonth);
        document.getElementById('calendarDays').innerHTML = calendarHTML;

        if (!selectedDate) {
            const todayKey = formatDateKey(new Date());
            selectDate(todayKey);
        }
    }

    function selectDate(dateKey) {
        selectedDate = dateKey;
        const selectedDateObj = new Date(dateKey);
        document.querySelectorAll('.calendar-day').forEach(el => {
            el.classList.toggle('selected', el.getAttribute('onclick').includes(dateKey));
        });

        const infoTitle = selectedDateObj.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
        document.getElementById('selectedInfo').innerHTML = `<h2>${infoTitle}</h2><p>Review scheduled events on this date or use the reservation buttons below to log in or create an account.</p>`;

        Promise.all([
            fetch(`/availability/day?campus_id=${selectedCampus}&date=${dateKey}`).then(response => response.json()),
            fetch(`/availability/vehicles/day?campus_id=${selectedCampus}&date=${dateKey}`).then(response => response.json())
        ])
            .then(([eventData, vehicleDataResponse]) => {
                renderDayEvents(eventData.events, vehicleDataResponse.reservations, dateKey);
            })
            .catch(error => {
                console.error('Error loading day availability:', error);
            });
    }

    function renderDayEvents(events, vehicles, dateKey) {
        const eventContainer = document.getElementById('eventDetails');
        const vehicleContainer = document.getElementById('vehicleDetails');

        if (!events || events.length === 0) {
            eventContainer.innerHTML = `
                <div class="no-events-box">
                    <strong>No scheduled events on this date.</strong>
                    <p>This day is currently available for reservation.</p>
                </div>
            `;
        } else {
            const eventsHTML = events.map(event => `
                <div class="event-card">
                    <strong>${event.title}</strong>
                    <div class="event-meta">
                        <div><strong>Time:</strong> ${event.time}</div>
                        <div><strong>Venue:</strong> ${event.venue}</div>
                        <div><strong>Campus:</strong> ${event.campus}</div>
                        <div><strong>Requested by:</strong> ${event.requestor}</div>
                        ${event.is_multi_date ? `<div><strong>Multi-date event:</strong> ${event.multiple_dates.join(', ')}</div>` : ''}
                    </div>
                </div>
            `).join('');

            eventContainer.innerHTML = eventsHTML;
        }

        if (!vehicles || vehicles.length === 0) {
            vehicleContainer.innerHTML = `
                <div class="no-events-box" style="margin-top: 12px;">
                    <strong>No pickup vehicle reservations on this date.</strong>
                    <p>This day is currently available for pickup vehicle requests.</p>
                </div>
            `;
        } else {
            const vehiclesHTML = vehicles.map(vehicle => `
                <div class="vehicle-card" style="margin-top: 12px;">
                    <strong>🚐 ${vehicle.code}</strong>
                    <div class="event-meta">
                        <div><strong>Time:</strong> ${vehicle.time}</div>
                        <div><strong>Purpose:</strong> ${vehicle.purpose}</div>
                        <div><strong>From:</strong> ${vehicle.origin_campus}</div>
                        <div><strong>Destination:</strong> ${vehicle.destination}</div>
                        <div><strong>Requested by:</strong> ${vehicle.requestor}</div>
                    </div>
                </div>
            `).join('');

            vehicleContainer.innerHTML = vehiclesHTML;
        }
    }

    function changeMonth(direction) {
        currentMonth += direction;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear -= 1;
        } else if (currentMonth > 11) {
            currentMonth = 0;
            currentYear += 1;
        }
        selectedDate = null;
        loadEvents();
    }

    function goToToday() {
        const today = new Date();
        currentDate = new Date(today);
        currentMonth = today.getMonth();
        currentYear = today.getFullYear();
        selectedDate = null;
        loadEvents();
    }

    function formatDateKey(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadEvents();
    });
</script>
@endsection
