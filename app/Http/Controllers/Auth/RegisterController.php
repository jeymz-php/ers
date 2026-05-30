<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Campus;
use App\Mail\UserCredentialsMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class RegisterController extends Controller
{
    protected string $redirectTo = '/dashboard';

    public function showRegistrationForm()
    {
        $campuses = Campus::getActiveCampuses();
        return view('auth.register', compact('campuses'));
    }

    /**
     * Generate a secure system password
     * NEW FORMAT: FirstInitial + LastInitial + UCC + MMDD + !
     * Example: JDUCC0115! (John Doe, Jan 15)
     * Example: MSMITHUCC1225! (Mary Smith, Dec 25)
     */
    protected function generatePassword($name)
    {
        // Split name into parts
        $nameParts = explode(' ', trim($name));
        $firstName = $nameParts[0] ?? '';
        $lastName = count($nameParts) > 1 ? end($nameParts) : '';
        
        // Get initials (first letter of first name and first letter of last name)
        $firstInitial = !empty($firstName) ? strtoupper(substr($firstName, 0, 1)) : 'X';
        $lastInitial = !empty($lastName) ? strtoupper(substr($lastName, 0, 1)) : 'X';
        
        // Get current date in MMDD format (without year)
        $date = Carbon::now()->format('md'); // This gives: 0115 for Jan 15, 1225 for Dec 25
        
        // Combine: FN + LN + UCC + MMDD + !
        $password = $firstInitial . $lastInitial . 'UCC' . $date . '!';
        
        // Make sure password is at least 8 characters (it will be: 2 letters + 3 letters UCC + 4 digits + 1 symbol = 10 chars)
        // Example: JDUCC0115! = 10 characters
        
        return $password;
    }

    public function register(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'confirmed'],
            'email_confirmation' => ['required', 'email'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'campus_id' => ['required', 'exists:campuses,id'],
        ], [
            'name.regex' => 'Name should only contain letters and spaces.',
            'phone_number.regex' => 'Please enter a valid phone number.',
            'email.confirmed' => 'Email confirmation does not match.',
            'email.unique' => 'This email is already registered.',
        ]);

        // Generate system password
        $plainPassword = $this->generatePassword($validated['name']);
        
        // Create user with pending status
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'campus_id' => $validated['campus_id'],
            'password' => Hash::make($plainPassword),
            'is_password_generated' => true,
            'account_status' => 'pending', // Important: Set to pending
            'role' => 'user', // Important: Set role to user
        ]);

        // Send credentials via email
        try {
            Mail::to($user->email)->send(new UserCredentialsMail($user, $plainPassword));
        } catch (\Exception $e) {
            \Log::error('Failed to send credentials email: ' . $e->getMessage());
        }

        // DO NOT auto-login - redirect to login page with message
        return redirect()->route('login')
            ->with('status', 'Account created successfully! Please wait for admin approval. You will receive an email once your account is approved.');
    }
}