<?php

use App\Models\Asset;
use App\Models\Building;
use App\Models\DamageReport;
use App\Models\Department;
use App\Models\JadwalPemeliharaan;
use App\Models\MaintenanceReport;
use App\Models\RepairReport;
use App\Models\Room;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeder menghasilkan data lokasi, aset, dan vendor yang beragam', function () {
    $this->seed();

    expect(Building::count())->toBeGreaterThanOrEqual(3)
        ->and(Department::count())->toBeGreaterThanOrEqual(5)
        ->and(Room::count())->toBeGreaterThanOrEqual(10)
        ->and(Asset::count())->toBeGreaterThanOrEqual(14)
        ->and(Vendor::count())->toBeGreaterThanOrEqual(4)
        ->and(Asset::query()->distinct()->count('merk'))->toBeGreaterThanOrEqual(5)
        ->and(Asset::query()->whereNull('room_id')->count())->toBe(1)
        ->and(Asset::query()->distinct()->count('status'))->toBeGreaterThanOrEqual(3);
});

it('seeder menghasilkan laporan kerusakan, perbaikan, perawatan, dan jadwal dengan status berbeda', function () {
    $this->seed();

    expect(DamageReport::count())->toBeGreaterThanOrEqual(2)
        ->and(DamageReport::query()->distinct()->count('status'))->toBeGreaterThanOrEqual(2)
        ->and(RepairReport::count())->toBeGreaterThanOrEqual(1)
        ->and(RepairReport::query()->distinct()->count('status'))->toBeGreaterThanOrEqual(2)
        ->and(MaintenanceReport::count())->toBeGreaterThanOrEqual(2)
        ->and(MaintenanceReport::query()->distinct()->count('status'))->toBeGreaterThanOrEqual(2)
        ->and(JadwalPemeliharaan::count())->toBeGreaterThanOrEqual(2)
        ->and(JadwalPemeliharaan::query()->distinct()->count('status'))->toBeGreaterThanOrEqual(2);
});

it('seeder membuat laporan perawatan dua bagian lengkap dengan lampiran terpisah', function () {
    $this->seed();

    $report = MaintenanceReport::has('items')->with(['items', 'attachments'])->first();

    expect($report)->not->toBeNull()
        ->and($report->items->first()->bagian)->toBe(2)
        ->and($report->items->first()->asset_id)->not->toBeNull()
        ->and($report->attachments->pluck('slot_key'))->toContain('indoor_cleaning')
        ->and($report->attachments->pluck('slot_key'))->toContain('indoor_cleaning_2')
        ->and($report->attachments->pluck('slot_key'))->toContain('maintenance_card_2');
});

it('seeder membuat laporan dengan tanggal pelaksanaan yang berbeda-beda', function () {
    $this->seed();

    $dates = RepairReport::query()->pluck('tanggal_pelaksanaan')
        ->merge(MaintenanceReport::query()->pluck('tanggal_pelaksanaan'))
        ->map(fn ($date) => $date?->toDateString())
        ->filter()
        ->unique();

    expect($dates->count())->toBeGreaterThanOrEqual(3);
});