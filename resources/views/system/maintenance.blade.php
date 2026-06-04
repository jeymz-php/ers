@extends('layouts.app')

@section('title', 'System Maintenance')

@section('content')
<style>
    .maintenance-page {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        background: linear-gradient(180deg, #f7faf8 0%, #ffffff 100%);
    }

    .maintenance-card {
        max-width: 680px;
        width: 100%;
        background: white;
        border-radius: 28px;
        padding: 40px;
        box-shadow: 0 24px 80px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .maintenance-card-top {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        margin-bottom: 24px;
    }

    .maintenance-icon {
        width: 72px;
        height: 72px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f1fbf4;
        color: #1a7a3e;
        box-shadow: 0 12px 24px rgba(26, 122, 62, 0.12);
    }

    .maintenance-card img {
        width: 120px;
        max-width: 100%;
        margin: 0 auto;
    }

    .chatbot-widget,
    .chatbot-window,
    .chatbot-toggle {
        display: none !important;
    }

    .maintenance-title {
        font-size: 2rem;
        margin-bottom: 16px;
        color: #1a7a3e;
    }

    .maintenance-text {
        color: #4f5f53;
        line-height: 1.75;
        margin-bottom: 28px;
    }

    .maintenance-note {
        color: #6e7f72;
        margin-top: 16px;
        font-size: 0.95rem;
    }
</style>

<div class="maintenance-page">
    <div class="maintenance-card">
        <div class="maintenance-card-top">
            <div class="maintenance-icon" aria-hidden="true">
                <img src="{{ asset('images/tool.png') }}" alt="Maintenance Tool Icon" style="width: 40px; height: 40px; object-fit: contain;" />
            </div>
            <img id="secretLogo" src="{{ asset('images/UCC_Logo.png') }}" alt="University of Caloocan City Logo" />
        </div>
        <h1 class="maintenance-title">{{ $maintenanceTitle }}</h1>
        <p class="maintenance-text">{{ $maintenanceMessage }}</p>
        <p class="maintenance-note">UCC-Event Reservation System are down at the moment, please try again later.</p>
        <p class="maintenance-note" id="clickCountText"></p>
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
