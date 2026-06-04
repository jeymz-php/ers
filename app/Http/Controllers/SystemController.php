<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function landing()
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if (SystemSetting::isDown()) {
                return view('system.maintenance', [
                    'maintenanceTitle' => SystemSetting::getValue('maintenance_title', 'System Maintenance'),
                    'maintenanceMessage' => SystemSetting::getValue('maintenance_message', 'The UCC Event Reservation System is temporarily down for maintenance. Please check back shortly.'),
                ]);
            }

            return redirect()->route('dashboard');
        }

        if (SystemSetting::isDown()) {
            return view('system.maintenance', [
                'maintenanceTitle' => SystemSetting::getValue('maintenance_title', 'System Maintenance'),
                'maintenanceMessage' => SystemSetting::getValue('maintenance_message', 'The UCC Event Reservation System is temporarily down for maintenance. Please check back shortly.'),
            ]);
        }

        return view('system.home');
    }

    public function maintenance()
    {
        return view('system.maintenance', [
            'maintenanceTitle' => SystemSetting::getValue('maintenance_title', 'System Maintenance'),
            'maintenanceMessage' => SystemSetting::getValue('maintenance_message', 'The UCC Event Reservation System is temporarily down for maintenance. Please check back shortly.'),
        ]);
    }
}
