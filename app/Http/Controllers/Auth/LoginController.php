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

            $request->session()->put('show_system_update', true);

            return $this->redirectAfterLogin($request, route('admin.dashboard'));
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
                $request->session()->put('show_system_update', true);
                return $this->redirectAfterLogin($request, route('admin.dashboard'));
            }
            
            // Check account status for regular users
            if ($user->isPending()) {
                return redirect()->route('user.pending');
            }
            
            if ($user->isRejected()) {
                return redirect()->route('user.rejected');
            }
            
            $request->session()->put('show_system_update', true);

            return $this->redirectAfterLogin($request, route('dashboard'));
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

    /**
     * Redirect to wherever the person was originally trying to go — but
     * ONLY if that's a real, known full page (e.g. the pages our QR codes
     * point to: /reservations, /vehicle-reservations, /availability).
     * Anything else (background JSON/AJAX polling endpoints in particular,
     * like the notification bell's unread-count check) falls back to the
     * normal dashboard, so a stale or unexpected "intended" URL can never
     * strand someone on a raw JSON response after logging in again.
     */
    private function redirectAfterLogin(Request $request, string $default)
    {
        $intended = $request->session()->pull('url.intended');

        if ($intended && $this->isSafeIntendedUrl($intended)) {
            return redirect()->to($intended);
        }

        return redirect()->to($default);
    }

    private function isSafeIntendedUrl(string $url): bool
    {
        $path = '/' . ltrim(parse_url($url, PHP_URL_PATH) ?? '', '/');

        $allowedPrefixes = [
            '/reservations',
            '/vehicle-reservations',
            '/availability',
            '/dashboard',
        ];

        foreach ($allowedPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return true;
            }
        }

        return false;
    }
}