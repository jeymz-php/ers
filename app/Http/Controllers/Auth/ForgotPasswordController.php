<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }
    
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }
        
        // Generate temporary password
        $tempPassword = $this->generateTemporaryPassword($user->name);
        
        // Update user password and set generated flag
        $user->password = Hash::make($tempPassword);
        $user->is_password_generated = true;  // Make sure this is set
        $user->save();
        
        // Send email with temporary password
        Mail::to($user->email)->send(new PasswordResetMail($user, $tempPassword));
        
        return back()->with('status', 'We have sent a temporary password to your email address.');
    }
}