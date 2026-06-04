<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $redirectTo = '/';

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showAdminLoginForm()
    {
        return view('auth.login', ['adminMode' => true]);
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->isAdmin()) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => __('The provided credentials do not match our admin records.'),
                ]);
            }

            if ($user->is_password_generated) {
                return redirect()->route('password.change');
            }

            return redirect()->route('admin.dashboard');
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Check if user is using a temporary/generated password
            if ($user->is_password_generated) {
                return redirect()->route('password.change');
            }
            
            // Redirect based on user role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            
            // Check account status for regular users
            if ($user->isPending()) {
                return redirect()->route('user.pending');
            }
            
            if ($user->isRejected()) {
                return redirect()->route('user.rejected');
            }
            
            return redirect()->route('dashboard');
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
                        ->with('status', 'You have been logged out successfully.');
    }
}