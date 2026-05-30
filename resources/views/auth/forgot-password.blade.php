@extends('layouts.app')

@section('title', 'Forgot Password')

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
            padding: 8px;
        }
        .hero-tag {
            font-size: 0.6rem;
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
</style>

<div class="auth-page">
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
                Reset Your<br />
                <span>Password</span>
            </h1>
            <p class="hero-subtitle">
                Enter your email address and we'll send you a temporary password.
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
                    <img src="{{ asset('images/UCC_Logo.png') }}" alt="" aria-hidden="true" />
                </div>
                <div class="brand-text">
                    <span class="brand-name">UCC-ERS</span>
                    <span class="brand-sub">Event Reservation System</span>
                </div>
            </div>

            <h2 class="auth-heading">Forgot Password?</h2>
            <p class="auth-subheading">
                Enter your registered email address and we'll send you a temporary password to reset your account.
            </p>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
                @csrf

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
                               autofocus />
                        <span class="field-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="2" y="4" width="20" height="16" rx="3"/>
                                <path d="M22 7l-10 7L2 7"/>
                            </svg>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="sendBtn">
                    Send Temporary Password
                </button>

                <div class="links-row">
                    <span class="link-text">
                        Remember your password?&nbsp;
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

@endsection

@push('scripts')
<script>
    document.getElementById('forgotForm').addEventListener('submit', function() {
        const btn = document.getElementById('sendBtn');
        btn.textContent = 'Sending...';
        btn.disabled = true;
    });
</script>
@endpush