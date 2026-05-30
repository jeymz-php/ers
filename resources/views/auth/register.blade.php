@extends('layouts.app')

@section('title', 'Register')

@section('content')

<style>
    /* Mobile Responsive Styles for Register */
    @media (max-width: 768px) {
        .auth-page {
            flex-direction: column;
        }
        
        .hero-panel {
            height: 30vh;
            min-height: 250px;
        }
        
        .hero-content {
            padding: 20px;
        }
        
        .hero-logo {
            width: 60px;
            height: 60px;
            padding: 8px;
        }
        
        .hero-tag {
            font-size: 0.55rem;
            padding: 0.25rem 0.8rem;
        }
        
        .hero-title {
            font-size: 1.3rem;
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
            padding: 20px;
        }
        
        .auth-inner {
            max-width: 100%;
        }
        
        .brand-mark {
            margin-bottom: 1.25rem;
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
            font-size: 1.4rem;
        }
        
        .auth-subheading {
            font-size: 0.7rem;
            margin-bottom: 1rem;
        }
        
        .info-box {
            padding: 0.6rem;
            margin-bottom: 1rem;
            font-size: 0.7rem;
        }
        
        .field-label {
            font-size: 0.65rem;
        }
        
        .field-input, .field-select {
            padding: 0.55rem 2rem 0.55rem 0.75rem;
            font-size: 0.8rem;
        }
        
        .btn-primary {
            padding: 0.65rem 1rem;
            font-size: 0.8rem;
        }
        
        .links-row {
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        
        .link-text {
            font-size: 0.7rem;
        }
        
        .auth-footer {
            font-size: 0.6rem;
            margin-top: 1.25rem;
        }
    }
    
    @media (max-width: 480px) {
        .hero-panel {
            height: 25vh;
            min-height: 220px;
        }
        
        .hero-logo {
            width: 50px;
            height: 50px;
            padding: 6px;
        }
        
        .hero-title {
            font-size: 1.1rem;
        }
        
        .hero-tag {
            font-size: 0.5rem;
        }
        
        .auth-panel {
            padding: 15px;
        }
        
        .auth-heading {
            font-size: 1.2rem;
        }
        
        .field-input, .field-select {
            padding: 0.5rem 1.8rem 0.5rem 0.7rem;
            font-size: 0.75rem;
        }
        
        .btn-primary {
            padding: 0.55rem 1rem;
            font-size: 0.75rem;
        }
        
        .info-box {
            font-size: 0.65rem;
            padding: 0.5rem;
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
            <span class="hero-tag">Join the UCC Community</span>
            <h1 class="hero-title">
                Create Your<br />
                <span>Account</span>
            </h1>
            <p class="hero-subtitle">
                Register to start reserving event venues and managing your campus activities.
            </p>
        </div>

        <div class="hero-dots" aria-hidden="true">
            <div class="hero-dot"></div>
            <div class="hero-dot active"></div>
            <div class="hero-dot"></div>
        </div>
    </section>

    {{-- RIGHT REGISTER PANEL --}}
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

            {{-- Heading --}}
            <h2 class="auth-heading">Create Account</h2>
            <p class="auth-subheading">
                Fill in your details below to get started.
            </p>

            {{-- Info Box --}}
            <div class="info-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="12" x2="12" y2="16"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <strong>System Generated Password</strong><br>
                A secure password will be automatically generated and sent to your email address.
                You can change it after your first login.
            </div>

            {{-- Alerts --}}
            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin-top: 0.25rem; margin-left: 1rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Registration Form --}}
            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                {{-- Full Name --}}
                <div class="field-group">
                    <label class="field-label" for="name">Full Name</label>
                    <div class="field-wrap">
                        <input id="name"
                               type="text"
                               name="name"
                               class="field-input @error('name') is-invalid @enderror"
                               placeholder="e.g., Juan Dela Cruz"
                               value="{{ old('name') }}"
                               required
                               autocomplete="name" />
                    </div>
                </div>

                {{-- Email Address --}}
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
                               autocomplete="email" />
                    </div>
                </div>

                {{-- Confirm Email --}}
                <div class="field-group">
                    <label class="field-label" for="email_confirmation">Confirm Email Address</label>
                    <div class="field-wrap">
                        <input id="email_confirmation"
                               type="email"
                               name="email_confirmation"
                               class="field-input @error('email') is-invalid @enderror"
                               placeholder="Confirm your email"
                               value="{{ old('email_confirmation') }}"
                               required />
                    </div>
                </div>

                {{-- Phone Number --}}
                <div class="field-group">
                    <label class="field-label" for="phone_number">Phone Number</label>
                    <div class="field-wrap">
                        <input id="phone_number"
                               type="tel"
                               name="phone_number"
                               class="field-input @error('phone_number') is-invalid @enderror"
                               placeholder="e.g., 09123456789"
                               value="{{ old('phone_number') }}"
                               required />
                    </div>
                </div>

                {{-- Campus Dropdown --}}
                <div class="field-group">
                    <label class="field-label" for="campus_id">Campus</label>
                    <div class="field-wrap">
                        <select id="campus_id"
                                name="campus_id"
                                class="field-select @error('campus_id') is-invalid @enderror"
                                required>
                            <option value="">Select your campus</option>
                            @foreach($campuses as $campus)
                                <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                                    {{ $campus->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary" id="registerBtn">
                    Register Account
                </button>

                {{-- Login Link --}}
                <div class="links-row">
                    <span class="link-text">
                        Already have an account?&nbsp;
                        <a href="{{ route('login') }}">Sign in here</a>
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
    document.getElementById('registerForm').addEventListener('submit', function() {
        const btn = document.getElementById('registerBtn');
        btn.textContent = 'Creating Account...';
        btn.disabled = true;
    });
</script>
@endpush