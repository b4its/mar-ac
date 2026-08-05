<?php

use App\Enums\DamageReportStatus;
use App\Enums\RepairStatus;
use App\Filament\Resources\DamageReports\Pages\ViewDamageReport;
use App\Filament\Resources\RepairReports\Pages\ViewRepairReport;
use App\Filament\Widgets\RepairPerluVerifikasi;
use App\Models\Asset;
use App\Models\Building;
use App\Models\DamageReport;
use App\Models\Department;
use App\Models\MaintenanceReport;
use App\Models\RepairReport;
use App\Models\Room;
use App\Models\User;
use App\Models\Vendor;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeImage(string $name, int $size = 128): UploadedFile
{
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
    $name = str_ends_with($name, '.png') ? $name : $name.'.png';

    return UploadedFile::fake()->createWithContent($name, str_pad($png, $size, '0'), 'image/png');
}

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
    $asset = Asset::create([
        'nama_alat' => 'AC Split 2 PK',
        'jenis_alat' => 'Pendingin Ruangan',
        'kode_alat' => 'AC-001',
        'no_inventaris' => 'INV-2024-0001',
        'room_id' => $room->id,
        'department_id' => $department->id,
        'status' => 'baik',
    ]);
    $this->vendor = Vendor::create([
        'nama_vendor' => 'PT AC Sejahtera',
        'kontak' => 'Budi Santoso',
        'telepon' => '0812-3456-7890',
        'alamat' => 'Jl. Pahlawan No. 12, Samarinda',
    ]);

    $this->damage = DamageReport::create([
        'nomor_laporan' => '001/UPA.PP/KSR/2026',
        'asset_id' => $asset->id,
        'pelapor_user_id' => $teknisi->id,
        'tingkat_kerusakan' => 'sedang',
        'jenis_kerusakan' => 'Kompresor mati',
        'uraian_kerusakan' => 'AC tidak dingin',
        'tanggal_laporan' => now(),
        'status' => DamageReportStatus::Dilaporkan->value,
    ]);
});

it('laporan kerusakan menyimpan beberapa foto lampiran', function () {
    $this->actingAs($this->teknisi);

    $response = $this->post(route('laporan.kerusakan.store'), [
        'asset_id' => $this->damage->asset_id,
        'tingkat_kerusakan' => 'sedang',
        'jenis_kerusakan' => 'Kabel putus',
        'photos' => [
            makeImage('foto1'),
            makeImage('foto2'),
            makeImage('foto3'),
        ],
        'photos_captions' => [
            'Kondisi kabel sebelum perbaikan',
            null,
            'Panel kontrol rusak',
        ],
    ]);

    $response->assertRedirect();

    $report = DamageReport::latest('id')->first();
    expect($report->attachments)->toHaveCount(3)
        ->and($report->attachments[0]->slot_key)->toBe('photo_1')
        ->and($report->attachments[0]->caption)->toBe('Kondisi kabel sebelum perbaikan')
        ->and($report->attachments[1]->caption)->toBe('Foto Kerusakan 2')
        ->and($report->attachments[2]->slot_key)->toBe('photo_3')
        ->and($report->attachments[2]->caption)->toBe('Panel kontrol rusak')
        ->and($report->attachments->every(fn ($a) => str_starts_with($a->file_path, 'media/kerusakan/')))->toBeTrue()
        ->and($report->attachments->every(fn ($a) => $a->category === 'damage_evidence'))->toBeTrue();
});

it('laporan kerusakan menyimpan data formulir cetak tambahan', function () {
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.kerusakan.store'), [
        'asset_id' => $this->damage->asset_id,
        'tingkat_kerusakan' => 'berat',
        'jenis_kerusakan' => 'Kondensasi berlebih',
        'print_fields' => [
            'tanggal_revisi' => '24 Januari 2024',
            'tanggal_berlaku' => '24 Januari 2024',
            'kode_dokumen' => 'FM-Polnes-11-02-03/R3',
            'nomor_laporan' => 'KSR-CUSTOM-001',
            'nama_alat' => 'AC Central Daikin VRV 18 PK',
            'lokasi_alat' => 'Gedung Maritim Lantai 1',
            'nama_ruangan' => 'Marine Lantai 1',
            'gedung' => 'Maritim',
            'jurusan' => 'Teknik Kemaritiman',
            'jurusan_unit' => 'Kemaritiman',
            'teknisi_nama' => 'Teknisi UPA.PP',
        ],
    ])->assertRedirect();

    $report = DamageReport::latest('id')->first();

    expect($report->print_fields)
        ->tanggal_revisi->toBe('24 Januari 2024')
        ->tanggal_berlaku->toBe('24 Januari 2024')
        ->kode_dokumen->toBe('FM-Polnes-11-02-03/R3')
        ->nomor_laporan->toBe('KSR-CUSTOM-001')
        ->nama_alat->toBe('AC Central Daikin VRV 18 PK')
        ->lokasi_alat->toBe('Gedung Maritim Lantai 1')
        ->nama_ruangan->toBe('Marine Lantai 1')
        ->gedung->toBe('Maritim')
        ->jurusan->toBe('Teknik Kemaritiman')
        ->jurusan_unit->toBe('Kemaritiman')
        ->teknisi_nama->toBe('Teknisi UPA.PP');
});

it('laporan kerusakan menolak foto lampiran non-gambar atau terlalu besar', function () {
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.kerusakan.store'), [
        'asset_id' => $this->damage->asset_id,
        'tingkat_kerusakan' => 'sedang',
        'jenis_kerusakan' => 'Kabel putus',
        'photos' => [
            UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
        ],
    ])->assertSessionHasErrors('photos.0');

    $this->post(route('laporan.kerusakan.store'), [
        'asset_id' => $this->damage->asset_id,
        'tingkat_kerusakan' => 'sedang',
        'jenis_kerusakan' => 'Kabel putus',
        'photos' => [
            makeImage('besar', 6000 * 1024),
        ],
    ])->assertSessionHasErrors('photos.0');

    expect(DamageReport::count())->toBe(1);
});

it('halaman status menampilkan lampiran foto laporan kerusakan', function () {
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.kerusakan.store'), [
        'asset_id' => $this->damage->asset_id,
        'tingkat_kerusakan' => 'sedang',
        'jenis_kerusakan' => 'Kabel putus',
        'photos' => [makeImage('foto1'), makeImage('foto2')],
    ]);

    $report = DamageReport::latest('id')->first();

    $this->get(route('laporan.status', ['nomor' => $report->nomor_laporan]))
        ->assertOk()
        ->assertSee('Lampiran Foto')
        ->assertSee('Foto Kerusakan 1')
        ->assertSee('Foto Kerusakan 2');
});

it('admin dapat menyetujui laporan kerusakan', function () {
    $this->actingAs($this->admin);

    Livewire::test(ViewDamageReport::class, ['record' => $this->damage->id])
        ->callAction('approve', ['hasil' => DamageReportStatus::Disetujui->value, 'catatan' => 'ok']);

    expect($this->damage->refresh()->status)->toBe(DamageReportStatus::Disetujui->value);
});

it('teknisi dapat mengirim laporan hasil perbaikan beserta lampiran foto', function () {
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perbaikan.store', $this->damage), [
        'vendor_id' => $this->vendor->id,
        'jenis_pekerjaan' => 'Penggantian kompresor',
        'uraian_pekerjaan' => 'Kompresor diganti dan diuji',
        'tanggal_pelaksanaan' => now()->toDateString(),
        'biaya' => 150000,
        'biaya_jasa' => 50000,
        'lampiran' => [
            [
                'file' => makeImage('X'),
                'caption' => 'Kondisi sebelum perbaikan',
            ],
            [
                'file' => makeImage('X'),
                'caption' => 'Setelah perbaikan',
            ],
        ],
    ])->assertRedirect();

    $repair = $this->damage->refresh()->repairReport;

    expect($repair)->not->toBeNull()
        ->and($repair->nomor_laporan)->toContain('PRB')
        ->and($repair->status)->toBe(RepairStatus::Diajukan->value)
        ->and($repair->attachments()->count())->toBe(2)
        ->and($repair->attachments()->first()->caption)->toBe('Kondisi sebelum perbaikan');
});

it('repair hanya dapat dikirim oleh pelapor laporan kerusakan', function () {
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);
    $other = User::factory()->create(['name' => 'Teknisi Lain']);
    $other->assignRole('teknisi');
    $this->actingAs($other);

    $this->get(route('laporan.perbaikan', $this->damage))->assertForbidden();
});

it('repair tidak dapat dibuat dari laporan kerusakan yang belum disetujui', function () {
    $this->actingAs($this->teknisi);

    $this->get(route('laporan.perbaikan', $this->damage))->assertForbidden();
});

it('repair yang sudah diajukan tidak dapat dikirim ulang', function () {
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);
    RepairReport::create([
        'nomor_laporan' => '999/UPA.PP/PRB/2026',
        'damage_report_id' => $this->damage->id,
        'asset_id' => $this->damage->asset_id,
        'pelapor_user_id' => $this->teknisi->id,
        'teknisi_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian',
        'status' => RepairStatus::Diajukan->value,
    ]);
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perbaikan.store', $this->damage), [
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian',
        'lampiran' => [
            ['file' => makeImage('X'), 'caption' => 'Foto'],
        ],
    ])->assertSessionHasErrors('repair');
});

it('admin verifikasi menyetujui perbaikan dan menutup laporan kerusakan', function () {
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);
    $repair = RepairReport::create([
        'nomor_laporan' => '999/UPA.PP/PRB/2026',
        'damage_report_id' => $this->damage->id,
        'asset_id' => $this->damage->asset_id,
        'pelapor_user_id' => $this->teknisi->id,
        'teknisi_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian',
        'status' => RepairStatus::Diajukan->value,
    ]);
    $this->actingAs($this->admin);

    Livewire::test(ViewRepairReport::class, ['record' => $repair->id])
        ->callAction('verifikasi-repair', ['hasil' => RepairStatus::Disetujui->value, 'catatan' => 'Sesuai']);

    expect($repair->refresh()->status)->toBe(RepairStatus::Disetujui->value)
        ->and($repair->verifikator_user_id)->toBe($this->admin->id)
        ->and($this->damage->refresh()->status)->toBe(DamageReportStatus::Selesai->value);
});

it('admin verifikasi revisi mengembalikan repair ke teknisi', function () {
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);
    $repair = RepairReport::create([
        'nomor_laporan' => '999/UPA.PP/PRB/2026',
        'damage_report_id' => $this->damage->id,
        'asset_id' => $this->damage->asset_id,
        'pelapor_user_id' => $this->teknisi->id,
        'teknisi_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian',
        'status' => RepairStatus::Diajukan->value,
    ]);
    $this->actingAs($this->admin);

    Livewire::test(ViewRepairReport::class, ['record' => $repair->id])
        ->callAction('verifikasi-repair', ['hasil' => RepairStatus::Revisi->value, 'catatan' => 'Foto kurang']);

    expect($repair->refresh()->status)->toBe(RepairStatus::Revisi->value)
        ->and($this->damage->refresh()->status)->toBe(DamageReportStatus::Disetujui->value);
});

it('teknisi dapat mengirim ulang repair yang direvisi', function () {
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);
    $repair = RepairReport::create([
        'nomor_laporan' => '999/UPA.PP/PRB/2026',
        'damage_report_id' => $this->damage->id,
        'asset_id' => $this->damage->asset_id,
        'pelapor_user_id' => $this->teknisi->id,
        'teknisi_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian',
        'status' => RepairStatus::Revisi->value,
    ]);
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perbaikan.store', $this->damage), [
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian revisi',
        'lampiran' => [
            ['file' => makeImage('X'), 'caption' => 'Foto baru'],
        ],
    ])->assertRedirect();

    expect($repair->refresh()->status)->toBe(RepairStatus::Diajukan->value)
        ->and($repair->attachments()->count())->toBe(1);
});

it('laporan perawatan wajib mengunggah tiga foto sesuai slot', function () {
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perawatan.store'), [
        'asset_id' => $this->damage->asset_id,
        'jenis_pekerjaan' => 'Cleaning Indoor & Outdoor AC',
        'uraian_pekerjaan' => 'Pencucian filter',
        'tanggal_pelaksanaan' => now()->toDateString(),
        'biaya' => '1.500.000',
        'biaya_jasa' => '250.000',
    ])->assertSessionHasErrors(['foto_indoor', 'foto_outdoor', 'foto_kartu']);
});

it('laporan perawatan menyimpan tiga lampiran dengan slot_key', function () {
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perawatan.store'), [
        'asset_id' => $this->damage->asset_id,
        'jenis_pekerjaan' => 'Cleaning Indoor & Outdoor AC',
        'uraian_pekerjaan' => 'Pencucian filter',
        'tanggal_pelaksanaan' => now()->toDateString(),
        'biaya' => '1.500.000',
        'biaya_jasa' => '250.000',
        'foto_indoor' => makeImage('X'),
        'foto_outdoor' => makeImage('X'),
        'foto_kartu' => makeImage('X'),
    ])->assertRedirect();

    $maintenance = MaintenanceReport::latest('id')->first();

    expect($maintenance->attachments()->count())->toBe(3)
        ->and($maintenance->attachments()->where('slot_key', 'indoor_cleaning')->exists())->toBeTrue()
        ->and($maintenance->attachments()->where('slot_key', 'outdoor_cleaning')->exists())->toBeTrue()
        ->and($maintenance->attachments()->where('slot_key', 'maintenance_card')->exists())->toBeTrue()
        ->and((int) $maintenance->biaya)->toBe(1500000)
        ->and((int) $maintenance->biaya_jasa)->toBe(250000);
});

it('laporan perawatan menyimpan data formulir cetak tambahan', function () {
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perawatan.store'), [
        'asset_id' => $this->damage->asset_id,
        'jenis_pekerjaan' => 'Cleaning Indoor & Outdoor AC',
        'uraian_pekerjaan' => 'Pencucian filter',
        'tanggal_pelaksanaan' => now()->toDateString(),
        'biaya' => 250000,
        'biaya_jasa' => 250000,
        'print_fields' => [
            'tanggal_berlaku' => '24 Januari 2024',
            'kode_dokumen' => 'FM-Polnes-11-12-11/R3',
            'nomor_laporan' => 'PRW-CUSTOM-001',
            'nama_alat' => 'AC 2 PK VRV DAIKIN',
            'lokasi_alat' => 'R. KPNK B2 206',
            'nama_ruangan' => 'KPNK B2 206',
            'gedung' => 'Kemaritiman',
            'kode_alat' => 'MT',
            'jurusan' => 'Teknik Kemaritiman',
            'jurusan_unit' => 'Kemaritiman',
            'pelaksana_nama' => 'Vendor',
        ],
        'foto_indoor' => makeImage('indoor'),
        'foto_outdoor' => makeImage('outdoor'),
        'foto_kartu' => makeImage('kartu'),
    ])->assertRedirect();

    $maintenance = MaintenanceReport::latest('id')->first();

    expect($maintenance->print_fields)
        ->tanggal_berlaku->toBe('24 Januari 2024')
        ->kode_dokumen->toBe('FM-Polnes-11-12-11/R3')
        ->nomor_laporan->toBe('PRW-CUSTOM-001')
        ->nama_alat->toBe('AC 2 PK VRV DAIKIN')
        ->lokasi_alat->toBe('R. KPNK B2 206')
        ->nama_ruangan->toBe('KPNK B2 206')
        ->gedung->toBe('Kemaritiman')
        ->kode_alat->toBe('MT')
        ->jurusan->toBe('Teknik Kemaritiman')
        ->jurusan_unit->toBe('Kemaritiman')
        ->pelaksana_nama->toBe('Vendor');
});

it('teknisi tidak dapat membuat laporan perbaikan via halaman admin', function () {
    expect($this->teknisi->can('create', RepairReport::class))->toBeFalse();
});

it('approval mencatat admin yang menyetujui laporan kerusakan', function () {
    $this->actingAs($this->admin);

    Livewire::test(ViewDamageReport::class, ['record' => $this->damage->id])
        ->callAction('approve', ['hasil' => DamageReportStatus::Disetujui->value, 'catatan' => 'ok']);

    expect($this->damage->refresh()->approved_by_user_id)->toBe($this->admin->id);
});

it('status laporan hanya dapat dilihat oleh pemilik atau admin', function () {
    $other = User::factory()->create(['name' => 'Teknisi Lain']);
    $other->assignRole('teknisi');
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);

    $this->actingAs($this->teknisi);
    $this->get(route('laporan.status', ['nomor' => $this->damage->nomor_laporan]))
        ->assertOk()
        ->assertSee($this->damage->jenis_kerusakan);

    $this->actingAs($other);
    $this->get(route('laporan.status', ['nomor' => $this->damage->nomor_laporan]))
        ->assertOk()
        ->assertDontSee($this->damage->jenis_kerusakan);
});

it('widget dashboard menampilkan perbaikan yang menunggu verifikasi', function () {
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);

    $this->actingAs($this->teknisi);
    $this->post(route('laporan.perbaikan.store', $this->damage), [
        'vendor_id' => $this->vendor->id,
        'jenis_pekerjaan' => 'Perbaikan kompresor',
        'uraian_pekerjaan' => 'Ganti kompresor',
        'tanggal_pelaksanaan' => now()->toDateString(),
        'biaya' => 1500000,
        'lampiran' => [
            ['file' => makeImage('foto-1'), 'caption' => 'Sebelum'],
        ],
    ])->assertRedirect();

    $this->actingAs($this->admin);
    Livewire::test(RepairPerluVerifikasi::class)
        ->assertSee('Perbaikan kompresor')
        ->assertSee('001/UPA.PP/PRB/2026');
});

it('teknisi dapat membuka preview dan mengunduh PDF laporan kerusakan miliknya', function () {
    $this->actingAs($this->teknisi);

    expect($this->damage->pelapor_user_id)->toBe($this->teknisi->id)
        ->and(auth()->id())->toBe($this->teknisi->id);

    $this->get(route('laporan.pdf.kerusakan', $this->damage))
        ->assertOk()
        ->assertSee('Preview PDF Laporan Kerusakan')
        ->assertSee('Download PDF')
        ->assertSee('Print');

    $this->get(route('laporan.pdf.kerusakan.file', [$this->damage, 'download' => 1]))
        ->assertOk()
        ->assertDownload();
});

it('teknisi tidak dapat mengunduh PDF laporan kerusakan milik orang lain', function () {
    $other = User::factory()->create(['name' => 'Teknisi Lain']);
    $other->assignRole('teknisi');
    $this->actingAs($other);

    $this->get(route('laporan.pdf.kerusakan', $this->damage))->assertForbidden();
});

it('tombol Cetak PDF tampil di halaman view admin', function () {
    $this->actingAs($this->admin);

    Livewire::test(ViewDamageReport::class, ['record' => $this->damage->id])
        ->assertActionExists('cetak-pdf')
        ->callAction('cetak-pdf')
        ->assertHasNoActionErrors();
});

it('halaman perbaikan dialihkan jika repair sudah dikirim', function () {
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);
    RepairReport::create([
        'nomor_laporan' => '888/UPA.PP/PRB/2026',
        'damage_report_id' => $this->damage->id,
        'asset_id' => $this->damage->asset_id,
        'pelapor_user_id' => $this->teknisi->id,
        'teknisi_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian',
        'status' => RepairStatus::Diajukan->value,
    ]);
    $this->actingAs($this->teknisi);

    $this->get(route('laporan.perbaikan', $this->damage))
        ->assertRedirect();
});

it('lampiran perbaikan menolak file non-gambar', function () {
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perbaikan.store', $this->damage), [
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian',
        'lampiran' => [
            ['file' => UploadedFile::fake()->create('dokumen.txt', 10), 'caption' => 'Bukan gambar'],
        ],
    ])->assertSessionHasErrors('lampiran.0.file');
});

it('lampiran perbaikan menolak file lebih dari 5 MB', function () {
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perbaikan.store', $this->damage), [
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian',
        'lampiran' => [
            ['file' => makeImage('besar', 6 * 1024 * 1024), 'caption' => 'Terlalu besar'],
        ],
    ])->assertSessionHasErrors('lampiran.0.file');
});

it('satu laporan kerusakan hanya dapat memiliki satu perbaikan', function () {
    RepairReport::create([
        'nomor_laporan' => '777/UPA.PP/PRB/2026',
        'damage_report_id' => $this->damage->id,
        'asset_id' => $this->damage->asset_id,
        'pelapor_user_id' => $this->teknisi->id,
        'teknisi_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian',
        'status' => RepairStatus::Diajukan->value,
    ]);

    expect(fn () => RepairReport::create([
        'nomor_laporan' => '776/UPA.PP/PRB/2026',
        'damage_report_id' => $this->damage->id,
        'asset_id' => $this->damage->asset_id,
        'pelapor_user_id' => $this->teknisi->id,
        'teknisi_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian',
        'status' => RepairStatus::Diajukan->value,
    ]))->toThrow(QueryException::class);
});

it('revisi mengganti lampiran dan menghapus file lama', function () {
    $this->damage->update(['status' => DamageReportStatus::Disetujui->value]);
    $repair = RepairReport::create([
        'nomor_laporan' => '666/UPA.PP/PRB/2026',
        'damage_report_id' => $this->damage->id,
        'asset_id' => $this->damage->asset_id,
        'pelapor_user_id' => $this->teknisi->id,
        'teknisi_user_id' => $this->teknisi->id,
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian',
        'status' => RepairStatus::Revisi->value,
    ]);
    $oldPath = 'reports/repairs/'.$repair->id.'/foto-lama.png';
    Storage::disk('public')->put($oldPath, 'old-content');
    $repair->attachments()->create([
        'category' => 'repair_evidence',
        'caption' => 'Foto lama',
        'file_path' => $oldPath,
        'original_name' => 'foto-lama.png',
        'mime_type' => 'image/png',
        'file_size' => 11,
        'sort_order' => 0,
        'uploaded_by_user_id' => $this->teknisi->id,
    ]);
    $this->actingAs($this->teknisi);

    $this->post(route('laporan.perbaikan.store', $this->damage), [
        'jenis_pekerjaan' => 'Perbaikan',
        'uraian_pekerjaan' => 'Uraian revisi',
        'lampiran' => [
            ['file' => makeImage('foto-baru'), 'caption' => 'Foto baru'],
        ],
    ])->assertRedirect();

    expect($repair->refresh()->attachments()->count())->toBe(1)
        ->and(Storage::disk('public')->exists($oldPath))->toBeFalse();
});
