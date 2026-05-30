<!-- User Topbar Partial -->
<style>
    .user-topbar {
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

    .system-clock {
        text-align: center;
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        padding: 8px 20px;
        border-radius: 50px;
        color: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .clock-time {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .clock-date {
        font-size: 10px;
        opacity: 0.9;
        margin-top: 2px;
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

    .user-email {
        font-size: 11px;
        color: #6e7f72;
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
        cursor: pointer;
        transition: transform 0.3s;
    }

    .user-avatar:hover {
        transform: scale(1.05);
    }

    @media (max-width: 768px) {
        .user-topbar {
            padding: 12px 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .page-title {
            font-size: 16px;
        }
        .system-clock {
            padding: 5px 12px;
        }
        .clock-time {
            font-size: 14px;
        }
        .user-info {
            display: none;
        }
    }
</style>

<div class="user-topbar">
    <div class="page-title">EVENT SCHEDULE</div>
    
    <!-- System Clock -->
    <div class="system-clock">
        <div class="clock-time" id="liveClock">--:-- --</div>
        <div class="clock-date" id="liveDate">Loading...</div>
    </div>
    
    <div class="user-menu">
        <div class="user-info">
            <div class="user-name">{{ Auth::user()->name }}</div>
            <div class="user-email">{{ Auth::user()->email }}</div>
        </div>
        <div class="user-avatar" onclick="toggleUserMenu()">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div>

<script>
    function updatePhilippineClock() {
        // Get current time in Asia/Manila timezone
        const options = { 
            timeZone: 'Asia/Manila',
            hour12: true,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        
        const dateOptions = {
            timeZone: 'Asia/Manila',
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        
        const now = new Date();
        const phTime = now.toLocaleTimeString('en-PH', options);
        const phDate = now.toLocaleDateString('en-PH', dateOptions);
        
        document.getElementById('liveClock').textContent = phTime;
        document.getElementById('liveDate').textContent = phDate;
    }
    
    // Update clock every second
    updatePhilippineClock();
    setInterval(updatePhilippineClock, 1000);
    
    function toggleUserMenu() {
        // You can add dropdown menu functionality here
        console.log('User menu clicked');
    }
</script>