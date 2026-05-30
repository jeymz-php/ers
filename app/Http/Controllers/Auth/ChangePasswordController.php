<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        // Update password
        $user->password = Hash::make($request->new_password);
        $user->is_password_generated = false;
        $user->save();
        
        // Redirect to appropriate dashboard based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Password changed successfully!');
        }
        
        return redirect()->route('dashboard')->with('success', 'Password changed successfully!');
    }
}