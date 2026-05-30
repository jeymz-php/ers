<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // If user is admin or super admin, redirect to admin dashboard
        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        // If user account is pending, show pending page
        if ($user && $user->isPending()) {
            return redirect()->route('user.pending');
        }
        
        // If user account is rejected, show rejected page
        if ($user && $user->isRejected()) {
            return redirect()->route('user.rejected');
        }
        
        return $next($request);
    }
}