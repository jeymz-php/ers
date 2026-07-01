<!-- Mobile Menu Toggle Button -->
<button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>

<script>
    function toggleMobileMenu() {
        const sidebar = document.querySelector('.user-sidebar');
        sidebar.classList.toggle('open');
    }
    
    // Close menu when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const sidebar = document.querySelector('.user-sidebar');
        const toggle = document.querySelector('.mobile-menu-toggle');
        
        if (window.innerWidth <= 768) {
            if (sidebar && toggle) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('open');
                }
            }
        }
    });
    
    // Close menu on window resize if screen becomes larger
    window.addEventListener('resize', function() {
        const sidebar = document.querySelector('.user-sidebar');
        if (window.innerWidth > 768 && sidebar) {
            sidebar.classList.remove('open');
        }
    });
</script>

<!-- User Sidebar Partial -->
<style>
    .user-sidebar {
        width: 280px;
        background: linear-gradient(180deg, #0a3d1f 0%, #1a7a3e 100%);
        color: white;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
        transition: all 0.3s ease;
        z-index: 100;
    }

    .sidebar-header {
        padding: 30px 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 20px;
    }

    /* Fixed Logo Styling - Centered and Proper Size */
    .sidebar-logo-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 15px;
    }

    .sidebar-logo {
        width: 85px;
        height: 85px;
        border-radius: 50%;
        background: white;
        padding: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: transform 0.3s ease;
        object-fit: contain;
    }

    .sidebar-logo:hover {
        transform: scale(1.05);
    }

    .sidebar-title {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 1px;
        margin-top: 5px;
    }

    .sidebar-subtitle {
        font-size: 11px;
        opacity: 0.8;
        margin-top: 5px;
    }

    .nav-menu {
        padding: 0 15px;
    }

    .nav-item {
        margin-bottom: 5px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: rgba(255,255,255,0.85);
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s;
        gap: 12px;
    }

    .nav-link:hover, .nav-link.active {
        background: rgba(255,255,255,0.15);
        color: white;
    }

    .nav-icon {
        width: 24px;
        font-size: 18px;
    }

    .nav-text {
        font-size: 14px;
        font-weight: 500;
    }

    .nav-divider {
        height: 1px;
        background: rgba(255,255,255,0.1);
        margin: 15px 0;
    }

    .sidebar-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
        font-size: 11px;
        text-align: center;
        opacity: 0.7;
    }

    @media (max-width: 768px) {
        .user-sidebar {
            transform: translateX(-100%);
        }
        .user-sidebar.active {
            transform: translateX(0);
        }
        .sidebar-logo {
            width: 70px;
            height: 70px;
            padding: 10px;
        }
    }

    /* Fix for mobile sidebar */
    @media (max-width: 768px) {
        .mobile-menu-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: #1a7a3e;
            border: none;
            color: white;
            font-size: 20px;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
        }
        
        /* Add padding to topbar to prevent overlap */
        .user-topbar {
            padding-left: 60px !important;
        }
        
        .page-title {
            font-size: 16px;
        }
    }
</style>

<aside class="user-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo-container">
            <img src="{{ asset('images/UCC_Logo.png') }}" class="sidebar-logo" alt="UCC Logo">
        </div>
        <div class="sidebar-title">UCC-ERS</div>
        <div class="sidebar-subtitle">Event Reservation System</div>
    </div>

    <nav class="nav-menu">
        <div class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                <span class="nav-text">Availability</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('user.summary') }}" class="nav-link {{ request()->routeIs('user.summary') ? 'active' : '' }}">
                <span class="nav-icon">📋</span>
                <span class="nav-text">Summary</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('user.reservations') }}" class="nav-link {{ request()->routeIs('user.reservations') ? 'active' : '' }}">
                <span class="nav-icon">📍</span>
                <span class="nav-text">Reservations</span>
            </a>
        </div>
        
        <div class="nav-divider"></div>

        <div class="nav-item">
            <a href="{{ route('user.chat') }}" class="nav-link {{ request()->routeIs('user.chat') ? 'active' : '' }}">
                <span class="nav-icon">💬</span>
                <span class="nav-text">Messages</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a href="{{ route('user.settings') }}" class="nav-link {{ request()->routeIs('user.settings') ? 'active' : '' }}">
                <span class="nav-icon">⚙️</span>
                <span class="nav-text">Settings</span>
            </a>
        </div>
        
        <div class="nav-item">
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="nav-link" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer;">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-text">Logout</span>
                </button>
            </form>
        </div>
        
        <div class="nav-divider"></div>
        
        <div class="nav-item">
            <a href="{{ route('user.guide') }}" class="{{ request()->routeIs('user.guide') ? 'active' : '' }}">
                <i class="..."></i> 📙 User Guide
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        &copy; {{ date('Y') }} UCC-ERS<br>
        Version 2.0 beta
    </div>
</aside>