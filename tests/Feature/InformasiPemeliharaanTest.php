<?php

use App\Enums\DamageReportStatus;
use App\Enums\RepairStatus;
use App\Filament\Resources\DamageReports\Pages\ListDamageReports;
use App\Filament\Resources\MaintenanceReports\Pages\ViewMaintenanceReport;
use App\Filament\Widgets\JadwalTerdekat;
use App\Filament\Widgets\KondisiAset;
use App\Filament\Widgets\TrenLaporan;
use App\Models\Asset;
use App\Models\Building;
use App\Models\DamageReport;
use App\Models\Department;
use App\Models\JadwalPemeliharaan;
use App\Models\MaintenanceReport;
use App\Models\RepairReport;
use App\Models\Room;
use App\Models\User;
use App\Services\LaporanCsv;
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

it('laporanRiwayat menggabungkan dan mengurutkan riwayat aset', function () {
    DamageReport::create([
        'nomor_laporan' => 'TEST/UPA.PP/KSR/2026',
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $this->teknisi->id,
        'tingkat_kerusakan' => 'sedang',
        'jenis_kerusakan' => 'Kompresor mati',
        'uraian_kerusakan' => 'AC tidak dingin.',
        'tanggal_laporan' => now()->subDays(5),
        'status' => DamageReportStatus::Disetujui->value,
    ]);

    MaintenanceReport::create([
        'nomor_laporan' => 'TEST/UPA.PP/PRW/2026',
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Pencucian AC',
        'uraian_pekerjaan' => 'Pencucian rutin.',
        'tanggal_pelaksanaan' => now()->subDay(),
        'biaya' => 100000,
        'status' => 'diajukan',
    ]);

    $this->assertDatabaseCount('maintenance_reports', 1);

    $riwayat = $this->asset->laporanRiwayat();

    expect($riwayat)->toHaveCount(2);
    expect($riwayat->pluck('jenis')->all())->toBe(['perawatan', 'kerusakan']);
    expect($riwayat->pluck('jenisLabel')->all())->toBe(['Kartu Perawatan', 'Laporan Kerusakan']);
    expect($riwayat->pluck('statusLabel')->all())->toBe(['Diajukan', 'Disetujui']);
});

it('riwayat aset menyertakan laporan perbaikan yang terhubung', function () {
    $damage = DamageReport::create([
        'nomor_laporan' => 'TEST/UPA.PP/KSR/2026',
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $this->teknisi->id,
        'tingkat_kerusakan' => 'berat',
        'jenis_kerusakan' => 'Kompresor mati',
        'uraian_kerusakan' => 'AC tidak dingin.',
        'tanggal_laporan' => now()->subDays(5),
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
        'tanggal_pelaksanaan' => now()->subDay(),
        'biaya' => 1000000,
        'status' => RepairStatus::Diajukan->value,
    ]);

    expect($this->asset->laporanRiwayat())
        ->toHaveCount(2)
        ->and($this->asset->laporanRiwayat()->pluck('jenis')->all())->toBe(['perbaikan', 'kerusakan']);
});

it('halaman view aset menampilkan riwayat untuk admin', function () {
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

    $this->actingAs($this->admin)
        ->get(route('filament.admin.resources.assets.view', $this->asset))
        ->assertOk()
        ->assertSee('Riwayat Penanganan Aset')
        ->assertSee('TEST/UPA.PP/KSR/2026')
        ->assertSee('Kompresor mati');
});

it('laporanCsv kerusakan menghasilkan csv dengan header dan data', function () {
    DamageReport::create([
        'nomor_laporan' => 'TEST/UPA.PP/KSR/2026',
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $this->teknisi->id,
        'tingkat_kerusakan' => 'sedang',
        'jenis_kerusakan' => 'Kompresor mati',
        'uraian_kerusakan' => 'AC tidak dingin.',
        'tanggal_laporan' => now(),
        'status' => DamageReportStatus::Disetujui->value,
    ]);

    $response = LaporanCsv::damage(DamageReport::with('asset.room.building', 'pelaporUser')->get());

    expect($response->headers->get('content-type'))->toContain('text/csv');

    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

    expect($content)
        ->toContain("\xEF\xBB\xBF")
        ->toContain('Nomor')
        ->toContain('TEST/UPA.PP/KSR/2026')
        ->toContain('Kompresor mati');
});

it('aksi ekspor csv pada list kerusakan berhasil tanpa error', function () {
    DamageReport::create([
        'nomor_laporan' => 'TEST/UPA.PP/KSR/2026',
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $this->teknisi->id,
        'tingkat_kerusakan' => 'sedang',
        'jenis_kerusakan' => 'Kompresor mati',
        'uraian_kerusakan' => 'AC tidak dingin.',
        'tanggal_laporan' => now(),
        'status' => DamageReportStatus::Dilaporkan->value,
    ]);

    $this->actingAs($this->admin);

    Livewire::test(ListDamageReports::class)
        ->callAction('ekspor-csv')
        ->assertHasNoActionErrors();
});

it('widget jadwal terdekat menampilkan jadwal bagi yang berhak', function () {
    JadwalPemeliharaan::create([
        'asset_id' => $this->asset->id,
        'tanggal_jadwal' => now()->addDays(3),
        'jenis_pekerjaan' => 'Pencucian AC',
        'status' => 'terjadwal',
        'created_by_user_id' => $this->admin->id,
    ]);

    $this->actingAs($this->teknisi);
    expect(JadwalTerdekat::canView())->toBeFalse();

    $this->actingAs($this->admin);
    expect(JadwalTerdekat::canView())->toBeTrue();

    Livewire::test(JadwalTerdekat::class)
        ->assertOk();
});

it('widget tren laporan tampil untuk admin dan menyediakan data', function () {
    $this->actingAs($this->teknisi);
    expect(TrenLaporan::canView())->toBeFalse();

    $this->actingAs($this->admin);
    expect(TrenLaporan::canView())->toBeTrue();

    Livewire::test(TrenLaporan::class)
        ->assertOk()
        ->assertHasNoErrors();
});

it('widget kondisi aset hanya tampil bagi yang berhak dan menyediakan data', function () {
    $this->actingAs($this->teknisi);
    expect(KondisiAset::canView())->toBeFalse();

    $this->actingAs($this->admin);
    expect(KondisiAset::canView())->toBeTrue();

    Livewire::test(KondisiAset::class)
        ->assertOk()
        ->assertHasNoErrors();
});

it('halaman registrasi aset dapat dicari dan menampilkan hasil', function () {
    $this->actingAs($this->teknisi)
        ->get('/aset')
        ->assertOk()
        ->assertSee('AC Split 2 PK');

    $this->actingAs($this->teknisi)
        ->get('/aset?q=AC-TEST')
        ->assertOk()
        ->assertSee('AC Split 2 PK')
        ->assertSee('Detail');

    $this->actingAs($this->teknisi)
        ->get('/aset?q=tidak-ada')
        ->assertOk()
        ->assertSee('Tidak ada aset ditemukan')
        ->assertDontSee('AC Split 2 PK');
});

it('halaman detail aset menampilkan riwayat dan jadwal mendatang', function () {
    $damage = DamageReport::create([
        'nomor_laporan' => 'TEST/UPA.PP/KSR/2026',
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $this->teknisi->id,
        'tingkat_kerusakan' => 'sedang',
        'jenis_kerusakan' => 'Kompresor mati',
        'uraian_kerusakan' => 'AC tidak dingin.',
        'tanggal_laporan' => now()->subDays(5),
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
        'tanggal_pelaksanaan' => now()->subDay(),
        'biaya' => 1000000,
        'status' => RepairStatus::Diajukan->value,
    ]);

    JadwalPemeliharaan::create([
        'asset_id' => $this->asset->id,
        'tanggal_jadwal' => now()->addDays(7),
        'jenis_pekerjaan' => 'Pencucian AC',
        'status' => 'terjadwal',
        'created_by_user_id' => $this->admin->id,
    ]);

    $this->actingAs($this->teknisi)
        ->get(route('aset.detail', $this->asset))
        ->assertOk()
        ->assertSee('AC Split 2 PK')
        ->assertSee('Riwayat Penanganan Aset')
        ->assertSee('TEST/UPA.PP/PRB/2026')
        ->assertSee('Jadwal Pemeliharaan Mendatang')
        ->assertSee('Pencucian AC');
});

it('persetujuan laporan perawatan memperbarui tanggal perawatan terakhir aset', function () {
    $verifikator = User::factory()->create(['name' => 'Verifikator']);
    $verifikator->assignRole('admin');

    $maintenance = MaintenanceReport::create([
        'nomor_laporan' => 'TEST/UPA.PP/PRW/2026',
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Pencucian AC',
        'uraian_pekerjaan' => 'Pencucian rutin.',
        'tanggal_pelaksanaan' => now()->subDay(),
        'biaya' => 100000,
        'status' => 'diverifikasi',
        'verifikator_user_id' => $verifikator->id,
        'verified_at' => now(),
    ]);

    $this->actingAs($this->admin);

    Livewire::test(ViewMaintenanceReport::class, ['record' => $maintenance->id])
        ->callAction('approve', ['hasil' => 'disetujui', 'catatan' => 'Sesuai']);

    expect($maintenance->refresh()->status)->toBe('disetujui');
    expect($this->asset->refresh()->last_maintenance_date?->toDateString())->toBe(now()->subDay()->toDateString());
});

it('permintaan revisi perawatan tidak mengubah tanggal perawatan aset', function () {
    $verifikator = User::factory()->create(['name' => 'Verifikator']);
    $verifikator->assignRole('admin');

    $maintenance = MaintenanceReport::create([
        'nomor_laporan' => 'TEST/UPA.PP/PRW/2026',
        'asset_id' => $this->asset->id,
        'pelapor_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Pencucian AC',
        'uraian_pekerjaan' => 'Pencucian rutin.',
        'tanggal_pelaksanaan' => now()->subDay(),
        'biaya' => 100000,
        'status' => 'diverifikasi',
        'verifikator_user_id' => $verifikator->id,
        'verified_at' => now(),
    ]);

    $this->actingAs($this->admin);

    Livewire::test(ViewMaintenanceReport::class, ['record' => $maintenance->id])
        ->callAction('approve', ['hasil' => 'revisi', 'catatan' => 'Foto kurang jelas']);

    expect($maintenance->refresh()->status)->toBe('revisi');
    expect($this->asset->refresh()->last_maintenance_date)->toBeNull();
});
