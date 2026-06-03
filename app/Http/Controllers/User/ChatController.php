<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\ChatSession;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get active session
        $activeSession = ChatSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();
        
        // Get all admins for display (but user doesn't choose)
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        
        // Get last 50 messages from active session
        $messages = [];
        if ($activeSession) {
            $messages = Message::where('session_id', $activeSession->id)
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'asc')
                ->get();
        }
        
        return view('user.chat', compact('admins', 'activeSession', 'messages'));
    }
    
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'nullable|string|max:1000',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);
            
            $user = Auth::user();
            
            // Get or create active session
            $session = ChatSession::where('user_id', $user->id)
                ->where('is_active', true)
                ->first();
            
            if (!$session) {
                $session = ChatSession::create([
                    'user_id' => $user->id,
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
                'sender_id' => $user->id,
                'receiver_id' => null, // No specific receiver - broadcast to all admins
                'session_id' => $session->id,
                'message' => $request->message ?? '',
                'attachment' => $attachmentPath,
                'attachment_type' => $attachmentType,
                'is_read' => false,
            ]);
            
            // Create notifications for ALL admins
            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'reservation_id' => null,
                    'title' => '💬 New Chat Message',
                    'message' => $user->name . ': ' . substr($request->message ?? 'Sent an attachment', 0, 50),
                    'type' => 'chat',
                    'is_read' => false,
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'last_id' => $message->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('User sendMessage error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getMessages(Request $request)
    {
        $user = Auth::user();
        $lastId = $request->last_id ?? 0;
        
        $session = ChatSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();
        
        if (!$session) {
            return response()->json([
                'success' => true,
                'messages' => [],
                'latest_id' => 0,
                'session_active' => false
            ]);
        }
        
        $query = Message::where('session_id', $session->id);
        
        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }
        
        $messages = $query->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();
        
        // Mark messages as read
        Message::where('session_id', $session->id)
            ->where('receiver_id', null)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        $latestId = Message::where('session_id', $session->id)->max('id');
        
        return response()->json([
            'success' => true,
            'messages' => $messages,
            'latest_id' => $latestId,
            'session_active' => true
        ]);
    }
    
    public function getUnreadCount()
    {
        $session = ChatSession::where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();
        
        if (!$session) {
            return response()->json(['count' => 0]);
        }
        
        $count = Message::where('session_id', $session->id)
            ->where('receiver_id', null)
            ->where('is_read', false)
            ->where('sender_id', '!=', Auth::id())
            ->count();
        
        return response()->json(['count' => $count]);
    }
    
    public function endSession(Request $request)
    {
        $user = Auth::user();
        
        $session = ChatSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();
        
        if ($session) {
            $session->update([
                'is_active' => false,
                'ended_at' => now(),
            ]);
            
            // Send closing message
            Message::create([
                'sender_id' => null,
                'receiver_id' => $user->id,
                'session_id' => $session->id,
                'message' => "🔒 *Chat Session Ended*\n\nThank you for chatting with us. The conversation has been closed.\n\n💡 To start a new chat, type 'talk to admin' in the AI Assistant.",
                'is_read' => false,
            ]);
        }
        
        return response()->json(['success' => true]);
    }
}