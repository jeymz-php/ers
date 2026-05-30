@extends('layouts.app')

@section('title', 'Change Password')

@section('content')

<style>
    @media (max-width: 768px) {
        .auth-page {
            flex-direction: column;
        }
        .hero-panel {
            height: 35vh;
            min-height: 280px;
        }
        .hero-content {
            padding: 20px;
        }
        .hero-logo {
            width: 65px;
            height: 65px;
        }
        .hero-title {
            font-size: 1.4rem;
        }
        .hero-subtitle {
            display: none;
        }
        .auth-panel {
            padding: 25px 20px;
        }
        .auth-inner {
            max-width: 100%;
        }
        .auth-heading {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .hero-panel {
            height: 30vh;
            min-height: 240px;
        }
        .hero-logo {
            width: 55px;
            height: 55px;
        }
        .hero-title {
            font-size: 1.2rem;
        }
        .auth-heading {
            font-size: 1.3rem;
        }
        .auth-panel {
            padding: 20px 15px;
        }
    }
    
    .warning-box {
        background: #fff9e6;
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    
    .warning-title {
        color: #856404;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .warning-text {
        color: #856404;
        font-size: 13px;
    }
</style>

<div class="auth-page">
    <section class="hero-panel" aria-hidden="true">
        <div class="hero-bg"
             style="background-image: url('{{ asset('images/UCC_South.png') }}');"
             aria-label="University of Caloocan City South Campus">
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-edge"></div>

        <div class="hero-content">
            <div class="hero-logo-wrap">
                <img src="{{ asset('images/UCC_Logo.png') }}"
                     alt="University of Caloocan City Official Logo"
                     class="hero-logo" />
            </div>
            <span class="hero-tag">Est. 1975 · Caloocan City</span>
            <h1 class="hero-title">
                Change Your<br />
                <span>Password</span>
            </h1>
            <p class="hero-subtitle">
                Create a new password for your account.
            </p>
        </div>

        <div class="hero-dots" aria-hidden="true">
            <div class="hero-dot"></div>
            <div class="hero-dot"></div>
            <div class="hero-dot active"></div>
        </div>
    </section>

    <main class="auth-panel" role="main">
        <div class="auth-inner">
            <div class="brand-mark">
                <div class="brand-icon">
                    <img src="{{ asset('images/UCC_Logo.png') }}" alt="" />
                </div>
                <div class="brand-text">
                    <span class="brand-name">UCC-ERS</span>
                    <span class="brand-sub">Event Reservation System</span>
                </div>
            </div>

            <h2 class="auth-heading">Create New Password</h2>
            <p class="auth-subheading">
                Please create a new password for your account.
            </p>
            
            <div class="warning-box">
                <div class="warning-title">⚠️ Temporary Password Detected</div>
                <div class="warning-text">
                    You are using a temporary or system-generated password. 
                    Please create a new password below for security purposes.
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.change.update') }}" id="changePasswordForm">
                @csrf

                <div class="field-group">
                    <label class="field-label" for="new_password">New Password</label>
                    <div class="field-wrap">
                        <input id="new_password"
                               type="password"
                               name="new_password"
                               class="field-input"
                               placeholder="Enter new password (min. 8 characters)"
                               required />
                        <button type="button"
                                class="toggle-password"
                                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--gray-400);"
                                onclick="togglePassword('new_password')">
                            <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label" for="new_password_confirmation">Confirm Password</label>
                    <div class="field-wrap">
                        <input id="new_password_confirmation"
                               type="password"
                               name="new_password_confirmation"
                               class="field-input"
                               placeholder="Confirm your new password"
                               required />
                        <button type="button"
                                class="toggle-password"
                                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--gray-400);"
                                onclick="togglePassword('new_password_confirmation')">
                            <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="changeBtn">
                    Update Password
                </button>

                <div class="links-row">
                    <span class="link-text">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    </span>
                </div>
            </form>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <footer class="auth-footer">
                &copy; {{ date('Y') }} University of Caloocan City. All rights reserved.
            </footer>
        </div>
    </main>
</div>

<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = input.nextElementSibling.querySelector('.eye-icon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }

    document.getElementById('changePasswordForm').addEventListener('submit', function() {
        const btn = document.getElementById('changeBtn');
        btn.textContent = 'Updating...';
        btn.disabled = true;
    });
</script>
@endsection