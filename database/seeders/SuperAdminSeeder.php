<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Campus;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Get first campus or create one
        $campus = Campus::first();
        
        User::updateOrCreate(
            ['email' => 'ers@ucc-caloocan.edu.ph'],
            [
                'name' => 'System Super Administrator',
                'email' => 'ers@ucc-caloocan.edu.ph',
                'password' => Hash::make('Admin@1234'),
                'phone_number' => '00000000000',
                'campus_id' => $campus ? $campus->id : null,
                'role' => 'super_admin',
                'account_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => 1,
                'is_password_generated' => false,
            ]
        );
    }
}