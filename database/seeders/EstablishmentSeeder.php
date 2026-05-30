<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Establishment;
use App\Models\Campus;

class EstablishmentSeeder extends Seeder
{
    public function run()
    {
        // Get campus IDs
        $mainCampus = Campus::where('code', 'MC')->first();
        $congressional = Campus::where('code', 'CEC')->first();
        $camarin = Campus::where('code', 'CAM')->first();
        $bagongSilang = Campus::where('code', 'BS')->first();

        // ========== MAIN CAMPUS ESTABLISHMENTS ==========
        $mainEstablishments = [
            ['name' => 'Social Hall', 'capacity' => 300, 'type' => 'Indoor'],
            ['name' => 'Covered Court', 'capacity' => 300, 'type' => 'Outdoor'],
            ['name' => 'CLEP Room', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => 'MOOT Court', 'capacity' => 300, 'type' => 'Outdoor'],
            ['name' => 'BIO Lab', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => 'Physics Lab', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => 'Chemistry Lab', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => '5th Floor Veranda', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => 'Speech Lab', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => 'Room 201', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => 'Student Activity Center', 'capacity' => 30, 'type' => 'Outdoor'],
            ['name' => '4th Floor Student Lounge', 'capacity' => 30, 'type' => 'Indoor'],
            ['name' => 'Computer Lab 1', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => 'Computer Lab 2', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => 'Computer Lab 3', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => 'Library AVR', 'capacity' => 30, 'type' => 'Indoor'],
            ['name' => '2nd Floor Lobby', 'capacity' => 10, 'type' => 'Outdoor'],
            ['name' => 'Room 203', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Room 204', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Lecture Room 302', 'capacity' => 150, 'type' => 'Indoor'],
            ['name' => 'Room 206', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'External Venue - SM Sangandaan', 'capacity' => 500, 'type' => 'Indoor'],
            ['name' => 'Room 104', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Room 106', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Room 209', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Room 101', 'capacity' => 30, 'type' => 'Indoor'],
            ['name' => 'Room 210', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Room 401', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Multimedia Room', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => 'Room 208', 'capacity' => 40, 'type' => 'Indoor'],
        ];

        // ========== CONGRESSIONAL EXTENSION CAMPUS ==========
        $congressionalEstablishments = [
            ['name' => 'Covered Court', 'capacity' => 300, 'type' => 'Outdoor'],
            ['name' => 'Social Hall', 'capacity' => 300, 'type' => 'Indoor'],
            ['name' => 'Speech Lab', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Room 403', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Parking Area', 'capacity' => 50, 'type' => 'Outdoor'],
            ['name' => 'Room 406', 'capacity' => 50, 'type' => 'Indoor'],
            ['name' => 'Room 301', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Room 302', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Room 303', 'capacity' => 40, 'type' => 'Indoor'],
            ['name' => 'Room 304', 'capacity' => 40, 'type' => 'Indoor'],
        ];

        // ========== CAMARIN EXTENSION CAMPUS ==========
        $camarinEstablishments = [
            ['name' => 'Covered Court', 'capacity' => 300, 'type' => 'Outdoor'],
            ['name' => 'Audio Visual Room', 'capacity' => 60, 'type' => 'Indoor'],
            ['name' => 'University Library', 'capacity' => 30, 'type' => 'Indoor'],
        ];

        // ========== BAGONG SILANG CAMPUS ==========
        $bagongSilangEstablishments = [
            ['name' => 'Multi-Purpose Hall', 'capacity' => 200, 'type' => 'Indoor'],
            ['name' => 'Covered Court', 'capacity' => 250, 'type' => 'Outdoor'],
        ];

        // Insert Main Campus
        foreach ($mainEstablishments as $est) {
            Establishment::create([
                'name' => $est['name'],
                'campus_id' => $mainCampus->id,
                'capacity' => $est['capacity'],
                'type' => $est['type'],
                'is_active' => true,
            ]);
        }

        // Insert Congressional
        foreach ($congressionalEstablishments as $est) {
            Establishment::create([
                'name' => $est['name'],
                'campus_id' => $congressional->id,
                'capacity' => $est['capacity'],
                'type' => $est['type'],
                'is_active' => true,
            ]);
        }

        // Insert Camarin
        foreach ($camarinEstablishments as $est) {
            Establishment::create([
                'name' => $est['name'],
                'campus_id' => $camarin->id,
                'capacity' => $est['capacity'],
                'type' => $est['type'],
                'is_active' => true,
            ]);
        }

        // Insert Bagong Silang
        foreach ($bagongSilangEstablishments as $est) {
            Establishment::create([
                'name' => $est['name'],
                'campus_id' => $bagongSilang->id,
                'capacity' => $est['capacity'],
                'type' => $est['type'],
                'is_active' => true,
            ]);
        }
    }
}