<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
    
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();
        
        if ($notification) {
            $notification->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return response()->json(['success' => true]);
    }
    
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }

    public function getLatest()
    {
        $notification = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($notification) {
            return response()->json([
                'notification' => [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'created_at' => $notification->created_at,
                ]
            ]);
        }
        
        return response()->json(['notification' => null]);
    }

    public function markAsReadAndRedirect($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();
        
        if ($notification) {
            $notification->update([
                'is_read' => true,
                'read_at' => now()
            ]);
            
            // Check if it's a vehicle reservation notification
            if ($notification->vehicle_reservation_id) {
                return redirect()->route('admin.vehicle-reservations.show', $notification->vehicle_reservation_id)
                    ->with('highlight', $notification->vehicle_reservation_id);
            }

            // Check if it's a reservation notification (has reservation_id)
            if ($notification->reservation_id) {
                // Redirect to reservation page
                return redirect()->route('admin.reservations.show', $notification->reservation_id)
                    ->with('highlight', $notification->reservation_id);
            } else {
                // For chat notifications or other types, redirect to appropriate page
                if ($notification->type === 'chat') {
                    return redirect()->route('admin.chat.index')
                        ->with('success', 'New message from user');
                }
                
                // Default redirect to dashboard
                return redirect()->route('admin.dashboard')
                    ->with('info', 'Notification marked as read');
            }
        }
        
        return redirect()->route('admin.dashboard');
    }
}