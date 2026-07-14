@extends('layouts.admin')

@section('title', 'Availability Calendar')
@section('page-title', 'Event Schedule')

@section('content')
<style>
    .availability-container {
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
        font-size: 22px;
        font-weight: 700;
        color: #1a7a3e;
    }
    
    .calendar-nav {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .nav-btn {
        background: #f0faf3;
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        color: #1a7a3e;
        transition: all 0.3s;
    }
    
    .nav-btn:hover {
        background: #1a7a3e;
        color: white;
    }
    
    .today-btn {
        background: #2db84f;
        color: white;
    }
    
    .campus-filter {
        padding: 8px 15px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 13px;
        background: white;
    }
    
    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    
    .weekday {
        text-align: center;
        padding: 10px;
        font-weight: 700;
        color: #1a7a3e;
        font-size: 13px;
        background: #f7faf8;
        border-radius: 10px;
    }
    
    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }
    
    .calendar-day {
        min-height: 110px;
        background: #f7faf8;
        border-radius: 12px;
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
    
    .day-number {
        font-weight: 700;
        color: #3c4a3f;
        font-size: 13px;
        margin-bottom: 8px;
    }
    
    .day-events {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .event-badge {
        background: rgba(45, 184, 79, 0.1);
        padding: 4px 6px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        border-left: 3px solid;
    }
    
    .event-badge.multi-date {
        background: rgba(255, 152, 0, 0.1);
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
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .events-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-height: 350px;
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
        margin-bottom: 2px;
    }
    
    .event-campus {
        font-size: 10px;
        color: #2db84f;
        font-weight: 600;
    }
    
    .multi-date-indicator {
        display: inline-block;
        background: #ff9800;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        margin-left: 8px;
    }
    
    .no-events {
        text-align: center;
        padding: 30px;
        color: #b0bdb3;
        font-size: 13px;
    }
    
    .legend {
        background: white;
        border-radius: 16px;
        padding: 15px;
        margin-top: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .legend-title {
        font-size: 12px;
        font-weight: 700;
        color: #1a7a3e;
        margin-bottom: 10px;
    }
    
    .legend-items {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: #6e7f72;
    }
    
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    
    @media (max-width: 1024px) {
        .availability-container {
            grid-template-columns: 1fr;
        }
        .calendar-day {
            min-height: 80px;
        }
    }
</style>

<div class="availability-container">
    <div class="calendar-wrapper">
        <div class="calendar-header">
            <div class="month-title" id="monthTitle">May 2026</div>
            <div class="calendar-nav">
                <select id="campusFilter" class="campus-filter">
                    <option value="all">All Campuses</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ $selectedCampus == $campus->id ? 'selected' : '' }}>
                            {{ $campus->name }}
                        </option>
                    @endforeach
                </select>
                <button class="nav-btn" onclick="changeMonth(-1)">←</button>
                <button class="nav-btn today-btn" onclick="goToToday()">Today</button>
                <button class="nav-btn" onclick="changeMonth(1)">→</button>
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
        
        <div class="legend">
            <div class="legend-title">📌 Legend</div>
            <div class="legend-items">
                <div class="legend-item">
                    <div class="legend-color" style="background: #2db84f;"></div>
                    <span>Single Date Event</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #ff9800;"></div>
                    <span>Multi-Date Event</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #6c5ce7;"></div>
                    <span>🚐 Pickup Vehicle Reservation</span>
                </div>
            </div>
        </div>
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
                <div class="selected-date-badge">All upcoming</div>
            </div>
            <div class="events-list" id="upcomingEventsList">
                <div class="no-events">Loading...</div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let selectedDateStr = '';
    let eventsData = {};
    let campusColors = {
        1: '#4caf50',
        2: '#2196f3',
        3: '#ff9800',
        4: '#9c27b0'
    };

    function getEventColor(campusId) {
        return campusColors[campusId] || '#6e7f72';
    }

    function loadEvents() {
        const campusId = document.getElementById('campusFilter').value;
        const url = `/admin/availability/events?campus_id=${campusId}&month=${currentMonth + 1}&year=${currentYear}`;
        
        console.log('Loading events for month:', currentMonth + 1, currentYear);
        
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
            });
    }

    function loadUpcomingEvents() {
        const campusId = document.getElementById('campusFilter').value;
        const url = `/admin/availability/upcoming?campus_id=${campusId}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.events.length > 0) {
                    renderUpcomingEvents(data.events);
                } else {
                    document.getElementById('upcomingEventsList').innerHTML = '<div class="no-events">No upcoming events scheduled</div>';
                }
            });
    }

    function renderCalendar() {
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
            let dateStr = '';
            let dayEvents = [];
            let campusIdForColor = null;
            
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
                
                if (selectedDateStr === dateStr) {
                    isSelected = true;
                }
                
                dayEvents = eventsData[dateStr] || [];
                if (dayEvents.length > 0 && dayEvents[0].campus_id) {
                    campusIdForColor = dayEvents[0].campus_id;
                }
            }
            
            let eventBadgesHtml = '';
            if (dayEvents.length > 0) {
                eventBadgesHtml = '<div class="day-events">';
                dayEvents.slice(0, 2).forEach(event => {
                    const isVehicle = event.type === 'vehicle';
                    const eventColor = isVehicle ? '#6c5ce7' : getEventColor(event.campus_id);
                    const isMultiDate = event.is_multi_date;
                    const multiDateClass = isMultiDate ? 'multi-date' : '';
                    const tooltip = `${event.title} - ${event.time} - ${event.venue}${isMultiDate ? ' (Multiple Dates)' : ''}`;
                    eventBadgesHtml += `
                        <div class="event-badge ${multiDateClass}" style="border-left-color: ${eventColor};" title="${tooltip}">
                            <span class="event-dot" style="background: ${eventColor};"></span>
                            ${event.title.length > 15 ? event.title.substring(0, 15) + '...' : event.title}
                            ${isMultiDate ? ' 📅📅' : ''}
                        </div>
                    `;
                });
                if (dayEvents.length > 2) {
                    eventBadgesHtml += `<div class="event-badge" style="background: #e8eee9; border-left-color: #b0bdb3;">+${dayEvents.length - 2} more</div>`;
                }
                eventBadgesHtml += '</div>';
            }
            
            calendarHTML += `
                <div class="calendar-day ${!isCurrentMonth ? 'other-month' : ''} ${isToday ? 'today' : ''} ${isSelected ? 'selected' : ''}"
                     onclick="selectDate('${dateStr}')">
                    <div class="day-number">${dayNumber}</div>
                    ${eventBadgesHtml}
                </div>
            `;
        }
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        document.getElementById('monthTitle').textContent = `${monthNames[currentMonth]} ${currentYear}`;
        document.getElementById('calendarDays').innerHTML = calendarHTML;
    }

    function selectDate(dateStr) {
        selectedDateStr = dateStr;
        
        console.log('Date selected:', dateStr);
        
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
                        <div class="event-campus">🏛️ ${event.campus} | 👤 ${event.requestor}</div>
                        ${isVehicle && event.vehicle ? `<div class="event-campus">🚗 ${event.vehicle}</div>` : ''}
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
            document.getElementById('upcomingEventsList').innerHTML = '<div class="no-events">No upcoming events scheduled</div>';
            return;
        }
        
        let eventsHTML = '';
        events.forEach(event => {
            const isVehicle = event.type === 'vehicle';
            const isMultiDate = event.is_multi_date;
            const multiDateBadge = isMultiDate ? '<span class="multi-date-indicator">Multiple Dates</span>' : '';
            const typeBadge = isVehicle ? '<span class="multi-date-indicator" style="background:#6c5ce7;">🚐 VEHICLE</span>' : '';
            const eventDate = event.date || (event.event_date ? new Date(event.event_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '');
            const dateLine = eventDate ? `<div class="event-venue">📅 ${eventDate}</div>` : '';
            eventsHTML += `
                <div class="event-item" ${isVehicle ? 'style="border-left-color:#6c5ce7;"' : ''}>
                    <div class="event-time">
                        <div class="time">${event.time}</div>
                    </div>
                    <div class="event-details">
                        <div class="event-name">${event.title} ${typeBadge} ${multiDateBadge}</div>
                        ${dateLine}
                        <div class="event-venue">📍 ${event.venue}</div>
                        <div class="event-campus">🏛️ ${event.campus} | 👤 ${event.requestor}</div>
                        ${isVehicle && event.vehicle ? `<div class="event-campus">🚗 ${event.vehicle}</div>` : ''}
                    </div>
                </div>
            `;
        });
        
        document.getElementById('upcomingEventsList').innerHTML = eventsHTML;
    }

    function changeMonth(direction) {
        currentMonth += direction;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        } else if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
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
        currentYear = today.getFullYear();
        currentMonth = today.getMonth();
        
        const todayStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
        
        loadEvents().then(() => {
            selectDate(todayStr);
            loadUpcomingEvents();
        });
    }

    document.getElementById('campusFilter').addEventListener('change', function() {
        currentMonth = new Date().getMonth();
        currentYear = new Date().getFullYear();
        selectedDateStr = '';
        document.getElementById('selectedDateBadge').innerHTML = 'Select a date';
        document.getElementById('scheduledEventsList').innerHTML = '<div class="no-events">Click on a date to view events</div>';
        
        loadEvents().then(() => {
            loadUpcomingEvents();
        });
    });

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