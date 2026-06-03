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
        
        // Get all active chat sessions (from users)
        $activeSessions = ChatSession::where('is_active', true)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get recent messages for each session
        $sessions = [];
        foreach ($activeSessions as $session) {
            $lastMessage = Message::where('session_id', $session->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $unreadCount = Message::where('session_id', $session->id)
                ->where('is_read', false)
                ->where('sender_id', '!=', $adminId)
                ->count();
            
            $sessions[] = [
                'session' => $session,
                'user' => $session->user,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
                'is_handled' => !is_null($session->admin_id),
                'handled_by' => $session->admin_id == $adminId
            ];
        }
        
        return view('admin.chat.index', compact('sessions'));
    }
    
    public function getMessages($sessionId, Request $request)
    {
        $admin = Auth::user();
        $lastId = $request->last_id ?? 0;
        
        $session = ChatSession::findOrFail($sessionId);
        
        // If no admin assigned to this session, assign this admin
        if (!$session->admin_id) {
            $session->update(['admin_id' => $admin->id]);
        }
        
        $query = Message::where('session_id', $sessionId);
        
        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }
        
        $messages = $query->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();
        
        // Mark messages as read
        Message::where('session_id', $sessionId)
            ->where('is_read', false)
            ->where('sender_id', '!=', $admin->id)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        // Clear chat notifications for this session
        Notification::where('user_id', $admin->id)
            ->where('type', 'chat')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        $latestId = Message::where('session_id', $sessionId)->max('id');
        
        return response()->json([
            'success' => true,
            'messages' => $messages,
            'latest_id' => $latestId,
            'session' => $session
        ]);
    }
    
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'nullable|string|max:1000',
                'session_id' => 'required|exists:chat_sessions,id',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);
            
            $admin = Auth::user();
            $sessionId = $request->session_id;
            
            $session = ChatSession::findOrFail($sessionId);
            
            // Assign admin to session if not already assigned
            if (!$session->admin_id) {
                $session->update(['admin_id' => $admin->id]);
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
                'receiver_id' => $session->user_id,
                'session_id' => $sessionId,
                'message' => $request->message ?? '',
                'attachment' => $attachmentPath,
                'attachment_type' => $attachmentType,
                'is_read' => false,
            ]);
            
            // Create notification for the user
            Notification::create([
                'user_id' => $session->user_id,
                'reservation_id' => null,
                'title' => '💬 New Reply from Admin',
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
                'session_id' => 'required|exists:chat_sessions,id',
                'closing_message' => 'required|string|max:500'
            ]);
            
            $adminId = Auth::id();
            $sessionId = $request->session_id;
            
            $session = ChatSession::findOrFail($sessionId);
            
            $session->update([
                'is_active' => false,
                'ended_at' => now(),
                'closing_message' => $request->closing_message,
                'admin_id' => $adminId
            ]);
            
            // Send final closing message
            Message::create([
                'sender_id' => $adminId,
                'receiver_id' => $session->user_id,
                'session_id' => $sessionId,
                'message' => "🔒 *Chat Session Ended*\n\n" . $request->closing_message . "\n\n⚠️ This chat session has been closed.\n\n💡 To start a new conversation, the user can type 'talk to admin' in the AI Assistant.",
                'is_read' => false,
            ]);
            
            // Notify user
            Notification::create([
                'user_id' => $session->user_id,
                'reservation_id' => null,
                'title' => '🔒 Chat Session Ended',
                'message' => 'The administrator has ended the chat session.',
                'type' => 'chat',
                'is_read' => false,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Chat session ended successfully'
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
        $adminId = Auth::id();
        
        // Get all active sessions where admin hasn't replied yet or has unread messages
        $unreadCount = Message::where('receiver_id', null)
            ->where('is_read', false)
            ->where('sender_id', '!=', $adminId)
            ->count();
        
        return response()->json(['count' => $unreadCount]);
    }
}