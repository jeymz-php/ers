@extends('layouts.app')

@section('title', 'Reset Password')

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
                Set New<br />
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
                Please enter your new password below.
            </p>

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

            <form method="POST" action="{{ route('password.update') }}" id="resetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="field-group">
                    <label class="field-label" for="password">New Password</label>
                    <div class="field-wrap">
                        <input id="password"
                               type="password"
                               name="password"
                               class="field-input"
                               placeholder="••••••••"
                               required />
                        <button type="button"
                                class="toggle-password"
                                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--gray-400);"
                                onclick="togglePassword(this, 'password')">
                            <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label" for="password_confirmation">Confirm Password</label>
                    <div class="field-wrap">
                        <input id="password_confirmation"
                               type="password"
                               name="password_confirmation"
                               class="field-input"
                               placeholder="Confirm your password"
                               required />
                        <button type="button"
                                class="toggle-password"
                                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--gray-400);"
                                onclick="togglePassword(this, 'password_confirmation')">
                            <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="resetBtn">
                    Reset Password
                </button>

                <div class="links-row">
                    <span class="link-text">
                        <a href="{{ route('login') }}">Back to Login</a>
                    </span>
                </div>
            </form>

            <footer class="auth-footer">
                &copy; {{ date('Y') }} University of Caloocan City. All rights reserved.
            </footer>
        </div>
    </main>
</div>

<script>
    function togglePassword(btn, inputId) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('.eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }

    document.getElementById('resetForm').addEventListener('submit', function() {
        const btn = document.getElementById('resetBtn');
        btn.textContent = 'Resetting...';
        btn.disabled = true;
    });
</script>
@endsection