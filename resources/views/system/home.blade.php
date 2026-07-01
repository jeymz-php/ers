@extends('layouts.app')

@section('title', 'UCC Event Reservation')

@section('content')
<style>
    .system-landing {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        background: linear-gradient(180deg, #f4f9f3 0%, #ffffff 100%);
    }

    .landing-card {
        max-width: 700px;
        width: 100%;
        background: white;
        border-radius: 28px;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.08);
        padding: 40px;
        text-align: center;
    }

    /* Logo Container - Perfect Centering */
    .logo-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 24px;
    }

    .landing-card img {
        height: 180px;
        background: white;
        padding: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .landing-card h1 {
        font-size: 2.4rem;
        margin: 0 0 16px;
        color: #1a7a3e;
    }

    .landing-card p {
        margin: 0 0 24px;
        color: #4f5f53;
        font-size: 1rem;
        line-height: 1.75;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 18px;
    }

    .btn-primary,
    .btn-secondary {
        border: none;
        border-radius: 999px;
        padding: 14px 28px;
        font-size: 0.95rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: #fff;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(34, 145, 63, 0.3);
    }

    .btn-secondary {
        background: #f3f7f2;
        color: #1a7a3e;
    }

    .btn-secondary:hover {
        background: #e8f0e6;
        transform: translateY(-2px);
    }

    .secret-hint {
        font-size: 0.85rem;
        color: #b0bdb3;
        margin-top: 12px;
        cursor: default;
    }

    .secret-hint span {
        color: #2db84f;
        font-weight: 600;
    }
</style>

<div class="system-landing">
    <div class="landing-card">
        <div class="logo-container">
            <img id="secretLogo" src="{{ asset('images/UCC_Logo.png') }}" alt="University of Caloocan City Logo" />
        </div>
        <h1>University of Caloocan City</h1>
        <h1 style="font-size: 1.5rem; margin-top: -10px; margin-bottom: 20px; color: #2db84f;">Event Reservation System</h1>
        <p>Reserve venues, check availability, and manage campus events in one secure system.</p>

        <div class="action-buttons">
            <a href="{{ route('login') }}" class="btn-primary">User Login</a>
            <a href="{{ route('public.availability.index') }}" class="btn-secondary">View Availability</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let secretClickCount = 0;
    const secretLogo = document.getElementById('secretLogo');
    const clickText = document.getElementById('clickCountText');

    secretLogo.addEventListener('click', function (e) {
        e.stopPropagation();
        secretClickCount += 1;
        
        if (secretClickCount >= 5) {
            window.location.href = '{{ route('admin.login') }}';
            return;
        }
        
        // Visual feedback on click
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            secretLogo.style.transform = 'scale(1)';
        }, 150);
        
        clickText.innerHTML = `🔒 Clicked ${secretClickCount}/5 times. <span>Keep clicking to access admin portal.</span>`;
        
        // Add progress bar effect
        const progressWidth = (secretClickCount / 5) * 100;
        clickText.style.background = `linear-gradient(90deg, #d4f5df ${progressWidth}%, transparent ${progressWidth}%)`;
        clickText.style.padding = '6px 12px';
        clickText.style.borderRadius = '50px';
    });
</script>
@endpush
@endsection