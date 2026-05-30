@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('content')
<style>
    .detail-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        max-width: 800px;
        margin: 0 auto;
    }
    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e8eee9;
    }
    .detail-label {
        font-weight: 600;
        color: #1a7a3e;
        width: 150px;
        display: inline-block;
    }
    .detail-row {
        margin-bottom: 20px;
        padding: 10px;
        background: #f7faf8;
        border-radius: 8px;
    }
    .btn-approve {
        background: #2db84f;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }
    .btn-reject {
        background: #dc2626;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }
    .btn-back {
        background: #6e7f72;
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
    }
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        justify-content: center;
    }
    .reject-form {
        display: inline;
    }
    .reason-input {
        margin-top: 20px;
        display: none;
    }
    .reason-input.active {
        display: block;
    }
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #e8eee9;
        border-radius: 8px;
        margin-top: 10px;
    }
</style>

<div class="detail-card">
    <div class="detail-header">
        <h2 style="color: #1a7a3e;">{{ $user->name }}</h2>
        <span class="status-badge status-{{ $user->account_status }}">
            {{ ucfirst($user->account_status) }}
        </span>
    </div>
    
    <div class="detail-row">
        <span class="detail-label">📧 Email:</span>
        {{ $user->email }}
    </div>
    
    <div class="detail-row">
        <span class="detail-label">📞 Phone Number:</span>
        {{ $user->phone_number }}
    </div>
    
    <div class="detail-row">
        <span class="detail-label">🏛️ Campus:</span>
        {{ $user->campus->name ?? 'N/A' }}
    </div>
    
    <div class="detail-row">
        <span class="detail-label">📅 Registered:</span>
        {{ $user->created_at->format('F d, Y h:i A') }}
    </div>
    
    @if($user->approved_at)
    <div class="detail-row">
        <span class="detail-label">✅ Approved on:</span>
        {{ $user->approved_at->format('F d, Y h:i A') }}
    </div>
    @endif
    
    @if($user->account_status == 'pending')
    <div class="action-buttons">
        <form method="POST" action="{{ route('admin.users.approve', $user->id) }}">
            @csrf
            <button type="submit" class="btn-approve" onclick="return confirm('Approve this user?')">
                ✅ Approve Account
            </button>
        </form>
        
        <button class="btn-reject" onclick="showRejectForm()">
            ❌ Reject Account
        </button>
    </div>
    
    <div id="rejectForm" class="reason-input">
        <form method="POST" action="{{ route('admin.users.reject', $user->id) }}" id="rejectSubmitForm">
            @csrf
            <label style="font-weight: 600;">Reason for Rejection:</label>
            <textarea name="reason" rows="4" placeholder="Please provide a reason for rejecting this account..." required></textarea>
            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <button type="submit" class="btn-reject" style="padding: 8px 20px;">Confirm Rejection</button>
                <button type="button" class="btn-back" onclick="hideRejectForm()" style="background: #6e7f72;">Cancel</button>
            </div>
        </form>
    </div>
    @endif
    
    <div style="margin-top: 30px; text-align: center;">
        <a href="{{ route('admin.users.index') }}" class="btn-back">← Back to Users List</a>
    </div>
</div>

<script>
    function showRejectForm() {
        document.getElementById('rejectForm').classList.add('active');
    }
    
    function hideRejectForm() {
        document.getElementById('rejectForm').classList.remove('active');
    }
</script>
@endsection