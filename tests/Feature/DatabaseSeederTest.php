<?php

use App\Models\Asset;
use App\Models\Building;
use App\Models\DamageReport;
use App\Models\Department;
use App\Models\JadwalPemeliharaan;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceReportItem;
use App\Models\RepairReport;
use App\Models\ReportAttachment;
use App\Models\Room;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('seeder menghasilkan 100 record pada setiap tabel domain kecuali users', function () {
    expect(Building::count())->toBe(100)
        ->and(Room::count())->toBe(100)
        ->and(Department::count())->toBe(100)
        ->and(Vendor::count())->toBe(100)
        ->and(Asset::count())->toBeGreaterThanOrEqual(500)
        ->and(Asset::query()->distinct()->count('room_id'))->toBe(100)
        ->and(DamageReport::count())->toBe(100)
        ->and(RepairReport::count())->toBe(100)
        ->and(MaintenanceReport::count())->toBe(100)
        ->and(MaintenanceReportItem::count())->toBe(100)
        ->and(JadwalPemeliharaan::count())->toBe(100)
        ->and(ReportAttachment::count())->toBe(100)
        ->and(User::count())->toBe(2);
});

it('seeder menghasilkan data lokasi, aset, dan vendor yang beragam', function () {
    expect(Asset::query()->distinct()->count('merk'))->toBeGreaterThanOrEqual(5)
        ->and(Asset::query()->whereNull('room_id')->count())->toBe(1)
        ->and(Asset::query()->distinct()->count('status'))->toBeGreaterThanOrEqual(3)
        ->and(Asset::query()->whereNotNull('last_maintenance_date')->count())->toBeGreaterThanOrEqual(10)
        ->and(Asset::query()->whereNotNull('last_maintenance_date')->distinct()->count('last_maintenance_date'))->toBeGreaterThanOrEqual(8)
        ->and(Building::query()->distinct()->count('kode_gedung'))->toBe(100)
        ->and(Room::query()->distinct()->count('kode_ruangan'))->toBe(100)
        ->and(Department::query()->distinct()->count('kode_jurusan'))->toBe(100);
});

it('seeder menghasilkan laporan dengan status yang berbeda-beda', function () {
    expect(DamageReport::query()->distinct()->count('status'))->toBeGreaterThanOrEqual(3)
        ->and(RepairReport::query()->distinct()->count('status'))->toBeGreaterThanOrEqual(2)
        ->and(MaintenanceReport::query()->distinct()->count('status'))->toBeGreaterThanOrEqual(2)
        ->and(JadwalPemeliharaan::query()->distinct()->count('status'))->toBeGreaterThanOrEqual(2)
        ->and(DamageReport::query()->distinct()->count('nomor_laporan'))->toBe(100)
        ->and(RepairReport::query()->distinct()->count('nomor_laporan'))->toBe(100)
        ->and(MaintenanceReport::query()->distinct()->count('nomor_laporan'))->toBe(100);
});

it('seeder membuat laporan perawatan dua bagian lengkap dengan lampiran terpisah', function () {
    $report = MaintenanceReport::query()
        ->whereHas('attachments', fn ($query) => $query->where('slot_key', 'indoor_cleaning_2'))
        ->with(['items', 'attachments'])
        ->first();

    expect($report)->not->toBeNull()
        ->and($report->items->first()->bagian)->toBe(2)
        ->and($report->items->first()->asset_id)->not->toBeNull()
        ->and($report->attachments->pluck('slot_key'))->toContain('indoor_cleaning')
        ->and($report->attachments->pluck('slot_key'))->toContain('indoor_cleaning_2')
        ->and($report->attachments->pluck('slot_key'))->toContain('maintenance_card_2');
});

it('seeder membuat laporan dengan tanggal pelaksanaan yang berbeda-beda', function () {
    $dates = RepairReport::query()->pluck('tanggal_pelaksanaan')
        ->merge(MaintenanceReport::query()->pluck('tanggal_pelaksanaan'))
        ->map(fn ($date) => $date?->toDateString())
        ->filter()
        ->unique();

    expect($dates->count())->toBeGreaterThanOrEqual(3);
});
