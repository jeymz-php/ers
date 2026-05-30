@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<style>
    .settings-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .settings-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    .card-title {
        font-size: 24px;
        font-weight: 700;
        color: #1a7a3e;
        margin-bottom: 10px;
    }
    
    .card-subtitle {
        color: #6e7f72;
        font-size: 14px;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8eee9;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1a7a3e;
        font-size: 13px;
    }
    
    .password-field {
        position: relative;
    }
    
    .password-field input {
        width: 100%;
        padding: 12px 45px 12px 15px;
        border: 1px solid #e8eee9;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .password-field input:focus {
        outline: none;
        border-color: #2db84f;
        box-shadow: 0 0 0 3px rgba(45, 184, 79, 0.1);
    }
    
    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #b0bdb3;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    .toggle-password:hover {
        color: #1a7a3e;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        width: 100%;
        font-size: 16px;
        transition: all 0.3s;
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(34,145,63,0.3);
    }
    
    .alert {
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .alert-success {
        background: #d4f5df;
        color: #1a7a3e;
        border: 1px solid #b3edca;
    }
    
    .alert-error {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    
    .info-text {
        background: #f0faf3;
        padding: 15px;
        border-radius: 10px;
        margin-top: 20px;
        font-size: 13px;
        color: #6e7f72;
        text-align: center;
    }
    
    @media (max-width: 768px) {
        .settings-container {
            padding: 15px;
        }
        .settings-card {
            padding: 20px;
        }
        .card-title {
            font-size: 20px;
        }
        .password-field input {
            padding: 10px 40px 10px 12px;
        }
    }
</style>

<div class="user-container">
    @include('partials.user-sidebar')

    <main class="user-main">
        @include('partials.user-topbar')

        <div class="content-area">
            <div class="settings-container">
                <div class="settings-card">
                    <div class="card-title">🔐 Change Password</div>
                    <div class="card-subtitle">Update your account password</div>
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-error">{{ session('error') }}</div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-error">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('user.settings.password') }}" id="passwordForm">
                        @csrf
                        
                        <div class="form-group">
                            <label>Current Password</label>
                            <div class="password-field">
                                <input type="password" id="current_password" name="current_password" required placeholder="Enter your current password">
                                <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>New Password</label>
                            <div class="password-field">
                                <input type="password" id="new_password" name="new_password" required placeholder="Enter new password (min. 8 characters)">
                                <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <div class="password-field">
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" required placeholder="Confirm your new password">
                                <button type="button" class="toggle-password" onclick="togglePassword('new_password_confirmation')">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-save" id="saveBtn">Update Password</button>
                    </form>
                    
                    <div class="info-text">
                        <strong>💡 Password Tips:</strong><br>
                        Use at least 8 characters with a mix of letters, numbers, and symbols.
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = input.nextElementSibling.querySelector('svg');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }
    
    document.getElementById('passwordForm').addEventListener('submit', function() {
        const btn = document.getElementById('saveBtn');
        btn.textContent = 'Updating...';
        btn.disabled = true;
    });
</script>
@endsection