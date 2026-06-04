@extends('layouts.app')

@section('title', 'Login')

@section('content')

<style>
    /* Mobile Responsive Styles for Login */
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
            padding: 8px;
        }
        
        .hero-tag {
            font-size: 0.6rem;
            padding: 0.25rem 0.8rem;
        }
        
        .hero-title {
            font-size: 1.4rem;
        }
        
        .hero-subtitle {
            display: none;
        }
        
        .hero-edge {
            clip-path: polygon(0 70%, 100% 40%, 100% 100%, 0% 100%);
            width: 100%;
            height: 60px;
            top: auto;
            bottom: -1px;
        }
        
        .hero-dots {
            bottom: 1rem;
        }
        
        .auth-panel {
            padding: 25px 20px;
        }
        
        .auth-inner {
            max-width: 100%;
        }
        
        .brand-mark {
            margin-bottom: 1.5rem;
        }
        
        .brand-icon {
            width: 35px;
            height: 35px;
        }
        
        .brand-icon img {
            width: 20px;
            height: 20px;
        }
        
        .brand-name {
            font-size: 0.7rem;
        }
        
        .brand-sub {
            font-size: 0.55rem;
        }
        
        .auth-heading {
            font-size: 1.5rem;
        }
        
        .auth-subheading {
            font-size: 0.75rem;
            margin-bottom: 1.25rem;
        }
        
        .field-label {
            font-size: 0.7rem;
        }
        
        .field-input {
            padding: 0.6rem 2rem 0.6rem 0.75rem;
            font-size: 0.85rem;
        }
        
        .btn-primary {
            padding: 0.7rem 1rem;
            font-size: 0.8rem;
        }
        
        .links-row {
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        
        .link-text, .links-row a {
            font-size: 0.75rem;
        }
        
        .auth-footer {
            font-size: 0.6rem;
            margin-top: 1.5rem;
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
            padding: 7px;
        }
        
        .hero-title {
            font-size: 1.2rem;
        }
        
        .hero-tag {
            font-size: 0.55rem;
        }
        
        .auth-panel {
            padding: 20px 15px;
        }
        
        .auth-heading {
            font-size: 1.3rem;
        }
        
        .field-input {
            padding: 0.5rem 2rem 0.5rem 0.7rem;
            font-size: 0.8rem;
        }
        
        .btn-primary {
            padding: 0.6rem 1rem;
        }
    }
</style>

<div class="auth-page">

    {{-- LEFT HERO PANEL --}}
    <section class="hero-panel" aria-hidden="true">
        <div class="hero-bg"
             style="background-image: url('{{ asset('images/UCC_South.png') }}');"
             role="img"
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
                University of<br />
                <span>Caloocan City</span>
            </h1>
            <p class="hero-subtitle">
                Streamline your campus events — reserve venues, manage schedules,
                and coordinate activities all in one unified platform.
            </p>
        </div>

        <div class="hero-dots" aria-hidden="true">
            <div class="hero-dot active"></div>
            <div class="hero-dot"></div>
            <div class="hero-dot"></div>
        </div>
    </section>

    {{-- RIGHT LOGIN PANEL --}}
    <main class="auth-panel" role="main">
        <div class="auth-inner">

            {{-- Brand mark --}}
            <div class="brand-mark">
                <div class="brand-icon">
                    <img src="{{ asset('images/UCC_Logo.png') }}" alt="" aria-hidden="true" />
                </div>
                <div class="brand-text">
                    <span class="brand-name">UCC-ERS</span>
                    <span class="brand-sub">Event Reservation System</span>
                </div>
            </div>

            @php
                $adminMode = $adminMode ?? false;
            @endphp

            {{-- Heading --}}
            <h2 class="auth-heading">{{ $adminMode ? 'Admin Login' : 'Welcome Back' }}</h2>
            <p class="auth-subheading">
                {{ $adminMode ? 'Admin access is available through the hidden login entry.' : 'Sign in to access your dashboard and manage event reservations.' }}
            </p>

            {{-- Alerts --}}
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Login Form --}}
            @php $loginAction = $adminMode ? route('admin.login.submit') : route('login'); @endphp
            <form method="POST" action="{{ $loginAction }}" id="loginForm">
                @csrf
                @if($adminMode)
                    <input type="hidden" name="admin_mode" value="1">
                @endif

                {{-- Email --}}
                <div class="field-group">
                    <label class="field-label" for="email">Email Address</label>
                    <div class="field-wrap">
                        <input id="email"
                               type="email"
                               name="email"
                               class="field-input @error('email') is-invalid @enderror"
                               placeholder="you@ucc.edu.ph"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="email" />
                        <span class="field-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="2" y="4" width="20" height="16" rx="3"/>
                                <path d="M22 7l-10 7L2 7"/>
                            </svg>
                        </span>
                    </div>
                </div>

                {{-- Password --}}
                <div class="field-group">
                    <label class="field-label" for="password">Password</label>
                    <div class="field-wrap">
                        <input id="password"
                            type="password"
                            name="password"
                            class="field-input @error('password') is-invalid @enderror"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password" />
                        <button type="button"
                                class="toggle-password"
                                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--gray-400); padding: 0; display: flex; align-items: center; justify-content: center;"
                                aria-label="Show password"
                                onclick="togglePassword(this)">
                            <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div style="display: flex; align-items: center; gap: 0.5rem; margin: 0.75rem 0;">
                    <input type="checkbox" name="remember" id="remember" style="width: 16px; height: 16px;">
                    <label for="remember" style="font-size: 0.8rem; color: var(--gray-600);">Remember me</label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary" id="loginBtn">
                    Sign In
                </button>

                {{-- Links --}}
                <div class="links-row">
                    @unless($adminMode)
                        <span class="link-text">
                            Don't have an account?&nbsp;
                            <a href="{{ route('register') }}">Register here</a>
                        </span>
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    @endunless
                </div>
            </form>

            <footer class="auth-footer">
                &copy; {{ date('Y') }} University of Caloocan City. All rights reserved.
            </footer>
        </div>
    </main>
</div>

@endsection

@push('scripts')
<script>
    function togglePassword(btn) {
        const input = btn.closest('.field-wrap').querySelector('.field-input');
        const icon = btn.querySelector('.eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }

    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.textContent = 'Signing in...';
        btn.disabled = true;
    });
</script>
@endpush