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
        $sessions = $this->buildSessionsList();

        return view('admin.chat.index', compact('sessions'));
    }

    /**
     * Lightweight JSON version of the sessions list, polled by the sidebar
     * every few seconds so admins see new/incoming chats without reloading.
     */
    public function sessionsJson()
    {
        $sessions = $this->buildSessionsList();

        $payload = array_map(function ($item) {
            return [
                'session_id'   => $item['session']->id,
                'user_name'    => $item['user']->name,
                'last_message' => $item['last_message']->message ?? 'No messages',
                'last_message_at' => optional($item['last_message'])->created_at,
                'unread_count' => $item['unread_count'],
                'is_handled'   => $item['is_handled'],
                'handled_by'   => $item['handled_by'],
                'started_by_admin' => $item['started_by_admin'],
            ];
        }, $sessions);

        return response()->json(['success' => true, 'sessions' => $payload]);
    }

    /**
     * Search approved ERS users (students/professors) so an Admin/Super Admin
     * can start a brand new conversation with them — giving Admin the
     * "first move" instead of always waiting for the user to write first.
     */
    /**
     * List all approved ERS users (students/professors) to populate the
     * "Start a New Chat" dropdown, so an Admin/Super Admin can pick anyone
     * and have the "first move" instead of always waiting for the user to
     * write first.
     */
    public function searchUsers(Request $request)
    {
        $users = User::where('role', 'user')
            ->where('account_status', 'approved')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return response()->json(['success' => true, 'users' => $users]);
    }

    /**
     * Admin/Super Admin starts a new conversation with a user (or reopens
     * their existing active session, to avoid creating duplicates) and
     * sends the first message.
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $admin = Auth::user();
        $targetUser = User::findOrFail($request->user_id);

        if ($targetUser->role !== 'user') {
            return response()->json(['success' => false, 'message' => 'You can only start a chat with an ERS user.'], 422);
        }

        $session = ChatSession::where('user_id', $targetUser->id)
            ->where('is_active', true)
            ->first();

        if (!$session) {
            $session = ChatSession::create([
                'user_id' => $targetUser->id,
                'admin_id' => $admin->id,
                'is_active' => true,
            ]);
        } elseif (!$session->admin_id) {
            $session->update(['admin_id' => $admin->id]);
        }

        $message = Message::create([
            'sender_id' => $admin->id,
            'receiver_id' => $targetUser->id,
            'session_id' => $session->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $targetUser->id,
            'reservation_id' => null,
            'title' => '💬 New Message from ' . $admin->name,
            'message' => $admin->name . ': ' . substr($request->message, 0, 50),
            'type' => 'chat',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'session_id' => $session->id,
            'user_name' => $targetUser->name,
            'message' => $message,
        ]);
    }

    private function buildSessionsList()
    {
        $adminId = Auth::id();

        $activeSessions = ChatSession::where('is_active', true)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $sessions = [];
        foreach ($activeSessions as $session) {
            $lastMessage = Message::where('session_id', $session->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $unreadCount = Message::where('session_id', $session->id)
                ->where('is_read', false)
                ->where('sender_id', '!=', $adminId)
                ->count();

            $firstMessage = Message::where('session_id', $session->id)
                ->orderBy('created_at', 'asc')
                ->first();

            $sessions[] = [
                'session'    => $session,
                'user'       => $session->user,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
                'is_handled' => !is_null($session->admin_id),
                'handled_by' => $session->admin_id == $adminId,
                'started_by_admin' => $firstMessage && $firstMessage->sender_id && $firstMessage->sender_id != $session->user_id,
            ];
        }

        return $sessions;
    }

    public function getMessages($sessionId, Request $request)
    {
        $admin  = Auth::user();
        $lastId = $request->last_id ?? 0;

        $session = ChatSession::findOrFail($sessionId);

        if (!$session->admin_id) {
            $session->update(['admin_id' => $admin->id]);
        }

        $query = Message::where('session_id', $sessionId);
        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }

        $messages  = $query->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();
        $latestId  = Message::where('session_id', $sessionId)->max('id');

        Message::where('session_id', $sessionId)
            ->where('is_read', false)
            ->where('sender_id', '!=', $admin->id)
            ->update(['is_read' => true, 'read_at' => now()]);

        Notification::where('user_id', $admin->id)
            ->where('type', 'chat')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'success'    => true,
            'messages'   => $messages,
            'latest_id'  => $latestId,
            'session'    => $session,
            'is_active'  => (bool) $session->is_active,
        ]);
    }

    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message'    => 'nullable|string|max:1000',
                'session_id' => 'required|exists:chat_sessions,id',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);

            $admin     = Auth::user();
            $sessionId = $request->session_id;
            $session   = ChatSession::findOrFail($sessionId);

            if (!$session->admin_id) {
                $session->update(['admin_id' => $admin->id]);
            }

            $attachmentPath = null;
            $attachmentType = null;

            if ($request->hasFile('attachment')) {
                $file           = $request->file('attachment');
                $filename       = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $attachmentPath = $file->storeAs('chat_attachments', $filename, 'public');
                $attachmentType = $file->getMimeType();
            }

            $message = Message::create([
                'sender_id'       => $admin->id,
                'receiver_id'     => $session->user_id,
                'session_id'      => $sessionId,
                'message'         => $request->message ?? '',
                'attachment'      => $attachmentPath,
                'attachment_type' => $attachmentType,
                'is_read'         => false,
            ]);

            Notification::create([
                'user_id'        => $session->user_id,
                'reservation_id' => null,
                'title'          => '💬 New Reply from Admin',
                'message'        => $admin->name . ': ' . substr($request->message ?? 'Sent an attachment', 0, 50),
                'type'           => 'chat',
                'is_read'        => false,
            ]);

            return response()->json([
                'success'  => true,
                'message'  => $message,
                'last_id'  => $message->id
            ]);

        } catch (\Exception $e) {
            Log::error('Admin sendMessage error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function endSession(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|exists:chat_sessions,id',
            ]);

            $admin     = Auth::user();
            $sessionId = $request->session_id;
            $session   = ChatSession::findOrFail($sessionId);
            $user      = $session->user;

            // Auto-generate the closing message
            $autoMessage =
                "🔒 Chat Session Ended\n\n" .
                "Hello {$user->name},\n\n" .
                "Your chat session has been officially closed by {$admin->name} " .
                "on " . now()->format('F d, Y') . " at " . now()->format('h:i A') . ".\n\n" .
                "If you need further assistance, you may start a new conversation by typing " .
                "\"talk to admin\" in the AI Assistant.\n\n" .
                "Thank you for reaching out to us. 😊";

            $session->update([
                'is_active'       => false,
                'ended_at'        => now(),
                'closing_message' => $autoMessage,
                'admin_id'        => $admin->id,
            ]);

            // Post the closing message into the chat
            Message::create([
                'sender_id'   => null,
                'receiver_id' => $session->user_id,
                'session_id'  => $sessionId,
                'message'     => $autoMessage,
                'is_read'     => false,
            ]);

            // Notify the user
            Notification::create([
                'user_id'        => $session->user_id,
                'reservation_id' => null,
                'title'          => '🔒 Chat Session Ended',
                'message'        => "Your chat session has been closed by {$admin->name}.",
                'type'           => 'chat',
                'is_read'        => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat session ended successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('End session error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getUnreadCount()
    {
        $adminId     = Auth::id();
        $unreadCount = Message::where('receiver_id', null)
            ->where('is_read', false)
            ->where('sender_id', '!=', $adminId)
            ->count();

        return response()->json(['count' => $unreadCount]);
    }
}