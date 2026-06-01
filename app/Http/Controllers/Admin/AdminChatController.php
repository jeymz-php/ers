<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\ChatSession;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminChatController extends Controller
{
    public function index()
    {
        $adminId = Auth::id();
        
        // Get all conversations where this admin is participant
        $conversations = Message::where('receiver_id', $adminId)
            ->orWhere('sender_id', $adminId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($message) use ($adminId) {
                return $message->sender_id == $adminId ? $message->receiver_id : $message->sender_id;
            });
        
        $users = [];
        foreach ($conversations as $userId => $messages) {
            $user = User::find($userId);
            if ($user && $user->role === 'user') {
                // Check if there's an active session with this user and this admin
                $activeSession = ChatSession::where('user_id', $userId)
                    ->where('admin_id', $adminId)
                    ->where('is_active', true)
                    ->first();
                    
                $users[] = [
                    'user' => $user,
                    'last_message' => $messages->first(),
                    'unread_count' => Message::where('sender_id', $userId)
                        ->where('receiver_id', $adminId)
                        ->where('is_read', false)
                        ->count(),
                    'session_active' => !is_null($activeSession),
                    'session_id' => $activeSession ? $activeSession->id : null
                ];
            }
        }
        
        $allUsers = User::where('role', 'user')->get();
        
        return view('admin.chat.index', compact('users', 'allUsers'));
    }
    
    public function getMessages($userId, Request $request)
    {
        $admin = Auth::user();
        $lastId = $request->last_id ?? 0;
        
        $query = Message::where(function($q) use ($admin, $userId) {
            $q->where('sender_id', $admin->id)->where('receiver_id', $userId);
        })->orWhere(function($q) use ($admin, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $admin->id);
        });
        
        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }
        
        $messages = $query->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();
        
        Message::where('sender_id', $userId)
            ->where('receiver_id', $admin->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        Notification::where('user_id', $admin->id)
            ->where('type', 'chat')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        $latestId = Message::where(function($q) use ($admin, $userId) {
            $q->where('sender_id', $admin->id)->where('receiver_id', $userId);
        })->orWhere(function($q) use ($admin, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $admin->id);
        })->max('id');
        
        // Check if session is active with this specific user and this admin
        $activeSession = ChatSession::where('user_id', $userId)
            ->where('admin_id', $admin->id)
            ->where('is_active', true)
            ->first();
        
        return response()->json([
            'success' => true,
            'messages' => $messages,
            'latest_id' => $latestId,
            'session_active' => !is_null($activeSession)
        ]);
    }
    
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'nullable|string|max:1000',
                'receiver_id' => 'required|exists:users,id',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);
            
            $receiverId = $request->receiver_id;
            $admin = Auth::user();
            
            // Check if session exists with this user and this admin
            $activeSession = ChatSession::where('user_id', $receiverId)
                ->where('admin_id', $admin->id)
                ->where('is_active', true)
                ->first();
            
            if (!$activeSession) {
                // Auto-create a session if none exists (admin initiating chat)
                $activeSession = ChatSession::create([
                    'user_id' => $receiverId,
                    'admin_id' => $admin->id,
                    'is_active' => true,
                ]);
            }
            
            $attachmentPath = null;
            $attachmentType = null;
            
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $attachmentPath = $file->storeAs('chat_attachments', $filename, 'public');
                $attachmentType = $file->getMimeType();
            }
            
            $message = Message::create([
                'sender_id' => $admin->id,
                'receiver_id' => $receiverId,
                'message' => $request->message ?? '',
                'attachment' => $attachmentPath,
                'attachment_type' => $attachmentType,
                'is_read' => false,
            ]);
            
            // Create notification for user
            Notification::create([
                'user_id' => $receiverId,
                'reservation_id' => null,
                'title' => '💬 New Message from Admin',
                'message' => $admin->name . ': ' . substr($request->message ?? 'Sent an attachment', 0, 50),
                'type' => 'chat',
                'is_read' => false,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'last_id' => $message->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Admin sendMessage error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function endSession(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'closing_message' => 'required|string|max:500'
            ]);
            
            $adminId = Auth::id();
            $userId = $request->user_id;
            
            // Find the active session for this specific admin-user pair
            $session = ChatSession::where('user_id', $userId)
                ->where('admin_id', $adminId)
                ->where('is_active', true)
                ->first();
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active chat session found with this user.'
                ], 404);
            }
            
            $session->update([
                'is_active' => false,
                'ended_at' => now(),
                'closing_message' => $request->closing_message
            ]);
            
            // Send final closing message
            Message::create([
                'sender_id' => $adminId,
                'receiver_id' => $userId,
                'message' => "🔒 *Chat Session Ended*\n\n" . $request->closing_message . "\n\n⚠️ This chat session has been closed.\n\n💡 To start a new conversation, the user can type 'talk to admin' in the AI Assistant.",
                'is_read' => false,
            ]);
            
            // Notify user
            Notification::create([
                'user_id' => $userId,
                'reservation_id' => null,
                'title' => '🔒 Chat Session Ended',
                'message' => 'The administrator has ended the chat session.',
                'type' => 'chat',
                'is_read' => false,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Chat session ended successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('End session error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }
}