<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSystemStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (!SystemSetting::isDown()) {
            return $next($request);
        }

        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        if ($request->routeIs('admin.login') || $request->routeIs('admin.login.submit') || $request->routeIs('maintenance')) {
            return $next($request);
        }

        return response()->view('system.maintenance', [
            'maintenanceTitle' => SystemSetting::getValue('maintenance_title', 'System Maintenance'),
            'maintenanceMessage' => SystemSetting::getValue('maintenance_message', 'The UCC Event Reservation System is temporarily down for maintenance. Please check back shortly.'),
        ], 503);
    }
}
