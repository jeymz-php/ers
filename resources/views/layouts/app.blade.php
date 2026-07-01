<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="UCC-ERS — University of Caloocan City Event Reservation System" />

    <title>UCC-ERS | @yield('title', 'Welcome')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/UCC_Logo.png') }}" />

    <style>
        /* ============================================================
           UCC-ERS  —  Global / Base Styles
           Theme   :  Green & White  |  Clean Professional Design
           Font    :  Arial System Font Stack
        ============================================================ */

        /* ── CSS Variables ───────────────────────────────────────── */
        :root {
            --green-900  : #0a3d1f;
            --green-800  : #145a2e;
            --green-700  : #1a7a3e;
            --green-600  : #22913f;
            --green-500  : #2db84f;
            --green-400  : #4cca68;
            --green-300  : #80dea0;
            --green-200  : #b3edca;
            --green-100  : #d4f5df;
            --green-50   : #f0faf3;

            --white      : #ffffff;
            --off-white  : #f7faf8;
            --gray-100   : #e8eee9;
            --gray-200   : #d4dbd6;
            --gray-300   : #b0bdb3;
            --gray-500   : #6e7f72;
            --gray-700   : #3c4a3f;
            --gray-900   : #1a241d;

            --shadow-sm  : 0 2px 8px rgba(22, 90, 46, 0.08);
            --shadow-md  : 0 8px 32px rgba(22, 90, 46, 0.12);
            --shadow-lg  : 0 20px 60px rgba(10, 61, 31, 0.15);

            --radius-xs  : 6px;
            --radius-sm  : 10px;
            --radius-md  : 16px;
            --radius-lg  : 26px;

            --font-family: 'Arial', 'Helvetica Neue', Helvetica, sans-serif;

            --transition : all 0.25s ease;
        }

        /* ── Reset ───────────────────────────────────────────────── */
        *, *::before, *::after {
            box-sizing : border-box;
            margin     : 0;
            padding    : 0;
        }

        html, body {
            height                 : 100%;
            font-family            : var(--font-family);
            background             : var(--off-white);
            color                  : var(--gray-700);
            -webkit-font-smoothing : antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        a {
            color          : var(--green-600);
            text-decoration: none;
            transition     : color 0.2s;
        }
        a:hover { color: var(--green-800); text-decoration: underline; }

        img { max-width: 100%; display: block; }

        /* ── Utility: Alert boxes ────────────────────────────────── */
        .alert {
            border-radius: var(--radius-xs);
            padding      : 0.75rem 1rem;
            font-size    : 0.85rem;
            margin-bottom: 1.25rem;
            display      : flex;
            gap          : 0.75rem;
            align-items  : flex-start;
            line-height  : 1.5;
        }
        .alert svg { flex-shrink: 0; margin-top: 2px; }
        .alert-error {
            background: #fef2f2;
            border    : 1px solid #fecaca;
            color     : #991b1b;
        }
        .alert-success {
            background: var(--green-50);
            border    : 1px solid var(--green-200);
            color     : var(--green-800);
        }
        .alert-info {
            background: #eff6ff;
            border    : 1px solid #bfdbfe;
            color     : #1e40af;
        }

        /* ============================================================
           AUTH LAYOUT  (login / register / password pages)
        ============================================================ */

        /* Page wrapper — flex row */
        .auth-page {
            display   : flex;
            min-height: 100vh;
            overflow  : hidden;
        }

        /* ── LEFT HERO PANEL  60% ──────────────────────────────── */
        .hero-panel {
            position  : relative;
            flex      : 0 0 60%;
            overflow  : hidden;
        }

        .hero-bg {
            position           : absolute;
            inset              : 0;
            background-size    : cover;
            background-position: center;
            transition         : transform 8s ease-out;
            will-change        : transform;
        }
        .hero-panel:hover .hero-bg { transform: scale(1.03); }

        /* Green gradient overlay */
        .hero-overlay {
            position  : absolute;
            inset     : 0;
            background: linear-gradient(
                135deg,
                rgba(10, 61, 31, 0.85) 0%,
                rgba(26, 122, 62, 0.70) 50%,
                rgba(34, 145, 63, 0.55) 100%
            );
            z-index: 1;
        }

        /* Diagonal white edge blending into right panel */
        .hero-edge {
            position  : absolute;
            top       : 0;
            right     : -1px;
            bottom    : 0;
            width     : 80px;
            background: var(--white);
            clip-path : polygon(40% 0, 100% 0, 100% 100%, 0% 100%);
            z-index   : 3;
        }

        /* Content */
        .hero-content {
            position       : relative;
            z-index        : 2;
            display        : flex;
            flex-direction : column;
            align-items    : center;
            justify-content: center;
            height         : 100%;
            padding        : 3rem 4rem;
            text-align     : center;
        }

        /* Logo circle */
        .hero-logo-wrap { margin-bottom: 2rem; animation: fadeUp 0.7s ease both; }

        .hero-logo {
            width         : 120px;
            height        : 120px;
            border-radius : 50%;
            object-fit    : contain;
            background    : rgba(255,255,255,0.10);
            backdrop-filter: blur(8px);
            border        : 3px solid rgba(255,255,255,0.40);
            padding       : 12px;
            box-shadow    : 0 10px 40px rgba(0,0,0,0.25);
            transition    : var(--transition);
        }
        .hero-logo:hover {
            transform : scale(1.05);
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
        }

        .hero-tag {
            display        : inline-block;
            background     : rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            color          : var(--green-200);
            font-size      : 0.7rem;
            font-weight    : 600;
            letter-spacing : 0.15em;
            text-transform : uppercase;
            padding        : 0.4rem 1.2rem;
            border-radius  : 100px;
            border         : 1px solid rgba(255,255,255,0.2);
            margin-bottom  : 1rem;
            animation      : fadeUp 0.7s 0.1s ease both;
        }

        .hero-title {
            font-family   : var(--font-family);
            font-size     : clamp(2rem, 3vw, 3.2rem);
            font-weight   : 700;
            color         : var(--white);
            line-height   : 1.2;
            margin-bottom : 1rem;
            animation     : fadeUp 0.7s 0.2s ease both;
        }
        .hero-title span { color: var(--green-300); }

        .hero-subtitle {
            font-size    : 0.9rem;
            color        : rgba(255,255,255,0.8);
            line-height  : 1.6;
            max-width    : 420px;
            animation    : fadeUp 0.7s 0.3s ease both;
        }

        /* Bottom dots */
        .hero-dots {
            position : absolute;
            bottom   : 2rem;
            left     : 0;
            right    : 0;
            display  : flex;
            justify-content: center;
            gap      : 0.5rem;
            z-index  : 2;
            animation: fadeUp 0.7s 0.5s ease both;
        }
        .hero-dot {
            width        : 8px;
            height       : 8px;
            border-radius: 50%;
            background   : rgba(255,255,255,0.3);
            transition   : var(--transition);
        }
        .hero-dot.active {
            background   : var(--green-400);
            width        : 24px;
            border-radius: 4px;
        }

        /* ── RIGHT AUTH PANEL  40% ─────────────────────────────── */
        .auth-panel {
            flex           : 0 0 40%;
            display        : flex;
            flex-direction : column;
            justify-content: center;
            background     : var(--white);
            padding        : 2.5rem 2.8rem;
            overflow-y     : auto;
            position       : relative;
            z-index        : 4;
            box-shadow     : -8px 0 40px rgba(0, 0, 0, 0.05);
            animation      : slideInRight 0.5s ease both;
        }

        /* Top green accent bar */
        .auth-panel::before {
            content   : '';
            position  : absolute;
            top       : 0;
            left      : 0;
            right     : 0;
            height    : 4px;
            background: linear-gradient(90deg, var(--green-700), var(--green-500), var(--green-700));
        }

        .auth-inner {
            width    : 100%;
            max-width: 380px;
            margin   : 0 auto;
        }

        /* Brand mark */
        .brand-mark {
            display       : flex;
            align-items   : center;
            gap           : 0.75rem;
            margin-bottom : 2rem;
            padding-bottom: 1rem;
            border-bottom : 2px solid var(--gray-100);
        }
        .brand-icon {
            width         : 42px;
            height        : 42px;
            border-radius : var(--radius-xs);
            background    : linear-gradient(135deg, var(--green-700), var(--green-500));
            display       : flex;
            align-items   : center;
            justify-content: center;
            box-shadow    : var(--shadow-sm);
            flex-shrink   : 0;
        }
        .brand-icon img {
            width     : 26px;
            height    : 26px;
            object-fit: contain;
            filter    : brightness(0) invert(1);
        }
        .brand-text { display: flex; flex-direction: column; }
        .brand-name {
            font-size     : 0.85rem;
            font-weight   : 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color         : var(--green-700);
        }
        .brand-sub {
            font-size     : 0.7rem;
            color         : var(--gray-500);
        }

        /* Auth headings */
        .auth-heading {
            font-family   : var(--font-family);
            font-size     : 1.75rem;
            font-weight   : 700;
            color         : var(--gray-900);
            line-height   : 1.2;
            margin-bottom : 0.5rem;
        }
        .auth-subheading {
            font-size    : 0.85rem;
            color        : var(--gray-500);
            line-height  : 1.5;
            margin-bottom: 1.75rem;
        }

        /* Form fields */
        .field-group {
            display       : flex;
            flex-direction: column;
            gap           : 0.4rem;
            margin-bottom : 1rem;
        }
        .field-label {
            font-size     : 0.75rem;
            font-weight   : 600;
            color         : var(--gray-700);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .field-wrap { position: relative; }

        .field-input, .field-select {
            width        : 100%;
            padding      : 0.75rem 2.4rem 0.75rem 0.9rem;
            border       : 1.5px solid var(--gray-200);
            border-radius: var(--radius-xs);
            font-family  : var(--font-family);
            font-size    : 0.9rem;
            color        : var(--gray-700);
            background   : var(--white);
            outline      : none;
            transition   : var(--transition);
        }
        .field-select {
            padding-right: 0.9rem;
            cursor      : pointer;
            appearance  : none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236e7f72' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
        }
        .field-input::placeholder { color: var(--gray-300); }
        .field-input:focus, .field-select:focus {
            border-color: var(--green-500);
            box-shadow  : 0 0 0 3px rgba(34,145,63,0.1);
        }
        .field-input.is-invalid, .field-select.is-invalid {
            border-color: #dc2626;
        }

        .field-icon {
            position      : absolute;
            right         : 0.75rem;
            top           : 50%;
            transform     : translateY(-50%);
            color         : var(--gray-400);
            pointer-events: none;
        }

        /* Primary button */
        .btn-primary {
            width        : 100%;
            padding      : 0.85rem 1rem;
            background   : linear-gradient(135deg, var(--green-700) 0%, var(--green-500) 100%);
            color        : var(--white);
            border       : none;
            border-radius: var(--radius-xs);
            font-family  : var(--font-family);
            font-size    : 0.85rem;
            font-weight  : 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor       : pointer;
            transition   : var(--transition);
            margin-top   : 0.5rem;
            box-shadow   : 0 2px 8px rgba(34,145,63,0.3);
        }
        .btn-primary:hover {
            transform : translateY(-1px);
            box-shadow: 0 4px 16px rgba(34,145,63,0.4);
        }
        .btn-primary:active {
            transform : translateY(0);
        }
        .btn-primary:disabled {
            opacity: 0.7;
            cursor : not-allowed;
            transform: none;
        }

        /* Secondary button */
        .btn-secondary {
            width        : 100%;
            padding      : 0.75rem 1rem;
            background   : transparent;
            color        : var(--green-600);
            border       : 1.5px solid var(--gray-200);
            border-radius: var(--radius-xs);
            font-family  : var(--font-family);
            font-size    : 0.85rem;
            font-weight  : 500;
            cursor       : pointer;
            transition   : var(--transition);
            text-align   : center;
            display      : inline-block;
        }
        .btn-secondary:hover {
            border-color: var(--green-500);
            background  : var(--green-50);
        }

        /* Links row */
        .links-row {
            display        : flex;
            justify-content: space-between;
            align-items    : center;
            flex-wrap      : wrap;
            gap            : 0.5rem;
            margin-top     : 1rem;
        }
        .link-text { font-size: 0.8rem; color: var(--gray-500); }

        /* Divider */
        .divider {
            display    : flex;
            align-items: center;
            gap        : 0.8rem;
            margin     : 1.5rem 0;
        }
        .divider-line { flex: 1; height: 1px; background: var(--gray-200); }
        .divider-text {
            font-size     : 0.7rem;
            color         : var(--gray-400);
            text-transform: uppercase;
        }

        /* Info box */
        .info-box {
            background   : var(--green-50);
            border       : 1px solid var(--green-200);
            border-radius: var(--radius-xs);
            padding      : 0.75rem;
            margin-bottom: 1.25rem;
            font-size    : 0.75rem;
            color        : var(--green-800);
            line-height  : 1.5;
        }
        .info-box svg {
            display      : inline-block;
            vertical-align: middle;
            margin-right : 0.5rem;
        }

        /* Footer */
        .auth-footer {
            margin-top : 2rem;
            padding-top: 1.5rem;
            text-align : center;
            font-size  : 0.7rem;
            color      : var(--gray-400);
            border-top : 1px solid var(--gray-100);
        }

        /* ── Keyframes ─────────────────────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0);    }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to   { opacity: 1; transform: translateX(0);    }
        }

        /* ============================================================
        MOBILE RESPONSIVE STYLES
        ============================================================ */

        /* Mobile Menu Toggle Button */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: #1a7a3e;
            border: none;
            color: white;
            font-size: 24px;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        /* Tablet Styles (768px - 1024px) */
        @media (max-width: 1024px) {
            .content-area {
                padding: 20px;
            }
            
            .dashboard-layout,
            .availability-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .calendar-day {
                min-height: 90px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Mobile Styles (480px - 768px) */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
            
            .user-sidebar,
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                z-index: 1000;
                transition: transform 0.3s ease;
            }
            
            .user-sidebar.open,
            .sidebar.open {
                transform: translateX(0);
            }
            
            .user-main,
            .main-content {
                margin-left: 0 !important;
                width: 100%;
            }
            
            .content-area {
                padding: 15px;
            }
            
            .welcome-banner {
                flex-direction: column;
                text-align: center;
                padding: 15px;
            }
            
            .dashboard-layout,
            .availability-container {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .calendar-wrapper {
                padding: 15px;
            }
            
            .calendar-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .calendar-nav {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .calendar-weekdays .weekday {
                font-size: 10px;
                padding: 6px;
            }
            
            .calendar-day {
                min-height: 70px;
                padding: 5px;
            }
            
            .day-number {
                font-size: 10px;
            }
            
            .event-badge {
                font-size: 8px;
                padding: 2px 4px;
            }
            
            .events-panel {
                gap: 15px;
            }
            
            .scheduled-events,
            .upcoming-events {
                padding: 15px;
            }
            
            .event-item {
                flex-direction: column;
                gap: 8px;
            }
            
            .event-time {
                min-width: auto;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .stat-card {
                padding: 15px;
            }
            
            .stat-value {
                font-size: 28px;
            }
            
            /* Tables */
            .table-wrapper {
                overflow-x: auto;
            }
            
            .data-table {
                min-width: 600px;
            }
            
            .data-table th,
            .data-table td {
                padding: 8px;
                font-size: 11px;
            }
            
            /* Filters */
            .filter-bar {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }
            
            /* Modal */
            .modal-content {
                width: 95%;
                margin: 20px auto;
            }
            
            .modal-body {
                grid-template-columns: 1fr;
                padding: 15px;
            }
            
            .calendar-section {
                position: relative;
                top: auto;
            }
            
            /* Establishments Grid */
            .establishments-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            /* Topbar */
            .topbar,
            .user-topbar {
                padding: 12px 15px;
                flex-wrap: wrap;
            }
            
            .page-title {
                font-size: 16px;
            }
            
            .system-clock {
                padding: 5px 10px;
            }
            
            .clock-time {
                font-size: 14px;
            }
            
            .user-info {
                display: none;
            }
            
            /* Pagination */
            .pagination {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
        }

        /* Small Mobile (under 480px) */
        @media (max-width: 480px) {
            .content-area {
                padding: 10px;
            }
            
            .calendar-day {
                min-height: 60px;
            }
            
            .day-number {
                font-size: 9px;
            }
            
            .event-badge {
                font-size: 7px;
                padding: 1px 3px;
            }
            
            .calendar-nav button {
                padding: 5px 10px;
                font-size: 11px;
            }
            
            .month-title {
                font-size: 16px;
            }
            
            .section-title {
                font-size: 14px;
            }
            
            .btn-primary,
            .btn-submit,
            .btn-view {
                padding: 8px 15px;
                font-size: 12px;
            }
            
            .modal-header h3 {
                font-size: 16px;
            }
            
            .close-modal {
                font-size: 24px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    @yield('content')

    <!-- Chatbot Widget -->
    @auth
        @if(!auth()->user()->isAdmin() && !request()->routeIs('user.chat') && !request()->routeIs('chatbot.index') && !request()->routeIs('user.guide') && !request()->routeIs('user.pending'))
            @include('partials.chatbot-widget')
        @endif
    @endauth

    @stack('scripts')
</body>
</html>