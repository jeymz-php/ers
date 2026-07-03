<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UCC-ERS Admin | @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/UCC_Logo.png') }}">
    <style>
        /* ========================================
           RESET & BASE STYLES
        ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica Neue', sans-serif;
            background: #f0faf3;
            overflow-x: hidden;
        }

        /* ========================================
           ADMIN CONTAINER
        ======================================== */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* ========================================
            PAGINATION STYLES
        ======================================== */
        .pagination-container {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e8eee9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .pagination-info {
            font-size: 13px;
            color: #6e7f72;
        }

        .pagination {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .pagination .page-link,
        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 6px 12px;
            border: 1px solid #e8eee9;
            border-radius: 8px;
            font-size: 13px;
            text-decoration: none;
            color: #1a7a3e;
            background: white;
            transition: all 0.3s;
            cursor: pointer;
        }

        .pagination .page-link:hover,
        .pagination a:hover {
            background: #1a7a3e;
            color: white;
            border-color: #1a7a3e;
        }

        .pagination .active span,
        .pagination span.active {
            background: #2db84f;
            color: white;
            border-color: #2db84f;
        }

        .pagination .disabled span,
        .pagination span.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Custom pagination wrapper for Laravel's paginator */
        .pagination nav {
            display: inline-block;
        }

        .pagination nav .pagination {
            margin: 0;
            gap: 5px;
        }

        /* Rows per page selector */
        .rows-per-page {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rows-per-page label {
            font-size: 13px;
            color: #6e7f72;
        }

        .rows-per-page select {
            padding: 6px 10px;
            border: 1px solid #e8eee9;
            border-radius: 8px;
            font-size: 13px;
            background: white;
            cursor: pointer;
        }

        /* ========================================
           SIDEBAR
        ======================================== */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0a3d1f 0%, #1a7a3e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 100;
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-logo {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            background: white;
            padding: 12px;
            object-fit: contain;
            transition: transform 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .sidebar-logo:hover {
            transform: scale(1.05);
        }

        .sidebar-title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .sidebar-subtitle {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 5px;
        }

        /* Navigation */
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

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
        }

        .nav-icon {
            width: 24px;
            text-align: center;
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

        /* ========================================
           MAIN CONTENT
        ======================================== */
        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }

        /* ========================================
           TOPBAR
        ======================================== */
        .topbar {
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a7a3e;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 700;
            color: #1a7a3e;
            font-size: 14px;
        }

        .user-role {
            font-size: 11px;
            color: #6e7f72;
            text-transform: capitalize;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #1a7a3e, #2db84f);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        /* ========================================
           CONTENT AREA
        ======================================== */
        .content-area {
            padding: 30px;
        }

        /* ========================================
           NOTIFICATION STYLES
        ======================================== */
        .notification-bell {
            position: relative;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .notification-bell:hover {
            transform: scale(1.1);
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc2626;
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 50%;
            min-width: 18px;
            text-align: center;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }

        .notification-dropdown {
            position: absolute;
            top: 60px;
            right: 20px;
            width: 380px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            z-index: 1000;
            display: none;
            overflow: hidden;
            animation: slideDown 0.3s ease;
        }

        .notification-dropdown.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification-header {
            padding: 15px 20px;
            background: linear-gradient(135deg, #1a7a3e, #2db84f);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h4 {
            margin: 0;
            font-size: 16px;
        }

        .mark-all-read {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 11px;
            transition: background 0.3s;
        }

        .mark-all-read:hover {
            background: rgba(255,255,255,0.3);
        }

        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #e8eee9;
            cursor: pointer;
            transition: background 0.3s;
            position: relative;
        }

        .notification-item:hover {
            background: #f7faf8;
        }

        .notification-item.unread {
            background: #f0faf3;
        }

        .notification-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #2db84f;
        }

        .notification-title {
            font-weight: 700;
            color: #1a7a3e;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .notification-message {
            color: #6e7f72;
            font-size: 12px;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .notification-time {
            color: #b0bdb3;
            font-size: 10px;
        }

        .empty-notifications {
            text-align: center;
            padding: 40px;
            color: #b0bdb3;
        }

        .notification-footer {
            padding: 10px 20px;
            border-top: 1px solid #e8eee9;
            text-align: center;
        }

        .notification-footer a {
            color: #2db84f;
            text-decoration: none;
            font-size: 12px;
        }

        /* ========================================
           TOAST NOTIFICATION
        ======================================== */
        .toast-notification {
            position: fixed;
            top: 80px;
            right: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 15px 20px;
            min-width: 300px;
            max-width: 400px;
            z-index: 1001;
            animation: slideInRight 0.3s ease;
            border-left: 4px solid #2db84f;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .toast-icon {
            font-size: 20px;
        }

        .toast-title {
            font-weight: 700;
            color: #1a7a3e;
            font-size: 14px;
        }

        .toast-message {
            color: #6e7f72;
            font-size: 12px;
            margin-left: 30px;
        }

        .toast-close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            color: #b0bdb3;
            font-size: 16px;
        }

        /* ========================================
           SCROLLBAR
        ======================================== */
        .sidebar::-webkit-scrollbar,
        .notification-list::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track,
        .notification-list::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb,
        .notification-list::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
        }

        /* ========================================
           RESPONSIVE
        ======================================== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .topbar {
                padding: 12px 20px;
            }
            .page-title {
                font-size: 16px;
            }
            .content-area {
                padding: 20px;
            }
            .notification-dropdown {
                width: calc(100% - 40px);
                right: 20px;
                left: 20px;
            }
            .toast-notification {
                left: 20px;
                right: 20px;
                min-width: auto;
            }
            .user-info {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .topbar {
                padding: 10px 15px;
            }
            .content-area {
                padding: 15px;
            }
            .notification-bell span {
                font-size: 18px;
            }
            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 15px;">
                    <img src="{{ asset('images/UCC_Logo.png') }}" class="sidebar-logo" alt="UCC Logo">
                </div>
                <div class="sidebar-title">UCC-ERS</div>
                <div class="sidebar-subtitle">Administrator Panel</div>
            </div>

            <nav class="nav-menu">
                <div class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.availability.index') }}" class="nav-link {{ request()->routeIs('admin.availability.*') ? 'active' : '' }}">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">Availability</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">Registered Users</span>
                    </a>
                </div>

                @if(Auth::user()->isSuperAdmin())
                <div class="nav-item">
                    <a href="{{ route('admin.admins.index') }}" class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                        <span class="nav-icon">👨‍💼</span>
                        <span class="nav-text">Administrators</span>
                    </a>
                </div>
                @endif

                <div class="nav-divider"></div>

                <div class="nav-item">
                    <a href="{{ route('admin.campuses.index') }}" class="nav-link {{ request()->routeIs('admin.campuses.*') ? 'active' : '' }}">
                        <span class="nav-icon">🏛️</span>
                        <span class="nav-text">Campuses</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.reservations.index') }}" class="nav-link {{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">View Reservation</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.vehicle-reservations.index') }}" class="nav-link {{ request()->routeIs('admin.vehicle-reservations.*') ? 'active' : '' }}">
                        <span class="nav-icon">🚐</span>
                        <span class="nav-text">Vehicle Reservations</span>
                    </a>
                </div>

                <div class="nav-divider"></div>

                <div class="nav-item">
                    <a href="{{ route('admin.chat.index') }}" class="nav-link {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
                        <span class="nav-icon">💬</span>
                        <span class="nav-text">Messages</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
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
            </nav>

            <div class="sidebar-footer">
                &copy; {{ date('Y') }} UCC-ERS<br>
                Version {{ \App\Models\SystemUpdate::currentVersion() }}
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="page-title">@yield('page-title', 'Dashboard')</div>
                <div class="user-menu">
                    <!-- Notification Bell -->
                    <div class="notification-bell" onclick="toggleNotificationDropdown()">
                        <span style="font-size: 22px;">🔔</span>
                        <span id="notificationBadge" class="notification-badge" style="display: none;">0</span>
                    </div>

                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</div>
                    </div>
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
            </div>

            <!-- Notification Dropdown -->
            <div id="notificationDropdown" class="notification-dropdown">
                <div class="notification-header">
                    <h4>🔔 Notifications</h4>
                    <button class="mark-all-read" onclick="markAllAsRead()">Mark all as read</button>
                </div>
                <div id="notificationList" class="notification-list">
                    <div class="empty-notifications">Loading...</div>
                </div>
                <div class="notification-footer">
                    <a href="{{ route('admin.reservations.index') }}">View all reservations</a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Notification Sound -->
    <audio id="notificationSound" preload="auto">
        <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
    </audio>

    <script>
        // ========================================
        // REAL-TIME NOTIFICATION SYSTEM
        // ========================================

        let notificationCheckInterval = null;
        let lastNotificationCount = 0;
        let lastCheckTime = new Date();

        // Play notification sound
        function playNotificationSound() {
            const audio = document.getElementById('notificationSound');
            if (audio) {
                audio.play().catch(function(e) {
                    console.log('Audio play failed:', e);
                });
            }
        }

        // Show toast notification
        function showToast(title, message) {
            const existingToast = document.querySelector('.toast-notification');
            if (existingToast) {
                existingToast.remove();
            }

            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = `
                <button class="toast-close" onclick="this.parentElement.remove()">×</button>
                <div class="toast-header">
                    <span class="toast-icon">🔔</span>
                    <span class="toast-title">${escapeHtml(title)}</span>
                </div>
                <div class="toast-message">${escapeHtml(message)}</div>
            `;
            document.body.appendChild(toast);

            setTimeout(function() {
                if (toast) toast.remove();
            }, 5000);
        }

        // Escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Get time ago
        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);

            if (seconds < 60) return 'just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ago';
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
            const days = Math.floor(hours / 24);
            if (days < 7) return days + ' day' + (days > 1 ? 's' : '') + ' ago';
            return date.toLocaleDateString();
        }

        // Check for new notifications (real-time)
        function checkNewNotifications() {
            fetch('/admin/notifications/unread-count')
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    const currentCount = data.count;
                    
                    if (currentCount > lastNotificationCount) {
                        // New notification arrived!
                        const newCount = currentCount - lastNotificationCount;
                        playNotificationSound();
                        
                        // Fetch the latest notification to show toast
                        fetch('/admin/notifications/latest')
                            .then(function(res) {
                                return res.json();
                            })
                            .then(function(latest) {
                                if (latest.notification) {
                                    showToast(latest.notification.title, latest.notification.message);
                                }
                            });
                    }
                    
                    lastNotificationCount = currentCount;
                    
                    // Update badge
                    const badge = document.getElementById('notificationBadge');
                    if (currentCount > 0) {
                        badge.style.display = 'block';
                        badge.textContent = currentCount > 99 ? '99+' : currentCount;
                    } else {
                        badge.style.display = 'none';
                    }
                });
        }

        // Load full notifications list
        function loadNotifications() {
            fetch('/admin/notifications')
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    const container = document.getElementById('notificationList');
                    if (!data.notifications.data || data.notifications.data.length === 0) {
                        container.innerHTML = '<div class="empty-notifications">No notifications yet</div>';
                        return;
                    }

                    let html = '';
                    data.notifications.data.forEach(function(notification) {
                        const timeAgo = getTimeAgo(notification.created_at);
                        const unreadClass = !notification.is_read ? 'unread' : '';
                        html += `
                            <div class="notification-item ${unreadClass}" onclick="markAsReadAndRedirect(${notification.id})">
                                <div class="notification-title">${escapeHtml(notification.title)}</div>
                                <div class="notification-message">${escapeHtml(notification.message)}</div>
                                <div class="notification-time">${timeAgo}</div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                });
        }

        // Toggle notification dropdown
        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');

            if (dropdown.classList.contains('show')) {
                loadNotifications();
            }
        }

        // Mark as read
        function markAsRead(id, title, message) {
            fetch('/admin/notifications/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(function() {
                loadNotifications();
                showToast(title, message);
                document.getElementById('notificationDropdown').classList.remove('show');
                
                // Update badge count
                if (lastNotificationCount > 0) {
                    lastNotificationCount--;
                    const badge = document.getElementById('notificationBadge');
                    if (lastNotificationCount > 0) {
                        badge.textContent = lastNotificationCount;
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
        }

        // Mark as read and redirect
        function markAsReadAndRedirect(id) {
            window.location.href = `/admin/notifications/${id}/redirect`;
        }

        // Mark all as read
        function markAllAsRead() {
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(function() {
                lastNotificationCount = 0;
                const badge = document.getElementById('notificationBadge');
                badge.style.display = 'none';
                loadNotifications();
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationDropdown');
            const bell = document.querySelector('.notification-bell');

            if (dropdown && bell && !dropdown.contains(event.target) && !bell.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Mobile sidebar toggle
        function toggleMobileSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('open');
        }

        // Add mobile menu button if on mobile
        if (window.innerWidth <= 768) {
            const topbar = document.querySelector('.topbar');
            const menuBtn = document.createElement('button');
            menuBtn.innerHTML = '☰';
            menuBtn.style.cssText = 'background: none; border: none; font-size: 24px; cursor: pointer; margin-right: 15px; color: #1a7a3e;';
            menuBtn.onclick = toggleMobileSidebar;
            topbar.insertBefore(menuBtn, topbar.firstChild);
        }

        // Initialize real-time notification checking (every 2 seconds for near real-time)
        function initRealTimeNotifications() {
            // Initial load
            fetch('/admin/notifications/unread-count')
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    lastNotificationCount = data.count;
                    const badge = document.getElementById('notificationBadge');
                    if (lastNotificationCount > 0) {
                        badge.style.display = 'block';
                        badge.textContent = lastNotificationCount > 99 ? '99+' : lastNotificationCount;
                    }
                });
            
            // Check every 2 seconds for new notifications (real-time feel)
            notificationCheckInterval = setInterval(checkNewNotifications, 2000);
        }

        // Also check when page becomes visible again
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                checkNewNotifications();
            }
        });

        // Start real-time notifications
        initRealTimeNotifications();
    </script>
    @stack('scripts')
</body>
</html>