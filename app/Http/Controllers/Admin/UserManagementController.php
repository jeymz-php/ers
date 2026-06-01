<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Campus;
use App\Mail\AccountApprovedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        // Get approved users
        $approvedQuery = User::where('role', 'user')
            ->where('account_status', 'approved')
            ->with('campus');
        
        // Get pending users
        $pendingQuery = User::where('role', 'user')
            ->where('account_status', 'pending')
            ->with('campus');
        
        // Apply search filter to both queries
        if ($request->search) {
            $approvedQuery->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
            $pendingQuery->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // Apply campus filter to both queries
        if ($request->campus_id && $request->campus_id != 'all') {
            $approvedQuery->where('campus_id', $request->campus_id);
            $pendingQuery->where('campus_id', $request->campus_id);
        }
        
        $approvedUsers = $approvedQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'approved_page');
        $pendingUsers = $pendingQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'pending_page');
        $campuses = Campus::all();
        
        return view('admin.users.index', compact('approvedUsers', 'pendingUsers', 'campuses'));
    }
    
    public function show($id)
    {
        $user = User::where('role', 'user')->with('campus')->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }
    
    public function approve($id)
    {
        $user = User::where('role', 'user')->findOrFail($id);
        
        $user->update([
            'account_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
        
        \App\Models\AdminActionLog::create([
            'admin_id' => auth()->id(),
            'target_user_id' => $user->id,
            'action' => 'approve_user',
            'details' => 'User account approved',
        ]);
        
        Mail::to($user->email)->send(new AccountApprovedMail($user));
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User account approved successfully');
    }
    
    public function bulkApprove(Request $request)
    {
        // Decode the JSON string to array
        $ids = json_decode($request->ids, true);
        
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'No users selected');
        }
        
        $users = User::whereIn('id', $ids)
            ->where('role', 'user')
            ->where('account_status', 'pending')
            ->get();
        
        $count = 0;
        foreach ($users as $user) {
            $user->update([
                'account_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);
            
            \App\Models\AdminActionLog::create([
                'admin_id' => auth()->id(),
                'target_user_id' => $user->id,
                'action' => 'approve_user',
                'details' => 'User account approved (bulk)',
            ]);
            
            Mail::to($user->email)->send(new AccountApprovedMail($user));
            $count++;
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', "$count user(s) approved successfully");
    }
    
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);
        
        $user = User::where('role', 'user')->findOrFail($id);
        $userEmail = $user->email;
        $userName = $user->name;
        
        Mail::to($userEmail)->send(new AccountRejectedMail($user, $request->reason));
        
        \App\Models\AdminActionLog::create([
            'admin_id' => auth()->id(),
            'target_user_id' => $user->id,
            'action' => 'reject_user',
            'details' => "User account rejected. Reason: {$request->reason}",
        ]);
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', "User {$userName} has been rejected and deleted");
    }
    
    public function destroy($id)
    {
        $user = User::where('role', 'user')->findOrFail($id);
        $userName = $user->name;
        
        \App\Models\AdminActionLog::create([
            'admin_id' => auth()->id(),
            'target_user_id' => $user->id,
            'action' => 'delete_user',
            'details' => 'User account deleted by admin',
        ]);
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', "User {$userName} has been deleted");
    }
}