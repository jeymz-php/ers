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

    .landing-card img {
        width: 120px;
        margin-bottom: 24px;
        cursor: pointer;
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
    }

    .btn-primary {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        color: #fff;
    }

    .btn-secondary {
        background: #f3f7f2;
        color: #1a7a3e;
    }

    .secret-hint,
    .click-count {
        font-size: 0.9rem;
        color: #6e7f72;
        margin-top: 4px;
    }
</style>

<div class="system-landing">
    <div class="landing-card">
        <img id="secretLogo" src="{{ asset('images/UCC_Logo.png') }}" alt="University of Caloocan City Logo" />
        <h1>University of Caloocan City Event Reservation</h1>
        <p>Reserve venues, check availability, and manage campus events in one secure system.</p>

        <div class="action-buttons">
            <a href="{{ route('login') }}" class="btn-primary">User Login</a>
            <a href="{{ route('public.availability.index') }}" class="btn-secondary">View Availability</a>
        </div>

        <p class="secret-hint">Authorized admins can access the hidden login by clicking the logo 5 times.</p>
        <p class="click-count" id="clickCountText"></p>
    </div>
</div>

@push('scripts')
<script>
    let secretClickCount = 0;
    const clickText = document.getElementById('clickCountText');
    const secretLogo = document.getElementById('secretLogo');

    secretLogo.addEventListener('click', function () {
        secretClickCount += 1;
        if (secretClickCount >= 5) {
            window.location.href = '{{ route('admin.login') }}';
            return;
        }
        clickText.textContent = `Clicked ${secretClickCount} / 5 times. Keep clicking to open admin access.`;
    });
</script>
@endpush
@endsection
