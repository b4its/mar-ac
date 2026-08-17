<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Department;
use App\Models\Room;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Create Buildings
        $building1 = Building::create([
            'nama_gedung' => 'Gedung Teknik Elektro',
            'kode_gedung' => 'TE',
            'keterangan' => 'Gedung utama untuk Teknik Elektro'
        ]);
        
        $building2 = Building::create([
            'nama_gedung' => 'Gedung Teknik Informatika',
            'kode_gedung' => 'TI',
            'keterangan' => 'Gedung teknik informatika'
        ]);
        
        $building3 = Building::create([
            'nama_gedung' => 'Gedung Industri',
            'kode_gedung' => 'IND',
            'keterangan' => 'Gedung teknik industri'
        ]);
        
        // Create Departments (Jurusan)
        Department::create([
            'building_id' => $building1->id,
            'nama_jurusan' => 'Teknik Elektro',
            'kode_jurusan' => 'JE',
            'keterangan' => 'Program studi Teknik Elektro'
        ]);
        
        Department::create([
            'building_id' => $building1->id,
            'nama_jurusan' => 'Teknik Mesin',
            'kode_jurusan' => 'JM',
            'keterangan' => 'Program studi Teknik Mesin'
        ]);
        
        Department::create([
            'building_id' => $building2->id,
            'nama_jurusan' => 'Teknik Informatika',
            'kode_jurusan' => 'TI',
            'keterangan' => 'Program studi Teknik Informatika'
        ]);
        
        Department::create([
            'building_id' => $building3->id,
            'nama_jurusan' => 'Teknik Industri',
            'kode_jurusan' => 'JI',
            'keterangan' => 'Program studi Teknik Industri'
        ]);
        
        // Create Rooms (Ruangan)
        Room::create([
            'department_id' => $building1->departments()->where('nama_jurusan', 'Teknik Elektro')->first()->id,
            'nama_ruangan' => 'EL 1',
            'kode_ruangan' => 'EL01',
            'keterangan' => 'Laboratorium EL 1'
        ]);
        
        Room::create([
            'department_id' => $building1->departments()->where('nama_jurusan', 'Teknik Elektro')->first()->id,
            'nama_ruangan' => 'EL 2',
            'kode_ruangan' => 'EL02',
            'keterangan' => 'Kelas EL 2'
        ]);
        
        Room::create([
            'department_id' => $building1->departments()->where('nama_jurusan', 'Teknik Mesin')->first()->id,
            'nama_ruangan' => 'ME 1',
            'kode_ruangan' => 'ME01',
            'keterangan' => 'Laboratorium ME 1'
        ]);
        
        Room::create([
            'department_id' => $building2->departments()->where('nama_jurusan', 'Teknik Informatika')->first()->id,
            'nama_ruangan' => 'IN 1',
            'kode_ruangan' => 'IN01',
            'keterangan' => 'Lab Komputer TI'
        ]);
        
        Room::create([
            'department_id' => $building2->departments()->where('nama_jurusan', 'Teknik Informatika')->first()->id,
            'nama_ruangan' => 'IN 2',
            'kode_ruangan' => 'IN02',
            'keterangan' => 'Ruang Kelas IN 2'
        ]);
        
        Room::create([
            'department_id' => $building3->departments()->where('nama_jurusan', 'Teknik Industri')->first()->id,
            'nama_ruangan' => 'ID 1',
            'kode_ruangan' => 'ID01',
            'keterangan' => 'Laboratorium Industri'
        ]);
        
        $this->command->info('✅ Locations seeded successfully!');
    }
}
