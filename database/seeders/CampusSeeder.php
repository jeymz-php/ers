<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campus;

class CampusSeeder extends Seeder
{
    public function run()
    {
        $campuses = [
            ['name' => 'Main Campus', 'code' => 'MC', 'display_order' => 1],
            ['name' => 'Congressional Extension Campus', 'code' => 'CEC', 'display_order' => 2],
            ['name' => 'Camarin Extension Campus', 'code' => 'CAM', 'display_order' => 3],
            ['name' => 'Bagong Silang Campus', 'code' => 'BS', 'display_order' => 4],
        ];

        foreach ($campuses as $campus) {
            Campus::updateOrCreate(
                ['code' => $campus['code']],
                array_merge($campus, [
                    'is_active' => true,
                    'address' => 'Caloocan City'
                ])
            );
        }
    }
}