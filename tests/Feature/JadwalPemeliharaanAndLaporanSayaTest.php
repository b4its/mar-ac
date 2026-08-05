<?php

use App\Enums\DamageReportStatus;
use App\Enums\RepairStatus;
use App\Filament\Resources\JadwalPemeliharaan\JadwalPemeliharaanResource;
use App\Filament\Resources\JadwalPemeliharaan\Pages\CreateJadwalPemeliharaan;
use App\Filament\Resources\JadwalPemeliharaan\Pages\ListJadwalPemeliharaan;
use App\Models\Asset;
use App\Models\Building;
use App\Models\DamageReport;
use App\Models\Department;
use App\Models\JadwalPemeliharaan;
use App\Models\MaintenanceReport;
use App\Models\RepairReport;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);

    $admin = User::factory()->create(['name' => 'Admin']);
    $admin->assignRole('admin');
    $teknisi = User::factory()->create(['name' => 'Teknisi']);
    $teknisi->assignRole('teknisi');

    $this->admin = $admin;
    $this->teknisi = $teknisi;

    $building = Building::create(['nama_gedung' => 'Gedung A', 'kode_gedung' => 'A']);
    $room = Room::create(['building_id' => $building->id, 'nama_ruangan' => 'Ruang Server', 'kode_ruangan' => 'A-2']);
    $department = Department::create(['nama_jurusan' => 'TI', 'kode_jurusan' => 'TI']);

    $this->asset = Asset::create([
        'nama_alat' => 'AC Split 2 PK',
        'jenis_alat' => 'Pendingin Ruangan',
        'kode_alat' => 'AC-TEST',
        'no_inventaris' => 'INV-TEST',
        'room_id' => $room->id,
        'department_id' => $department->id,
        'status' => 'baik',
    ]);
});

it('halaman laporan saya menampilkan semua laporan teknisi', function () {
    $damage = DamageReport::create([
        'nomor_laporan' => 'TEST/UPA.PP/KSR/2026',
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $this->teknisi->id,
        'tingkat_kerusakan' => 'sedang',
        'jenis_kerusakan' => 'Kompresor mati',
        'uraian_kerusakan' => 'AC tidak dingin.',
        'tanggal_laporan' => now(),
        'status' => DamageReportStatus::Disetujui->value,
    ]);

    RepairReport::create([
        'nomor_laporan' => 'TEST/UPA.PP/PRB/2026',
        'damage_report_id' => $damage->id,
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $this->teknisi->id,
        'teknisi_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Penggantian kompresor',
        'uraian_pekerjaan' => 'Kompresor diganti.',
        'tanggal_pelaksanaan' => now(),
        'biaya' => 1000000,
        'status' => RepairStatus::Diajukan->value,
    ]);

    MaintenanceReport::create([
        'nomor_laporan' => 'TEST/UPA.PP/PRW/2026',
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Pencucian AC',
        'uraian_pekerjaan' => 'Pencucian rutin.',
        'tanggal_pelaksanaan' => now(),
        'biaya' => 100000,
        'status' => 'diajukan',
    ]);

    $this->actingAs($this->teknisi)
        ->get('/laporan/saya')
        ->assertOk()
        ->assertSee('Kompresor mati')
        ->assertSee('Penggantian kompresor')
        ->assertSee('Pencucian AC')
        ->assertSee('TEST/UPA.PP/KSR/2026')
        ->assertSee('TEST/UPA.PP/PRB/2026')
        ->assertSee('TEST/UPA.PP/PRW/2026');
});

it('laporan saya hanya menampilkan milik pengguna sendiri', function () {
    $other = User::factory()->create();
    $other->assignRole('teknisi');

    DamageReport::create([
        'nomor_laporan' => 'MILIK/ORANG/LAIN/2026',
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $other->id,
        'tingkat_kerusakan' => 'ringan',
        'jenis_kerusakan' => 'Remote rusak',
        'uraian_kerusakan' => 'Remote tidak berfungsi.',
        'tanggal_laporan' => now(),
        'status' => DamageReportStatus::Dilaporkan->value,
    ]);

    $this->actingAs($this->teknisi)
        ->get('/laporan/saya')
        ->assertOk()
        ->assertDontSee('MILIK/ORANG/LAIN/2026');
});

it('halaman laporan saya wajib login', function () {
    $this->get('/laporan/saya')
        ->assertRedirect(route('login'));
});

it('admin dapat membuka resource jadwal pemeliharaan', function () {
    $this->actingAs($this->admin)
        ->get(JadwalPemeliharaanResource::getUrl('index'))
        ->assertOk()
        ->assertSee('Jadwal Pemeliharaan');
});

it('teknisi tidak dapat membuka resource jadwal pemeliharaan', function () {
    $this->actingAs($this->teknisi)
        ->get(JadwalPemeliharaanResource::getUrl('index'))
        ->assertForbidden();
});

it('admin dapat membuat jadwal pemeliharaan', function () {
    $this->actingAs($this->admin);

    Livewire::test(CreateJadwalPemeliharaan::class)
        ->fillForm([
            'asset_id' => $this->asset->id,
            'tanggal_jadwal' => '2026-08-20',
            'jenis_pekerjaan' => 'Pencucian AC Indoor & Outdoor',
            'catatan' => 'Rutin bulanan.',
            'status' => 'terjadwal',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('jadwal_pemeliharaan', [
        'asset_id' => $this->asset->id,
        'jenis_pekerjaan' => 'Pencucian AC Indoor & Outdoor',
        'status' => 'terjadwal',
        'created_by_user_id' => $this->admin->id,
    ]);
});

it('list jadwal menampilkan jadwal yang ada', function () {
    JadwalPemeliharaan::create([
        'asset_id' => $this->asset->id,
        'tanggal_jadwal' => now()->addDays(5),
        'jenis_pekerjaan' => 'Cek freon',
        'status' => 'terjadwal',
        'created_by_user_id' => $this->admin->id,
    ]);

    $this->actingAs($this->admin);

    Livewire::test(ListJadwalPemeliharaan::class)
        ->assertCanSeeTableRecords(JadwalPemeliharaan::all());
});
