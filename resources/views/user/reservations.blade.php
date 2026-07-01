@extends('layouts.app')

@section('title', 'Reservations')

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

    /* Reservations Container */
    .reservations-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        height: calc(100vh - 180px);
        overflow: hidden;
    }

    /* Campuses List Section */
    .campuses-section {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
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
        flex-shrink: 0;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 18px;
        font-weight: 700;
        color: #1a7a3e;
    }

    .section-title img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: contain;
    }

    /* Scrollable Campuses */
    .campuses-scroll {
        flex: 1;
        overflow-y: auto;
        padding-right: 5px;
    }

    .campus-card {
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
        position: relative;
        background-size: cover;
        background-position: center;
        min-height: 120px;
        overflow: hidden;
    }

    .campus-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 0;
    }

    .campus-card:hover {
        transform: translateX(5px);
    }

    .campus-card.selected {
        border-color: #2db84f;
        box-shadow: 0 4px 20px rgba(45, 184, 79, 0.3);
    }

    .campus-content {
        position: relative;
        z-index: 1;
        color: white;
    }

    .campus-name {
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 8px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }

    .campus-code {
        font-size: 12px;
        opacity: 0.9;
        text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
    }

    /* Establishments Section */
    .establishments-section {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }

    /* Content container */
    #establishmentsContent {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Search and Filter Bar */
    .search-filter-wrapper {
        flex-shrink: 0;
        margin-bottom: 15px;
    }

    .search-filter-card {
        background: #f7faf8;
        border-radius: 12px;
        padding: 15px;
    }

    .search-filter-grid {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 15px;
        align-items: end;
    }

    .filter-group {
        margin-bottom: 0;
    }

    .filter-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #1a7a3e;
        font-size: 12px;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 13px;
    }

    /* Result count */
    .result-count {
        font-size: 12px;
        color: #6e7f72;
        margin-bottom: 10px;
        padding: 5px 0;
        flex-shrink: 0;
    }

    /* SCROLLABLE ESTABLISHMENTS WRAPPER */
    .establishments-wrapper {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 8px;
        margin-top: 5px;
    }

    .establishments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        padding-bottom: 10px;
    }

    /* Custom Scrollbar */
    .campuses-scroll::-webkit-scrollbar,
    .establishments-wrapper::-webkit-scrollbar {
        width: 6px;
    }

    .campuses-scroll::-webkit-scrollbar-track,
    .establishments-wrapper::-webkit-scrollbar-track {
        background: #f0faf3;
        border-radius: 10px;
    }

    .campuses-scroll::-webkit-scrollbar-thumb,
    .establishments-wrapper::-webkit-scrollbar-thumb {
        background: #b0bdb3;
        border-radius: 10px;
    }

    /* Establishment Card */
    .establishment-card {
        background: #f7faf8;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s;
        cursor: pointer;
        border: 1px solid #e8eee9;
    }

    .establishment-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .card-header {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        padding: 15px;
        color: white;
    }

    .card-header h3 {
        font-size: 16px;
        margin-bottom: 5px;
    }

    .card-type {
        font-size: 10px;
        opacity: 0.9;
    }

    .card-body {
        padding: 15px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e8eee9;
    }

    .info-label {
        font-weight: 600;
        color: #6e7f72;
        font-size: 11px;
    }

    .info-value {
        color: #1a7a3e;
        font-weight: 700;
        font-size: 16px;
    }

    .btn-view {
        background: #2db84f;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        width: 100%;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        margin-top: 10px;
        font-size: 12px;
    }

    .btn-view:hover {
        background: #1a7a3e;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #b0bdb3;
    }

    .selected-campus-info {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        padding: 12px 15px;
        border-radius: 12px;
        margin-bottom: 15px;
        text-align: center;
        flex-shrink: 0;
    }

    .selected-campus-name {
        font-size: 16px;
        font-weight: 700;
    }

    /* Modal Styles - Fixed for Mobile */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        z-index: 1000;
        overflow-y: auto;
        padding: 20px;
    }

    .modal.active {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        width: 100%;
        max-width: 1300px;
        position: relative;
        animation: slideIn 0.3s ease;
        overflow: hidden;
        margin: 20px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        padding: 20px 25px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 10;
        flex-shrink: 0;
    }

    .modal-header h3 {
        font-size: 20px;
    }

    .close-modal {
        background: none;
        border: none;
        color: white;
        font-size: 28px;
        cursor: pointer;
    }

    .modal-body {
        padding: 25px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        overflow-y: auto;
        flex: 1;
    }

    /* Calendar Styles */
    .calendar-section {
        background: #f7faf8;
        border-radius: 16px;
        padding: 20px;
        position: sticky;
        top: 0;
        align-self: flex-start;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .month-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a7a3e;
    }

    .calendar-nav {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .nav-btn {
        background: white;
        border: 1px solid #e8eee9;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
    }

    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
        margin-bottom: 10px;
    }

    .weekday {
        text-align: center;
        padding: 8px;
        font-weight: 700;
        color: #1a7a3e;
        font-size: 12px;
    }

    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
    }

    .calendar-day {
        min-height: 80px;
        background: white;
        border-radius: 8px;
        padding: 8px;
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid #e8eee9;
        position: relative;
    }

    .calendar-day:hover {
        background: #e8eee9;
        transform: scale(1.02);
    }

    .calendar-day.booked {
        background: #fef2f2;
        border-color: #dc2626;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .calendar-day.selected {
        background: #d4f5df;
        border: 2px solid #2db84f;
    }

    .calendar-day.other-month {
        opacity: 0.4;
    }

    .calendar-day.past {
        background: #e8eee9;
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .day-number {
        font-weight: 600;
        font-size: 12px;
        color: #3c4a3f;
    }

    /* Form Styles */
    .form-section {
        background: #f7faf8;
        border-radius: 16px;
        padding: 20px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #1a7a3e;
        font-size: 13px;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 14px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 15px 0;
    }

    .equipment-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        padding: 8px 12px;
        border-radius: 8px;
        margin-bottom: 8px;
        border: 1px solid #e8eee9;
    }

    .remove-equipment {
        background: #dc2626;
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 11px;
    }

    .add-equipment-btn {
        background: #2db84f;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
    }

    .selected-dates {
        background: #d4f5df;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 12px;
        word-break: break-word;
    }

    .btn-submit {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        width: 100%;
        margin-top: 20px;
    }

    /* File Upload Styles */
    .file-upload-container {
        margin-top: 10px;
    }

    .file-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        padding: 8px 12px;
        border-radius: 8px;
        margin-bottom: 8px;
        border: 1px solid #e8eee9;
    }

    .file-name {
        font-size: 12px;
        color: #3c4a3f;
        word-break: break-all;
    }

    .file-size {
        font-size: 10px;
        color: #6e7f72;
        margin-left: 8px;
    }

    .remove-file {
        background: #dc2626;
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 11px;
    }

    .add-file-btn {
        background: #2db84f;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        margin-top: 10px;
        width: 100%;
    }

    /* Mobile Responsive */
    @media (max-width: 1024px) {
        .user-main {
            margin-left: 0;
        }
        .reservations-container {
            grid-template-columns: 1fr;
            height: auto;
            gap: 20px;
        }
        .campuses-section {
            height: 250px;
        }
        .establishments-section {
            height: 1100px;
        }
    }

    @media (max-width: 768px) {
        .content-area {
            padding: 15px;
            padding-top: 70px;
        }
        .search-filter-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .filter-group {
            width: 100%;
        }
        .filter-group input,
        .filter-group select {
            width: 100% !important;
        }
        .establishments-grid {
            grid-template-columns: 1fr;
        }
        
        /* Mobile Modal Fix */
        .modal {
            padding: 10px;
        }
        .modal-content {
            margin: 10px;
            max-height: 95vh;
        }
        .modal-body {
            grid-template-columns: 1fr;
            padding: 15px;
            gap: 15px;
        }
        .modal-header {
            padding: 15px 20px;
        }
        .modal-header h3 {
            font-size: 16px;
        }
        .calendar-section {
            padding: 15px;
            position: relative;
            top: auto;
        }
        .calendar-header {
            flex-direction: column;
            align-items: stretch;
        }
        .calendar-nav {
            justify-content: center;
        }
        .calendar-weekdays .weekday {
            font-size: 10px;
            padding: 5px;
        }
        .calendar-day {
            min-height: 60px;
            padding: 5px;
        }
        .day-number {
            font-size: 10px;
        }
        .form-section {
            padding: 15px;
        }
        .btn-submit {
            padding: 10px 20px;
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .content-area {
            padding: 10px;
            padding-top: 60px;
        }
        .campus-card {
            min-height: 100px;
        }
        .campus-name {
            font-size: 14px;
        }
        .card-header h3 {
            font-size: 14px;
        }
        .calendar-day {
            min-height: 50px;
        }
        .weekday {
            font-size: 9px;
            padding: 4px;
        }
        .calendar-section {
            padding: 10px;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            font-size: 13px;
            padding: 8px;
        }
    }
</style>

<div class="user-container">
    @include('partials.user-sidebar')

    <main class="user-main">
        @include('partials.user-topbar')

        <div class="content-area">
            <div class="reservations-container">
                <!-- Left: Campuses List -->
                <div class="campuses-section">
                    <div class="section-header">
                        <div class="section-title">
                            <img src="{{ asset('images/UCC_Logo.png') }}" alt="UCC Logo">
                            Select Campus
                        </div>
                    </div>
                    <div class="campuses-scroll" id="campusesScroll">
                        @foreach($campuses as $campus)
                        <div class="campus-card" 
                             data-id="{{ $campus->id }}"
                             data-name="{{ $campus->name }}"
                             style="background-image: url('{{ asset('images/UCC_South.png') }}');">
                            <div class="campus-content">
                                <div class="campus-name">{{ $campus->name }}</div>
                                <div class="campus-code">{{ $campus->address ?? 'Caloocan City' }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right: Establishments List -->
                <div class="establishments-section">
                    <div class="section-header">
                        <div class="section-title">📍 Available Establishments</div>
                    </div>
                    
                    <div id="establishmentsContent">
                        <div class="empty-state">🏛️ Select a campus to view establishments</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Availability Modal -->
<div id="availabilityModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Schedule of Events</h3>
            <button class="close-modal" onclick="closeAvailabilityModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="calendar-section">
                <div class="calendar-header">
                    <div class="month-title" id="calendarMonth">May 2026</div>
                    <div class="calendar-nav">
                        <button class="nav-btn" onclick="changeMonth(-1)">←</button>
                        <button class="nav-btn" onclick="goToToday()">Today</button>
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
                <p style="font-size: 11px; color: #6e7f72; margin-top: 15px; text-align: center;">
                    📌 Click on a future date to request a reservation. Red dates are booked.
                </p>
            </div>

            <div class="form-section">
                <h4 style="color: #1a7a3e; margin-bottom: 15px;" id="establishmentNameDisplay"></h4>
                
                <form id="reservationForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="establishment_id" name="establishment_id">
                    
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" value="{{ Auth::user()->name }}" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>I am a</label>
                        <select id="user_type" name="user_type" required>
                            <option value="">Select</option>
                            <option value="student">Student</option>
                            <option value="professor">Professor</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Department (Optional)</label>
                        <input type="text" id="department" name="department" placeholder="e.g., College of Computer Studies">
                    </div>
                    
                    <div class="form-group">
                        <label>Event Name</label>
                        <input type="text" id="event_name" name="event_name" required placeholder="Enter event name">
                    </div>
                    
                    <div class="form-group">
                        <label>Event Objectives</label>
                        <textarea id="event_objectives" name="event_objectives" placeholder="Describe your event objectives..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Reservation Dates</label>
                        <div id="selectedDatesList" class="selected-dates">
                            No dates selected. Click on dates in the calendar.
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Event Start Time</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Event End Time</label>
                        <input type="time" id="end_time" name="end_time" required>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="request_equipment">
                        <label for="request_equipment">Request Equipment</label>
                    </div>
                    
                    <div id="equipmentSection" style="display: none;">
                        <div class="form-group">
                            <label>Equipment Name</label>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                <input type="text" id="equipment_name" placeholder="e.g., Sound System" style="flex: 1;">
                                <button type="button" class="add-equipment-btn" onclick="addEquipment()">Add</button>
                            </div>
                        </div>
                        <div id="equipmentList"></div>
                    </div>
                    
                    <!-- File Upload Section with Add Another File Button -->
                    <div class="form-group">
                        <label>Attachments</label>
                        <div id="filesContainer">
                            <div class="file-item" style="display: none;" id="fileTemplate">
                                <div class="file-info">
                                    <span class="file-name"></span>
                                    <span class="file-size"></span>
                                </div>
                                <button type="button" class="remove-file" onclick="removeFile(this)">Remove</button>
                            </div>
                        </div>
                        <div id="fileList"></div>
                        <button type="button" class="add-file-btn" onclick="addFileInput()">+ Add Another File</button>
                        <small style="font-size: 10px; display: block; margin-top: 5px;">Max 5 files, 15MB each (PDF, JPG, PNG)</small>
                    </div>
                    
                    <button type="submit" class="btn-submit">CONFIRM RESERVATION</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let currentCampusId = null;
    let currentEstablishmentId = null;
    let currentEstablishmentName = '';
    let allEstablishments = [];
    let bookedDates = {};
    let selectedDates = [];
    let equipmentList = [];
    let attachedFiles = []; // Array to store File objects
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let filterTimeout = null;
    let fileCounter = 0;

    // ========== FILE UPLOAD FUNCTIONS ==========
    function addFileInput() {
        if (attachedFiles.length >= 5) {
            alert('Maximum 5 files allowed.');
            return;
        }
        
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.pdf,.jpg,.jpeg,.png';
        input.style.display = 'none';
        input.onchange = function(e) {
            handleFileSelect(e, input);
        };
        document.body.appendChild(input);
        input.click();
    }
    
    function handleFileSelect(event, inputElement) {
        const file = event.target.files[0];
        if (!file) return;
        
        const maxSize = 15 * 1024 * 1024;
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        
        if (!allowedTypes.includes(file.type)) {
            alert(`Invalid file type: ${file.name}. Only PDF, JPG, PNG allowed.`);
            inputElement.remove();
            return;
        }
        
        if (file.size > maxSize) {
            alert(`File too large: ${file.name}. Maximum size is 15MB.`);
            inputElement.remove();
            return;
        }
        
        attachedFiles.push(file);
        displayFileList();
        inputElement.remove();
    }
    
    function displayFileList() {
        const fileListDiv = document.getElementById('fileList');
        if (!fileListDiv) return;
        
        if (attachedFiles.length === 0) {
            fileListDiv.innerHTML = '';
            return;
        }
        
        let html = '<div style="background: #f0faf3; padding: 10px; border-radius: 8px; margin-top: 5px;"><strong>Selected Files (' + attachedFiles.length + '):</strong><ul style="margin: 8px 0 0 20px;">';
        attachedFiles.forEach((file, index) => {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            html += `<li style="margin-bottom: 5px;">${file.name} (${fileSize} MB) <button type="button" onclick="removeFileByIndex(${index})" style="background: #dc2626; color: white; border: none; border-radius: 4px; padding: 2px 8px; margin-left: 8px; cursor: pointer;">Remove</button></li>`;
        });
        html += '</ul></div>';
        fileListDiv.innerHTML = html;
    }
    
    function removeFileByIndex(index) {
        attachedFiles.splice(index, 1);
        displayFileList();
    }
    
    function removeFile(button) {
        const fileItem = button.closest('.file-item');
        const fileName = fileItem.querySelector('.file-name').textContent;
        attachedFiles = attachedFiles.filter(f => f.name !== fileName);
        displayFileList();
    }

    // ========== CAMPUS SELECTION FUNCTIONS ==========
    function selectCampus(id, name) {
        currentCampusId = id;
        
        document.querySelectorAll('.campus-card').forEach(card => {
            card.classList.remove('selected');
        });
        const selectedCard = document.querySelector(`.campus-card[data-id="${id}"]`);
        if (selectedCard) {
            selectedCard.classList.add('selected');
        }
        
        loadEstablishments(id);
    }

    function loadEstablishments(campusId) {
        const container = document.getElementById('establishmentsContent');
        container.innerHTML = '<div class="empty-state">⏳ Loading establishments...</div>';
        
        fetch(`/api/campuses/${campusId}/establishments`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allEstablishments = data.establishments;
                    renderEstablishments(allEstablishments, data.campus);
                } else {
                    container.innerHTML = '<div class="empty-state">❌ Error loading establishments</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<div class="empty-state">❌ Error loading establishments</div>';
            });
    }

    function renderEstablishments(establishments, campus) {
        const container = document.getElementById('establishmentsContent');
        
        let html = `
            <div class="selected-campus-info">
                <div class="selected-campus-name">🏛️ ${campus.name}</div>
            </div>
            <div class="search-filter-wrapper">
                <div class="search-filter-card">
                    <div class="search-filter-grid">
                        <div class="filter-group">
                            <label>🔍 Search Venues</label>
                            <input type="text" id="searchVenue" placeholder="Search by name...">
                        </div>
                        <div class="filter-group">
                            <label>🏷️ Filter by Type</label>
                            <select id="filterType">
                                <option value="all">All Types</option>
                                <option value="Indoor">Indoor</option>
                                <option value="Outdoor">Outdoor</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>📊 Sort By</label>
                            <select id="sortBy">
                                <option value="name">Name (A-Z)</option>
                                <option value="capacity_asc">Capacity (Low to High)</option>
                                <option value="capacity_desc">Capacity (High to Low)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        if (establishments.length === 0) {
            html += '<div class="empty-state">📌 No establishments found for this campus.</div>';
            container.innerHTML = html;
            attachFilterEvents();
            return;
        }
        
        html += `<div class="result-count">📊 Showing ${establishments.length} establishment(s)</div>`;
        html += `<div class="establishments-wrapper"><div class="establishments-grid" id="establishmentsGrid">`;
        
        establishments.forEach(est => {
            html += `
                <div class="establishment-card" onclick="showAvailabilityModal(${est.id}, '${est.name}', ${est.capacity}, '${est.type}')">
                    <div class="card-header">
                        <h3>${escapeHtml(est.name)}</h3>
                        <div class="card-type">${est.type}</div>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Capacity</span>
                            <span class="info-value">${est.capacity.toLocaleString()}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Type</span>
                            <span class="info-value">${est.type}</span>
                        </div>
                        <button class="btn-view">View Availability →</button>
                    </div>
                </div>
            `;
        });
        
        html += `</div></div>`;
        container.innerHTML = html;
        attachFilterEvents();
    }

    function attachFilterEvents() {
        const searchInput = document.getElementById('searchVenue');
        const filterSelect = document.getElementById('filterType');
        const sortSelect = document.getElementById('sortBy');
        
        if (searchInput) {
            searchInput.removeEventListener('input', handleSearchInput);
            searchInput.addEventListener('input', handleSearchInput);
        }
        
        if (filterSelect) {
            filterSelect.removeEventListener('change', filterEstablishments);
            filterSelect.addEventListener('change', filterEstablishments);
        }
        
        if (sortSelect) {
            sortSelect.removeEventListener('change', filterEstablishments);
            sortSelect.addEventListener('change', filterEstablishments);
        }
    }
    
    function handleSearchInput(e) {
        if (filterTimeout) clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            filterEstablishments();
        }, 300);
    }

    function filterEstablishments() {
        const searchInput = document.getElementById('searchVenue');
        const filterSelect = document.getElementById('filterType');
        const sortSelect = document.getElementById('sortBy');
        
        if (!searchInput) return;
        
        const searchTerm = searchInput.value.toLowerCase();
        const filterType = filterSelect ? filterSelect.value : 'all';
        const sortBy = sortSelect ? sortSelect.value : 'name';
        
        let filtered = allEstablishments.filter(est => {
            const matchesSearch = est.name.toLowerCase().includes(searchTerm);
            const matchesType = filterType === 'all' || est.type === filterType;
            return matchesSearch && matchesType;
        });
        
        if (sortBy === 'name') {
            filtered.sort((a, b) => a.name.localeCompare(b.name));
        } else if (sortBy === 'capacity_asc') {
            filtered.sort((a, b) => a.capacity - b.capacity);
        } else if (sortBy === 'capacity_desc') {
            filtered.sort((a, b) => b.capacity - a.capacity);
        }
        
        const campusNameElement = document.querySelector('.selected-campus-name');
        const campusName = campusNameElement ? campusNameElement.innerText.replace('🏛️ ', '') : '';
        renderEstablishments(filtered, { name: campusName });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Make campus cards clickable
    document.querySelectorAll('.campus-card').forEach(card => {
        card.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.querySelector('.campus-name').innerText;
            selectCampus(parseInt(id), name);
        });
    });

    // Auto-select first campus on load
    document.addEventListener('DOMContentLoaded', function() {
        const firstCard = document.querySelector('.campus-card');
        if (firstCard) {
            const id = firstCard.getAttribute('data-id');
            const name = firstCard.querySelector('.campus-name').innerText;
            setTimeout(() => {
                selectCampus(parseInt(id), name);
            }, 100);
        }
    });

    // ========== CALENDAR FUNCTIONS ==========
    function showAvailabilityModal(id, name, capacity, type) {
        currentEstablishmentId = id;
        currentEstablishmentName = name;
        
        document.getElementById('establishment_id').value = id;
        document.getElementById('establishmentNameDisplay').innerHTML = `<strong>${name}</strong><br><small>Capacity: ${capacity} | Type: ${type}</small>`;
        document.getElementById('modalTitle').innerHTML = `📅 ${name} - Schedule of Events`;
        
        fetch(`/reservations/availability/${id}`)
            .then(response => response.json())
            .then(data => {
                bookedDates = data.booked_dates || {};
                renderCalendar();
            });
        
        document.getElementById('availabilityModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeAvailabilityModal() {
        document.getElementById('availabilityModal').classList.remove('active');
        document.body.style.overflow = '';
        resetForm();
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
            let isBooked = false;
            let isPast = false;
            let dateStr = '';
            
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
                if (checkDate < today) isPast = true;
                if (bookedDates[dateStr]) isBooked = true;
            }
            
            const isSelected = selectedDates.includes(dateStr);
            
            calendarHTML += `
                <div class="calendar-day ${!isCurrentMonth ? 'other-month' : ''} ${isToday ? 'today' : ''} ${isBooked ? 'booked' : ''} ${isSelected ? 'selected' : ''} ${isPast ? 'past' : ''}"
                    onclick="${!isPast && !isBooked ? `selectDate('${dateStr}')` : ''}">
                    <div class="day-number">${dayNumber}</div>
                </div>
            `;
        }
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        document.getElementById('calendarMonth').textContent = `${monthNames[currentMonth]} ${currentYear}`;
        document.getElementById('calendarDays').innerHTML = calendarHTML;
    }

    function selectDate(dateStr) {
        const index = selectedDates.indexOf(dateStr);
        if (index === -1) {
            selectedDates.push(dateStr);
        } else {
            selectedDates.splice(index, 1);
        }
        updateSelectedDatesDisplay();
        renderCalendar();
    }

    function updateSelectedDatesDisplay() {
        const container = document.getElementById('selectedDatesList');
        if (selectedDates.length === 0) {
            container.innerHTML = 'No dates selected. Click on dates in the calendar.';
        } else {
            const formattedDates = selectedDates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }).join(', ');
            container.innerHTML = `📅 Selected: ${formattedDates}`;
        }
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
        renderCalendar();
    }

    function goToToday() {
        const today = new Date();
        currentYear = today.getFullYear();
        currentMonth = today.getMonth();
        renderCalendar();
    }

    // ========== EQUIPMENT FUNCTIONS ==========
    document.getElementById('request_equipment').addEventListener('change', function() {
        document.getElementById('equipmentSection').style.display = this.checked ? 'block' : 'none';
        if (!this.checked) {
            equipmentList = [];
            renderEquipmentList();
        }
    });

    function addEquipment() {
        const name = document.getElementById('equipment_name').value.trim();
        if (name === '') {
            alert('Please enter equipment name');
            return;
        }
        equipmentList.push(name);
        document.getElementById('equipment_name').value = '';
        renderEquipmentList();
    }

    function removeEquipment(index) {
        equipmentList.splice(index, 1);
        renderEquipmentList();
    }

    function renderEquipmentList() {
        const container = document.getElementById('equipmentList');
        if (equipmentList.length === 0) {
            container.innerHTML = '';
            return;
        }
        let html = '<h5 style="margin: 10px 0 5px;">Requested Equipment:</h5>';
        equipmentList.forEach((item, index) => {
            html += `<div class="equipment-item"><span>🔧 ${item}</span><button type="button" class="remove-equipment" onclick="removeEquipment(${index})">Remove</button></div>`;
        });
        container.innerHTML = html;
    }

    // ========== FORM SUBMISSION ==========
    document.getElementById('reservationForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (selectedDates.length === 0) {
            alert('Please select at least one date.');
            return;
        }
        
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        
        if (!startTime || !endTime) {
            alert('Please select start and end times.');
            return;
        }
        
        if (startTime >= endTime) {
            alert('End time must be after start time.');
            return;
        }
        
        const formData = new FormData();
        formData.append('establishment_id', currentEstablishmentId);
        formData.append('event_name', document.getElementById('event_name').value);
        formData.append('event_objectives', document.getElementById('event_objectives').value);
        formData.append('user_type', document.getElementById('user_type').value);
        formData.append('department', document.getElementById('department').value);
        formData.append('start_time', startTime);
        formData.append('end_time', endTime);
        formData.append('event_dates', JSON.stringify(selectedDates));
        formData.append('equipment', JSON.stringify(equipmentList));
        
        // Append all files
        for (let i = 0; i < attachedFiles.length; i++) {
            formData.append('attachments[]', attachedFiles[i]);
        }
        
        console.log('Submitting with', attachedFiles.length, 'file(s)');
        
        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.textContent = 'Submitting...';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('{{ route("user.reservations.store") }}', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                },
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                alert(data.message);
                closeAvailabilityModal();
                window.location.href = '{{ route("user.summary") }}';
            } else {
                alert(data.message || 'Error creating reservation');
            }
        } catch (error) {
            console.error('Submission error:', error);
            alert('An error occurred. Please try again.');
        } finally {
            submitBtn.textContent = 'CONFIRM RESERVATION';
            submitBtn.disabled = false;
        }
    });

    function resetForm() {
        selectedDates = [];
        equipmentList = [];
        attachedFiles = [];
        document.getElementById('reservationForm').reset();
        document.getElementById('equipmentSection').style.display = 'none';
        document.getElementById('request_equipment').checked = false;
        document.getElementById('fileList').innerHTML = '';
        updateSelectedDatesDisplay();
        renderEquipmentList();
    }
</script>
@endsection