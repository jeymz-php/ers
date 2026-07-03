@extends('layouts.admin')

@section('title', 'Add Vehicle Reservation')
@section('page-title', 'Add Pickup Vehicle Reservation')

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

    .alert-error {
        background: #fef2f2;
        color: #dc2626;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
    }
</style>

<div class="settings-container">
    <div class="settings-card">
        <div class="card-title">🚐 Add Pickup Vehicle Reservation (on behalf of a requester)</div>

        @if($errors->any())
            <div class="alert-error">
                <ul style="margin-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.vehicle-reservations.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label>Requester</label>
                    <select name="user_id" required>
                        <option value="">Select requester...</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Requester Type</label>
                    <select name="requester_type" required>
                        <option value="">Select...</option>
                        <option value="student" {{ old('requester_type') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="professor" {{ old('requester_type') == 'professor' ? 'selected' : '' }}>Professor</option>
                        <option value="admin" {{ old('requester_type') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>From Campus (Origin)</label>
                <select name="origin_campus_id" required>
                    <option value="">Select campus...</option>
                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ old('origin_campus_id') == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                    @endforeach
                </select>
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
                <input type="text" name="other_purpose" value="{{ old('other_purpose') }}" maxlength="255">
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

            <div class="form-row">
                <div class="form-group">
                    <label>Trip Date</label>
                    <input type="date" name="trip_date" value="{{ old('trip_date') }}" required>
                </div>
                <div class="form-group">
                    <label>Pickup Time</label>
                    <input type="time" name="pickup_time" value="{{ old('pickup_time') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label>Additional Details (optional)</label>
                <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>

            <div class="form-group">
                <label>Attachment(s) (optional)</label>
                <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <small style="display: block; margin-bottom: 15px; color: #6e7f72;">This reservation will be created with status <strong>Approved</strong> since it is being added directly by an Admin/Super Admin.</small>

            <button type="submit" class="btn-primary">🚀 Create Reservation</button>
        </form>
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

    toggleOtherPurpose();
    toggleDestinationFields();
</script>
@endsection