@extends('layouts.admin')

@section('title', 'Edit Administrator')
@section('page-title', 'Edit Administrator Account')

@section('content')
<style>
    .form-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        max-width: 600px;
        margin: 0 auto;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1a7a3e;
    }
    .form-group input, .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        font-size: 14px;
    }
    .btn-submit {
        background: #1a7a3e;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        width: 100%;
        border: none;
        font-size: 15px;
    }
    .btn-submit:hover {
        background: #155e30;
    }
    .btn-back {
        background: #6e7f72;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        text-decoration: none;
        display: block;
        text-align: center;
        margin-top: 10px;
    }
    .info-text {
        background: #e8eee9;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #3c4a3f;
    }
    @media (max-width: 768px) {
        .form-card { padding: 20px; }
    }
</style>

<div class="form-card">
    <div class="info-text">
        ✏️ <strong>Editing account:</strong> {{ $admin->name }}
        — Password is not changed here.
    </div>

    @if ($errors->any())
        <div style="background: #fee2e2; color: #dc2626; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="name" required value="{{ old('name', $admin->name) }}">
            @error('name') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Email Address *</label>
            <input type="email" name="email" required value="{{ old('email', $admin->email) }}">
            @error('email') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Phone Number *</label>
            <input type="tel" name="phone_number" required value="{{ old('phone_number', $admin->phone_number) }}">
            @error('phone_number') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Campus *</label>
            <select name="campus_id" required>
                <option value="">Select Campus</option>
                @foreach($campuses as $campus)
                    <option value="{{ $campus->id }}" {{ old('campus_id', $admin->campus_id) == $campus->id ? 'selected' : '' }}>
                        {{ $campus->name }}
                    </option>
                @endforeach
            </select>
            @error('campus_id') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Role *</label>
            <select name="role" required>
                <option value="admin" {{ old('role', $admin->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="super_admin" {{ old('role', $admin->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
            </select>
            <small style="font-size: 11px; color: #6e7f72;">Super Admin can manage other admins and has full access.</small>
            @error('role') <small style="color: #dc2626;">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn-submit">Save Changes</button>
    </form>

    <a href="{{ route('admin.admins.index') }}" class="btn-back">← Back to List</a>
</div>
@endsection