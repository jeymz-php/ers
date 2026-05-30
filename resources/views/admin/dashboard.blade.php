@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<style>
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #1a7a3e, #2db84f);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .stat-icon {
        font-size: 45px;
        opacity: 0.8;
    }
    
    .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: #1a7a3e;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #6e7f72;
        font-size: 14px;
        font-weight: 500;
    }
    
    .stat-trend {
        font-size: 12px;
        margin-top: 10px;
        color: #2db84f;
    }
    
    /* Two Column Layout */
    .dashboard-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }
    
    /* Cards */
    .dashboard-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e8eee9;
    }
    
    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a7a3e;
    }
    
    .card-subtitle {
        font-size: 12px;
        color: #6e7f72;
    }
    
    /* Campus Status Items */
    .campus-item {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e8eee9;
    }
    
    .campus-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .campus-name {
        font-weight: 700;
        color: #1a7a3e;
        font-size: 16px;
        margin-bottom: 10px;
    }
    
    .campus-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    
    .campus-label {
        color: #6e7f72;
        font-size: 13px;
    }
    
    .campus-value {
        font-weight: 600;
        color: #3c4a3f;
    }
    
    .utilization-bar {
        background: #e8eee9;
        border-radius: 10px;
        height: 8px;
        overflow: hidden;
        margin-top: 8px;
    }
    
    .utilization-fill {
        background: linear-gradient(90deg, #1a7a3e, #2db84f);
        height: 100%;
        border-radius: 10px;
        transition: width 0.5s ease;
    }
    
    /* Recent Activity List */
    .activity-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .activity-item {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #e8eee9;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        background: #f0faf3;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-action {
        font-weight: 600;
        color: #1a7a3e;
        font-size: 13px;
        margin-bottom: 4px;
    }
    
    .activity-detail {
        color: #6e7f72;
        font-size: 12px;
        margin-bottom: 4px;
    }
    
    .activity-time {
        color: #b0bdb3;
        font-size: 11px;
    }
    
    .activity-role {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        margin-right: 8px;
    }
    
    .role-user {
        background: #e8eee9;
        color: #3c4a3f;
    }
    
    .role-admin {
        background: #d4f5df;
        color: #1a7a3e;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #b0bdb3;
    }
    
    /* Scrollbar */
    .activity-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .activity-list::-webkit-scrollbar-track {
        background: #f0faf3;
        border-radius: 10px;
    }
    
    .activity-list::-webkit-scrollbar-thumb {
        background: #b0bdb3;
        border-radius: 10px;
    }
    
    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, #1a7a3e, #2db84f);
        border-radius: 20px;
        padding: 25px 30px;
        margin-bottom: 30px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    
    .welcome-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .welcome-subtitle {
        font-size: 14px;
        opacity: 0.9;
    }
    
    /* Mobile Responsive */
    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .dashboard-two-col {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .welcome-title {
            font-size: 20px;
        }
        .stat-value {
            font-size: 28px;
        }
    }
</style>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div class="welcome-title">Welcome back, {{ Auth::user()->name }}! 👋</div>
    <div class="welcome-subtitle">Here's what's happening with your event reservations today.</div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon">📅</div>
        </div>
        <div class="stat-value">{{ number_format($totalBookings) }}</div>
        <div class="stat-label">Total Bookings</div>
        <div class="stat-trend">All time reservations</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon">🎉</div>
        </div>
        <div class="stat-value">{{ number_format($totalEvents) }}</div>
        <div class="stat-label">Total Events</div>
        <div class="stat-trend">Unique events</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon">⏳</div>
        </div>
        <div class="stat-value">{{ number_format($upcomingEvents) }}</div>
        <div class="stat-label">Upcoming Events</div>
        <div class="stat-trend">Next 30 days</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon">⏰</div>
        </div>
        <div class="stat-value">{{ number_format($pendingBookings) }}</div>
        <div class="stat-label">Pending Bookings</div>
        <div class="stat-trend">Awaiting approval</div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="dashboard-two-col">
    <!-- Campus Status -->
    <div class="dashboard-card">
        <div class="card-header">
            <div class="card-title">🏛️ Campus Status</div>
            <div class="card-subtitle">Utilization Rate</div>
        </div>
        <div class="campus-list">
            @forelse($campusUtilization as $campus)
            <div class="campus-item">
                <div class="campus-name">{{ $campus['name'] }}</div>
                <div class="campus-stats">
                    <span class="campus-label">Utilization Rate:</span>
                    <span class="campus-value">{{ $campus['utilization_rate'] }}%</span>
                </div>
                <div class="utilization-bar">
                    <div class="utilization-fill" style="width: {{ $campus['utilization_rate'] }}%;"></div>
                </div>
                <div class="campus-stats" style="margin-top: 8px;">
                    <span class="campus-label">Campus:</span>
                    <span class="campus-value">{{ $campus['booked_days'] }}/{{ $campus['total_days'] }} Days</span>
                </div>
            </div>
            @empty
            <div class="empty-state">No campus data available</div>
            @endforelse
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="dashboard-card">
        <div class="card-header">
            <div class="card-title">📋 Recent Activity</div>
            <div class="card-subtitle">Latest actions</div>
        </div>
        <div class="activity-list">
            @forelse($recentActivities as $activity)
            <div class="activity-item">
                <div class="activity-icon">
                    @if($activity['role'] == 'admin')
                        👨‍💼
                    @else
                        👤
                    @endif
                </div>
                <div class="activity-content">
                    <div class="activity-action">
                        <span class="activity-role role-{{ $activity['role'] }}">{{ strtoupper($activity['role']) }}</span>
                        {{ $activity['action'] }}
                    </div>
                    <div class="activity-detail">{{ $activity['detail'] }}</div>
                    <div class="activity-time">{{ $activity['time'] }}</div>
                </div>
            </div>
            @empty
            <div class="empty-state">No recent activity</div>
            @endforelse
        </div>
    </div>
</div>
@endsection