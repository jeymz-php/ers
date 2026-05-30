<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Campus;
use App\Models\AdminActionLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Total Bookings (All reservations)
        $totalBookings = Reservation::count();
        
        // Total Events (Unique event names)
        $totalEvents = Reservation::distinct('event_name')->count('event_name');
        
        // Upcoming Events (Next 30 days)
        $upcomingEvents = Reservation::where('event_date', '>=', Carbon::today())
            ->where('event_date', '<=', Carbon::today()->addDays(30))
            ->where('status', 'approved')
            ->count();
        
        // Pending Bookings
        $pendingBookings = Reservation::where('status', 'pending')->count();
        
        // Campus Utilization
        $campuses = Campus::where('is_active', true)->get();
        $campusUtilization = [];
        
        foreach ($campuses as $campus) {
            $startDate = Carbon::today()->startOfMonth();
            $endDate = Carbon::today()->endOfMonth();
            $totalDays = $startDate->diffInDays($endDate) + 1;
            
            $bookedDays = Reservation::where('campus_id', $campus->id)
                ->where('status', 'approved')
                ->whereBetween('event_date', [$startDate, $endDate])
                ->distinct('event_date')
                ->count('event_date');
            
            $utilizationRate = $totalDays > 0 ? round(($bookedDays / $totalDays) * 100, 2) : 0;
            
            $campusUtilization[] = [
                'name' => $campus->name,
                'utilization_rate' => $utilizationRate,
                'booked_days' => $bookedDays,
                'total_days' => $totalDays,
            ];
        }
        
        // Recent Activities
        $recentActivities = [];
        
        // Get recent admin actions
        $adminLogs = AdminActionLog::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($adminLogs as $log) {
            $recentActivities[] = [
                'role' => 'admin',
                'action' => $log->action,
                'detail' => $log->details ?? 'No details',
                'time' => Carbon::parse($log->created_at)->format('M d, Y \a\t g:i A'),
            ];
        }
        
        // Get recent reservation creations (NEW!)
        $recentReservations = Reservation::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($recentReservations as $res) {
            $userName = $res->user ? $res->user->name : 'Unknown User';
            $remarks = json_decode($res->remarks, true);
            $multipleDates = $remarks['multiple_dates'] ?? [$res->event_date];
            $isMultiDate = count($multipleDates) > 1;
            $dateDisplay = $isMultiDate ? count($multipleDates) . ' dates' : Carbon::parse($res->event_date)->format('M d, Y');
            
            $recentActivities[] = [
                'role' => 'user',
                'action' => 'Created Reservation',
                'detail' => "{$userName} requested '{$res->event_name}' for {$dateDisplay}",
                'time' => Carbon::parse($res->created_at)->format('M d, Y \a\t g:i A'),
            ];
        }
        
        // Get recent user registrations
        $recentUsers = User::where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($recentUsers as $user) {
            $recentActivities[] = [
                'role' => 'user',
                'action' => 'Registered Account',
                'detail' => "New user: {$user->name} ({$user->email})",
                'time' => Carbon::parse($user->created_at)->format('M d, Y \a\t g:i A'),
            ];
        }
        
        // Get recent status changes (approvals/rejections)
        $statusChanges = Reservation::whereNotNull('approved_at')
            ->orderBy('approved_at', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($statusChanges as $res) {
            $userName = $res->user ? $res->user->name : 'Unknown User';
            $recentActivities[] = [
                'role' => 'admin',
                'action' => 'Reservation ' . ucfirst($res->status),
                'detail' => "{$res->event_name} by {$userName} was " . strtoupper($res->status),
                'time' => Carbon::parse($res->approved_at)->format('M d, Y \a\t g:i A'),
            ];
        }
        
        // Sort activities by time (most recent first) and take top 15
        $recentActivities = collect($recentActivities)
            ->sortByDesc(function($item) {
                return strtotime($item['time']);
            })
            ->take(15)
            ->values()
            ->all();
        
        return view('admin.dashboard', compact(
            'totalBookings',
            'totalEvents',
            'upcomingEvents',
            'pendingBookings',
            'campusUtilization',
            'recentActivities'
        ));
    }
}