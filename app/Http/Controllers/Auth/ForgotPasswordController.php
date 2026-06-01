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
        $user->is_password_generated = true;
        $user->save();
        
        // Send email with temporary password
        Mail::to($user->email)->send(new PasswordResetMail($user, $tempPassword));
        
        return back()->with('status', 'We have sent a temporary password to your email address.');
    }
    
    /**
     * Generate a temporary password for password reset
     * Format: FirstInitial + LastInitial + Temp + MMDD + random
     * Example: JDTemp0515123 (John Doe, May 15)
     */
    private function generateTemporaryPassword($name)
    {
        // Split name into parts
        $nameParts = explode(' ', trim($name));
        $firstName = $nameParts[0] ?? '';
        $lastName = count($nameParts) > 1 ? end($nameParts) : '';
        
        // Get initials (first letter of first name and first letter of last name)
        $firstInitial = !empty($firstName) ? strtoupper(substr($firstName, 0, 1)) : 'X';
        $lastInitial = !empty($lastName) ? strtoupper(substr($lastName, 0, 1)) : 'X';
        
        // Get current date in MMDD format (without year)
        $date = Carbon::now()->format('md');
        
        // Generate random number (3 digits)
        $random = rand(100, 999);
        
        // Combine: FN + LN + Temp + MMDD + Random
        $password = $firstInitial . $lastInitial . 'Temp' . $date . $random;
        
        return $password;
    }
}