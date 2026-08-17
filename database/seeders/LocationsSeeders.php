<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Department;
use App\Models\Room;

class LocationsSeeders extends Seeder
{
    public function run(): void
    {
        // Clear existing
        Building::truncate();
        
        // Create Buildings
        $b1 = Building::create(['nama_gedung' => 'Gedung A', 'kode_gedung' => 'A']);
        $b2 = Building::create(['nama_gedung' => 'Gedung B', 'kode_gedung' => 'B']);
        $b3 = Building::create(['nama_gedung' => 'Gedung C', 'kode_gedung' => 'C']);
        
        // Create Departments with building_id
        Department::create(['building_id' => $b1->id, 'nama_jurusan' => 'Teknik Elektro', 'kode_jurusan' => 'TE']);
        Department::create(['building_id' => $b1->id, 'nama_jurusan' => 'Teknik Mesin', 'kode_jurusan' => 'TM']);
        Department::create(['building_id' => $b2->id, 'nama_jurusan' => 'Teknik Informatika', 'kode_jurusan' => 'TI']);
        Department::create(['building_id' => $b3->id, 'nama_jurusan' => 'Teknik Industri', 'kode_jurusan' => 'TI']);
        
        // Create Rooms with department_id
        Room::create(['department_id' => 1, 'nama_ruangan' => 'EL 1', 'kode_ruangan' => 'EL01']);
        Room::create(['department_id' => 1, 'nama_ruangan' => 'EL 2', 'kode_ruangan' => 'EL02']);
        Room::create(['department_id' => 2, 'nama_ruangan' => 'ME 1', 'kode_ruangan' => 'ME01']);
        Room::create(['department_id' => 3, 'nama_ruangan' => 'IN 1', 'kode_ruangan' => 'IN01']);
        Room::create(['department_id' => 4, 'nama_ruangan' => 'ID 1', 'kode_ruangan' => 'ID01']);
        
        echo "✅ Locations seeded successfully!" . PHP_EOL;
    }
}
