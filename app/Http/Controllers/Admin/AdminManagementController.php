<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Campus;
use App\Mail\AdminCredentialsMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('super_admin');
    }
    
    public function index()
    {
        $admins = User::whereIn('role', ['admin', 'super_admin'])
            ->with('campus')
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(10);
            
        return view('admin.admins.index', compact('admins'));
    }
    
    public function create()
    {
        $campuses = Campus::all();
        return view('admin.admins.create', compact('campuses'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'campus_id' => 'required|exists:campuses,id',
            'role' => 'required|in:admin,super_admin',
        ]);
        
        // Generate a random secure password
        $password = $this->generateAdminPassword();
        
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'campus_id' => $request->campus_id,
            'role' => $request->role,
            'password' => Hash::make($password),
            'account_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'is_password_generated' => true,
        ]);
        
        // Send email with credentials
        try {
            Mail::to($admin->email)->send(new AdminCredentialsMail($admin, $password));
            
            \App\Models\AdminActionLog::create([
                'admin_id' => auth()->id(),
                'target_user_id' => $admin->id,
                'action' => 'create_admin',
                'details' => "Created new {$request->role} account. Credentials sent via email.",
            ]);
            
            return redirect()->route('admin.admins.index')
                ->with('success', "Admin account created successfully! Credentials have been sent to {$admin->email}");
                
        } catch (\Exception $e) {
            \Log::error('Failed to send admin credentials email: ' . $e->getMessage());
            
            return redirect()->route('admin.admins.index')
                ->with('warning', "Admin account created but email could not be sent. Temporary password: {$password}");
        }
    }
    
    private function generateAdminPassword()
    {
        // Generate a secure password: Admin + random numbers + random letters
        $random = Str::random(6);
        return 'Admin' . rand(1000, 9999) . $random;
    }
    
    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        
        // Prevent deleting yourself or the last super admin
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account');
        }
        
        if ($admin->role === 'super_admin' && User::where('role', 'super_admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the only super admin account');
        }
        
        \App\Models\AdminActionLog::create([
            'admin_id' => auth()->id(),
            'target_user_id' => $admin->id,
            'action' => 'delete_admin',
            'details' => "Deleted {$admin->role} account",
        ]);
        
        $admin->delete();
        
        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin account deleted successfully');
    }
    
    public function edit($id)
    {
        $admin = User::whereIn('role', ['admin', 'super_admin'])->findOrFail($id);
        $campuses = Campus::all();
        return view('admin.admins.edit', compact('admin', 'campuses'));
    }
    
    public function update(Request $request, $id)
    {
        $admin = User::whereIn('role', ['admin', 'super_admin'])->findOrFail($id);
    
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($admin->id)],
            'phone_number' => 'required|string|max:20',
            'campus_id'    => 'required|exists:campuses,id',
            'role'         => 'required|in:admin,super_admin',
        ]);
    
        // Prevent demoting the last super admin
        if ($admin->role === 'super_admin' && $request->role !== 'super_admin') {
            if (User::where('role', 'super_admin')->count() <= 1) {
                return back()->withErrors(['role' => 'Cannot change role: this is the only Super Admin account.'])->withInput();
            }
        }
    
        $admin->update([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'campus_id'    => $request->campus_id,
            'role'         => $request->role,
        ]);
    
        \App\Models\AdminActionLog::create([
            'admin_id'       => auth()->id(),
            'target_user_id' => $admin->id,
            'action'         => 'edit_admin',
            'details'        => "Updated {$admin->role} account details.",
        ]);
    
        return redirect()->route('admin.admins.index')
            ->with('success', "Admin account for {$admin->name} has been updated successfully.");
    }
}