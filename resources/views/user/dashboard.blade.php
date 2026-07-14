@extends('layouts.app')

@section('title', 'User Dashboard')

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
        overflow-x: hidden;
    }

    .user-container {
        display: flex;
        min-height: 100vh;
        overflow-x: hidden;
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
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .welcome-text {
        font-size: 16px;
        color: #3c4a3f;
    }

    .welcome-text strong {
        color: #1a7a3e;
    }

    .campus-badge {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .readonly-badge {
        background: #fff3cd;
        color: #856404;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .dashboard-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 25px;
    }

    .calendar-wrapper {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8eee9;
        flex-wrap: wrap;
        gap: 15px;
    }

    .month-title {
        font-size: 20px;
        font-weight: 700;
        color: #1a7a3e;
    }

    .calendar-nav {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .view-switch {
        display: flex;
        gap: 8px;
    }

    .nav-btn {
        background: #f0faf3;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        color: #1a7a3e;
        transition: all 0.3s;
    }

    .nav-btn:hover,
    .nav-btn.active {
        background: #1a7a3e;
        color: white;
    }

    .today-btn {
        background: #2db84f;
        color: white;
    }

    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
        margin-bottom: 10px;
    }

    .weekday {
        text-align: center;
        padding: 8px;
        font-weight: 700;
        color: #1a7a3e;
        font-size: 12px;
        background: #f7faf8;
        border-radius: 8px;
    }

    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
    }

    .calendar-day {
        min-height: 100px;
        background: #f7faf8;
        border-radius: 10px;
        padding: 8px;
        transition: all 0.3s;
        cursor: pointer;
        border: 1px solid transparent;
        position: relative;
    }

    .calendar-day:hover {
        background: #e8eee9;
        transform: scale(1.02);
    }

    .calendar-day.selected {
        background: #d4f5df;
        border: 2px solid #2db84f;
    }

    .calendar-day.today {
        background: #e8eee9;
        border: 1px solid #2db84f;
    }

    .calendar-day.other-month {
        opacity: 0.4;
    }

    .calendar-day.past {
        background: #e8eee9;
        opacity: 0.5;
        cursor: not-allowed;
    }

    .day-number {
        font-weight: 600;
        color: #3c4a3f;
        font-size: 12px;
        margin-bottom: 8px;
    }

    .calendar-day.today .day-number {
        color: #1a7a3e;
        font-weight: 700;
    }

    .day-events {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .event-badge {
        background: rgba(45, 184, 79, 0.1);
        color: #1a7a3e;
        padding: 4px 6px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-left: 3px solid #2db84f;
        cursor: pointer;
    }

    .event-badge.multi-date {
        background: rgba(45, 184, 79, 0.2);
        border-left-color: #ff9800;
    }

    .event-badge:hover {
        background: rgba(45, 184, 79, 0.2);
    }

    .events-panel {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .scheduled-events, .upcoming-events {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e8eee9;
    }

    .panel-title {
        font-size: 16px;
        font-weight: 700;
        color: #1a7a3e;
    }

    .selected-date-badge {
        background: #f0faf3;
        color: #1a7a3e;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .events-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-height: 300px;
        overflow-y: auto;
    }

    .event-item {
        display: flex;
        gap: 12px;
        padding: 12px;
        background: #f7faf8;
        border-radius: 12px;
        transition: all 0.3s;
        border-left: 3px solid #2db84f;
    }

    .event-item:hover {
        background: #e8eee9;
    }

    .event-time {
        min-width: 100px;
    }

    .event-time .time {
        font-weight: 700;
        color: #1a7a3e;
        font-size: 12px;
    }

    .event-time .date {
        font-size: 10px;
        color: #6e7f72;
    }

    .event-details {
        flex: 1;
    }

    .event-name {
        font-weight: 700;
        color: #3c4a3f;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .event-venue {
        font-size: 11px;
        color: #6e7f72;
    }

    .multi-date-indicator {
        display: inline-block;
        background: #ff9800;
        color: white;
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 9px;
        margin-left: 8px;
    }

    .no-events {
        text-align: center;
        padding: 30px;
        color: #b0bdb3;
        font-size: 13px;
    }

    .upcoming-events .events-list {
        max-height: 400px;
    }

    @media (max-width: 1024px) {
        .user-main {
            margin-left: 0;
        }
        .dashboard-layout {
            grid-template-columns: 1fr;
        }
        .calendar-day {
            min-height: 80px;
        }
    }

    @media (max-width: 768px) {
        .content-area {
            padding: 18px 14px;
        }

        .welcome-banner {
            padding: 16px 16px;
        }

        .welcome-text {
            font-size: 14px;
        }

        .calendar-wrapper,
        .scheduled-events,
        .upcoming-events {
            padding: 16px;
        }

        .calendar-header {
            gap: 10px;
        }

        .month-title {
            font-size: 18px;
        }

        .calendar-weekdays,
        .calendar-days {
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 5px;
        }

        .calendar-day {
            min-height: 70px;
            padding: 6px;
        }

        .day-number {
            font-size: 11px;
        }

        .event-badge {
            white-space: normal;
            overflow-wrap: anywhere;
            font-size: 10px;
            max-width: 100%;
        }

        .event-item {
            flex-wrap: wrap;
        }

        .event-time {
            min-width: 70px;
            width: auto;
        }

        .panel-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .events-list {
            max-height: none;
        }

        .dashboard-layout {
            gap: 18px;
        }
    }

    @media (max-width: 540px) {
        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 6px;
            margin-bottom: 10px;
        }

        .calendar-days {
            display: grid;
            gap: 10px;
            overflow-x: hidden;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }

        .calendar-days.month-view {
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }

        .calendar-days.week-view {
            grid-template-columns: 1fr;
        }

        .calendar-days.month-view .day-events {
            display: none;
        }

        .calendar-days.month-view .calendar-day.has-event .day-number {
            background: #2db84f;
            color: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .calendar-day {
            min-width: 0;
            min-height: auto;
            padding: 12px;
            display: flex;
            flex-direction: column;
            word-break: break-word;
            scroll-snap-align: start;
        }

        .day-number {
            font-size: 12px;
            margin-bottom: 8px;
        }

        .calendar-wrapper {
            overflow: visible;
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
                <div style="display: flex; gap: 10px;">
                    <div class="campus-badge">🏛️ {{ $userCampusName }}</div>
                    <div class="readonly-badge">READ ONLY MODE</div>
                </div>
            </div>

            <div class="dashboard-layout">
                <div class="calendar-wrapper">
                    <div class="calendar-header">
                        <div class="month-title" id="monthTitle">{{ $currentMonth }} {{ $currentYear }}</div>
                        <div class="calendar-nav">
                        <div class="view-switch">
                            <button id="monthBtn" class="nav-btn active" onclick="setView('month')">Month</button>
                            <button id="weekBtn" class="nav-btn" onclick="setView('week')">Week</button>
                        </div>
                        <button class="nav-btn" onclick="changeView(-1)">←</button>
                        <button class="nav-btn today-btn" onclick="goToToday()">Today</button>
                        <button class="nav-btn" onclick="changeView(1)">→</button>
                    </div>
                    </div>

                    <div class="calendar-weekdays">
                        <div class="weekday">Sun</div>
                        <div class="weekday">Mon</div>
                        <div class="weekday">Tue</div>
                        <div class="weekday">Wed</div>
                        <div class="weekday">Thu</div>
                        <div class="weekday">Fri</div>
                        <div class="weekday">Sat</div>
                    </div>

                    <div class="calendar-days" id="calendarDays"></div>
                </div>

                <div class="events-panel">
                    <div class="scheduled-events">
                        <div class="panel-header">
                            <div class="panel-title">📅 Scheduled Events</div>
                            <div class="selected-date-badge" id="selectedDateBadge">Select a date</div>
                        </div>
                        <div class="events-list" id="scheduledEventsList">
                            <div class="no-events">Click on a date to view events</div>
                        </div>
                    </div>

                    <div class="upcoming-events">
                        <div class="panel-header">
                            <div class="panel-title">📋 Upcoming Events</div>
                            <div class="selected-date-badge">{{ $userCampusName }}</div>
                        </div>
                        <div class="events-list" id="upcomingEventsList">
                            <div class="no-events">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let selectedDateStr = '';
    let eventsData = {};
    let currentView = 'month';

    function getWeekStart(date) {
        const result = new Date(date);
        const dayOfWeek = result.getDay();
        result.setDate(result.getDate() - dayOfWeek);
        result.setHours(0, 0, 0, 0);
        return result;
    }

    function formatWeekTitle(date) {
        const weekStart = getWeekStart(date);
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 6);
        const options = { month: 'short', day: 'numeric' };
        return `Week of ${weekStart.toLocaleDateString('en-US', options)} - ${weekEnd.toLocaleDateString('en-US', options)}`;
    }

    function setView(mode) {
        if (currentView === mode) return;

        currentView = mode;
        document.getElementById('monthBtn').classList.toggle('active', mode === 'month');
        document.getElementById('weekBtn').classList.toggle('active', mode === 'week');

        if (mode === 'month') {
            currentMonth = currentDate.getMonth();
            currentYear = currentDate.getFullYear();
        } else {
            currentDate = new Date();
        }

        selectedDateStr = '';
        document.getElementById('selectedDateBadge').innerHTML = 'Select a date';
        document.getElementById('scheduledEventsList').innerHTML = '<div class="no-events">Click on a date to view events</div>';

        loadEvents();
    }

    function loadEvents() {
        if (currentView === 'month') {
            return fetchEventsForMonth(currentMonth, currentYear);
        }

        return fetchEventsForWeek(currentDate);
    }

    function fetchEventsForMonth(month, year) {
        const url = `/user/dashboard/events?month=${month + 1}&year=${year}`;
        console.log('Loading events for month:', month + 1, year);

        return fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    eventsData = data.events;
                    console.log('Events loaded:', Object.keys(eventsData).length, 'dates with events');
                    renderCalendar();
                    return true;
                }
                return false;
            })
            .catch(error => {
                console.error('Error loading events:', error);
                return false;
            });
    }

    function fetchEventsForWeek(anchorDate) {
        const weekStart = getWeekStart(anchorDate);
        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 6);

        const monthYearPairs = new Map();
        const cursor = new Date(weekStart);
        while (cursor <= weekEnd) {
            monthYearPairs.set(`${cursor.getMonth()}-${cursor.getFullYear()}`, {
                month: cursor.getMonth(),
                year: cursor.getFullYear(),
            });
            cursor.setDate(cursor.getDate() + 1);
        }

        eventsData = {};
        const fetchPromises = Array.from(monthYearPairs.values()).map(({ month, year }) => {
            const url = `/user/dashboard/events?month=${month + 1}&year=${year}`;
            return fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Object.assign(eventsData, data.events);
                    }
                });
        });

        console.log('Loading events for week starting:', weekStart.toDateString());

        return Promise.all(fetchPromises)
            .then(() => {
                renderCalendar();
                return true;
            })
            .catch(error => {
                console.error('Error loading events for week:', error);
                return false;
            });
    }

    function loadUpcomingEvents() {
        const url = `/user/dashboard/upcoming`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.events.length > 0) {
                    renderUpcomingEvents(data.events);
                } else {
                    document.getElementById('upcomingEventsList').innerHTML = '<div class="no-events">No upcoming events scheduled in your campus</div>';
                }
            });
    }

    function renderCalendar() {
        if (currentView === 'week') {
            renderWeekCalendar();
        } else {
            renderMonthCalendar();
        }
    }

    function formatMonthTitle(year, month) {
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];
        return `${monthNames[month]} ${year}`;
    }

    function renderMonthCalendar() {
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const startingDay = firstDay.getDay();
        const daysInMonth = lastDay.getDate();

        let calendarHTML = '';
        let dayCounter = 1;
        const prevMonthLastDay = new Date(currentYear, currentMonth, 0).getDate();
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        for (let i = 0; i < 42; i++) {
            let dayNumber = '';
            let isCurrentMonth = true;
            let isToday = false;
            let isSelected = false;
            let isPast = false;
            let dateStr = '';
            let dayEvents = [];

            if (i < startingDay) {
                dayNumber = prevMonthLastDay - (startingDay - i) + 1;
                isCurrentMonth = false;
                const prevMonth = new Date(currentYear, currentMonth - 1, dayNumber);
                dateStr = `${prevMonth.getFullYear()}-${String(prevMonth.getMonth() + 1).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;
            } else if (dayCounter > daysInMonth) {
                dayNumber = dayCounter - daysInMonth;
                isCurrentMonth = false;
                const nextMonth = new Date(currentYear, currentMonth + 1, dayNumber);
                dateStr = `${nextMonth.getFullYear()}-${String(nextMonth.getMonth() + 1).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;
                dayCounter++;
            } else {
                dayNumber = dayCounter;
                dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;
                dayCounter++;

                const checkDate = new Date(currentYear, currentMonth, dayNumber);
                if (checkDate.toDateString() === today.toDateString()) {
                    isToday = true;
                }

                if (checkDate < today) {
                    isPast = true;
                }

                if (selectedDateStr === dateStr) {
                    isSelected = true;
                }

                dayEvents = eventsData[dateStr] || [];
            }

            let eventBadgesHtml = '';
            if (dayEvents.length > 0) {
                eventBadgesHtml = '<div class="day-events">';
                dayEvents.slice(0, 2).forEach(event => {
                    const isVehicle = event.type === 'vehicle';
                    const isMultiDate = event.is_multi_date;
                    const multiDateClass = isMultiDate ? 'multi-date' : '';
                    const badgeStyle = isVehicle ? 'style="border-left: 3px solid #6c5ce7;"' : '';
                    const icon = isVehicle ? '' : '📍 ';
                    eventBadgesHtml += `
                        <div class="event-badge ${multiDateClass}" ${badgeStyle} title="${event.title} - ${event.time} - ${event.venue}${isMultiDate ? ' (Multiple Dates)' : ''}">
                            ${icon}${event.title.length > 12 ? event.title.substring(0, 12) + '...' : event.title}
                            ${isMultiDate ? '<span style="background: #ff9800; padding: 1px 4px; border-radius: 8px; font-size: 8px; margin-left: 4px;">📅📅</span>' : ''}
                        </div>
                    `;
                });
                if (dayEvents.length > 2) {
                    eventBadgesHtml += `<div class="event-badge" style="background: #e8eee9;">+${dayEvents.length - 2} more</div>`;
                }
                eventBadgesHtml += '</div>';
            }

            calendarHTML += `
                <div class="calendar-day ${!isCurrentMonth ? 'other-month' : ''} ${isToday ? 'today' : ''} ${isSelected ? 'selected' : ''} ${isPast ? 'past' : ''} ${dayEvents.length > 0 ? 'has-event' : ''}"
                     onclick="${!isPast ? `selectDate('${dateStr}')` : ''}">
                    <div class="day-number">${dayNumber}</div>
                    ${eventBadgesHtml}
                </div>
            `;
        }

        document.getElementById('monthTitle').textContent = formatMonthTitle(currentYear, currentMonth);
        const calendarDaysEl = document.getElementById('calendarDays');
        calendarDaysEl.innerHTML = calendarHTML;
        calendarDaysEl.classList.toggle('month-view', currentView === 'month');
        calendarDaysEl.classList.toggle('week-view', currentView === 'week');
    }

    function renderWeekCalendar() {
        const weekStart = getWeekStart(currentDate);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        let calendarHTML = '';
        for (let i = 0; i < 7; i++) {
            const thisDay = new Date(weekStart);
            thisDay.setDate(weekStart.getDate() + i);
            const dayNumber = thisDay.getDate();
            const dateStr = `${thisDay.getFullYear()}-${String(thisDay.getMonth() + 1).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;
            const isToday = thisDay.toDateString() === today.toDateString();
            const isSelected = selectedDateStr === dateStr;
            const isPast = thisDay < today;
            const dayEvents = eventsData[dateStr] || [];

            let eventBadgesHtml = '';
            if (dayEvents.length > 0) {
                eventBadgesHtml = '<div class="day-events">';
                dayEvents.slice(0, 2).forEach(event => {
                    const isVehicle = event.type === 'vehicle';
                    const isMultiDate = event.is_multi_date;
                    const multiDateClass = isMultiDate ? 'multi-date' : '';
                    const badgeStyle = isVehicle ? 'style="border-left: 3px solid #6c5ce7;"' : '';
                    const icon = isVehicle ? '' : '📍 ';
                    eventBadgesHtml += `
                        <div class="event-badge ${multiDateClass}" ${badgeStyle} title="${event.title} - ${event.time} - ${event.venue}${isMultiDate ? ' (Multiple Dates)' : ''}">
                            ${icon}${event.title.length > 12 ? event.title.substring(0, 12) + '...' : event.title}
                            ${isMultiDate ? '<span style="background: #ff9800; padding: 1px 4px; border-radius: 8px; font-size: 8px; margin-left: 4px;">📅📅</span>' : ''}
                        </div>
                    `;
                });
                if (dayEvents.length > 2) {
                    eventBadgesHtml += `<div class="event-badge" style="background: #e8eee9;">+${dayEvents.length - 2} more</div>`;
                }
                eventBadgesHtml += '</div>';
            }

            calendarHTML += `
                <div class="calendar-day ${isToday ? 'today' : ''} ${isSelected ? 'selected' : ''} ${isPast ? 'past' : ''}"
                     onclick="${!isPast ? `selectDate('${dateStr}')` : ''}">
                    <div class="day-number">${dayNumber}</div>
                    ${eventBadgesHtml}
                </div>
            `;
        }

        document.getElementById('monthTitle').textContent = formatWeekTitle(currentDate);
        const calendarDaysEl = document.getElementById('calendarDays');
        calendarDaysEl.innerHTML = calendarHTML;
        calendarDaysEl.classList.toggle('month-view', currentView === 'month');
        calendarDaysEl.classList.toggle('week-view', currentView === 'week');
    }

    function selectDate(dateStr) {
        if (!dateStr) return;
        
        console.log('Date selected:', dateStr);
        selectedDateStr = dateStr;
        
        const dateObj = new Date(dateStr);
        const formattedDate = dateObj.toLocaleDateString('en-US', { 
            weekday: 'long', 
            month: 'long', 
            day: 'numeric', 
            year: 'numeric' 
        });
        
        document.getElementById('selectedDateBadge').innerHTML = formattedDate;
        
        const dayEvents = eventsData[dateStr] || [];
        console.log('Events for this date:', dayEvents.length);
        renderScheduledEvents(dayEvents, dateStr);
        
        renderCalendar();
    }

    function renderScheduledEvents(events, dateStr) {
        if (!events || events.length === 0) {
            document.getElementById('scheduledEventsList').innerHTML = '<div class="no-events">No events scheduled on this date</div>';
            return;
        }
        
        let eventsHTML = '';
        events.forEach(event => {
            const isVehicle = event.type === 'vehicle';
            const isMultiDate = event.is_multi_date;
            const multiDateBadge = isMultiDate ? '<span class="multi-date-indicator">Multiple Dates</span>' : '';
            const typeBadge = isVehicle ? '<span class="multi-date-indicator" style="background:#6c5ce7;">🚐 VEHICLE</span>' : '';
            eventsHTML += `
                <div class="event-item" ${isVehicle ? 'style="border-left-color:#6c5ce7;"' : ''}>
                    <div class="event-time">
                        <div class="time">${event.time}</div>
                    </div>
                    <div class="event-details">
                        <div class="event-name">${event.title} ${typeBadge} ${multiDateBadge}</div>
                        <div class="event-venue">📍 ${event.venue}</div>
                        ${isVehicle && event.vehicle ? `<div class="event-venue">🚗 ${event.vehicle}</div>` : ''}
                        ${isMultiDate && event.multiple_dates ? `
                            <div style="font-size: 10px; color: #ff9800; margin-top: 4px;">
                                📅 Also on: ${event.multiple_dates.filter(d => d !== dateStr).map(d => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })).join(', ')}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        
        document.getElementById('scheduledEventsList').innerHTML = eventsHTML;
    }

    function renderUpcomingEvents(events) {
        if (!events || events.length === 0) {
            document.getElementById('upcomingEventsList').innerHTML = '<div class="no-events">No upcoming events scheduled in your campus</div>';
            return;
        }
        
        let eventsHTML = '';
        events.forEach(event => {
            const isVehicle = event.type === 'vehicle';
            const isMultiDate = event.is_multi_date;
            const multiDateBadge = isMultiDate ? '<span class="multi-date-indicator">Multiple Dates</span>' : '';
            const typeBadge = isVehicle ? '<span class="multi-date-indicator" style="background:#6c5ce7;">🚐 VEHICLE</span>' : '';
            eventsHTML += `
                <div class="event-item" ${isVehicle ? 'style="border-left-color:#6c5ce7;"' : ''}>
                    <div class="event-time">
                        <div class="time">${event.time}</div>
                        <div class="date">${event.date}</div>
                    </div>
                    <div class="event-details">
                        <div class="event-name">${event.title} ${typeBadge} ${multiDateBadge}</div>
                        <div class="event-venue">📍 ${event.venue}</div>
                        ${isVehicle && event.vehicle ? `<div class="event-venue">🚗 ${event.vehicle}</div>` : ''}
                    </div>
                </div>
            `;
        });
        
        document.getElementById('upcomingEventsList').innerHTML = eventsHTML;
    }

    function changeView(direction) {
        if (currentView === 'month') {
            currentMonth += direction;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            } else if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            currentDate = new Date(currentYear, currentMonth, 1);
        } else {
            currentDate.setDate(currentDate.getDate() + direction * 7);
        }

        selectedDateStr = '';
        document.getElementById('selectedDateBadge').innerHTML = 'Select a date';
        document.getElementById('scheduledEventsList').innerHTML = '<div class="no-events">Click on a date to view events</div>';

        loadEvents().then(() => {
            loadUpcomingEvents();
        });
    }

    function goToToday() {
        const today = new Date();
        currentDate = new Date(today);
        currentYear = today.getFullYear();
        currentMonth = today.getMonth();

        const todayStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

        loadEvents().then(() => {
            selectDate(todayStr);
            loadUpcomingEvents();
        });
    }

    // Initialize
    loadEvents().then(() => {
        loadUpcomingEvents();
        const today = new Date();
        const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
        setTimeout(() => {
            selectDate(todayStr);
        }, 100);
    });
</script>
@endsection