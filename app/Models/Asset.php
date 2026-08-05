<?php

namespace App\Models;

use App\Enums\DamageReportStatus;
use App\Enums\RepairStatus;
use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Asset extends Model
{
    protected $fillable = [
        'nama_alat',
        'jenis_alat',
        'kode_alat',
        'no_inventaris',
        'room_id',
        'department_id',
        'kapasitas',
        'merk',
        'tahun_pemakaian',
        'status',
        'last_maintenance_date',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'last_maintenance_date' => 'date',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function maintenanceReports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class);
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class);
    }

    /**
     * Riwayat penanganan aset (kerusakan, perbaikan, perawatan) terurut kronologis.
     *
     * @return Collection<int, array{
     *     tanggal: Carbon,
     *     jenis: string,
     *     jenisLabel: string,
     *     nomor: string,
     *     detail: string,
     *     status: string,
     *     statusLabel: string,
     *     statusColor: string,
     *     link: array{name: string, params: int}|null
     * }>
     */
    public function laporanRiwayat(): Collection
    {
        $rows = collect();

        foreach ($this->damageReports()->with('repairReport')->get() as $damage) {
            $rows->push([
                'tanggal' => $damage->tanggal_laporan,
                'jenis' => 'kerusakan',
                'jenisLabel' => 'Laporan Kerusakan',
                'nomor' => $damage->nomor_laporan,
                'detail' => $damage->jenis_kerusakan,
                'status' => $damage->status,
                'statusLabel' => DamageReportStatus::from($damage->status)->label(),
                'statusColor' => DamageReportStatus::from($damage->status)->color(),
                'link' => ['name' => 'filament.admin.resources.damage-reports.view', 'params' => $damage->id],
            ]);

            if ($damage->repairReport) {
                $repair = $damage->repairReport;
                $rows->push([
                    'tanggal' => $repair->tanggal_pelaksanaan ?? $damage->tanggal_laporan,
                    'jenis' => 'perbaikan',
                    'jenisLabel' => 'Laporan Perbaikan',
                    'nomor' => $repair->nomor_laporan,
                    'detail' => $repair->jenis_pekerjaan,
                    'status' => $repair->status,
                    'statusLabel' => RepairStatus::from($repair->status)->label(),
                    'statusColor' => RepairStatus::from($repair->status)->color(),
                    'link' => ['name' => 'filament.admin.resources.repair-reports.view', 'params' => $repair->id],
                ]);
            }
        }

        foreach ($this->maintenanceReports as $maintenance) {
            $rows->push([
                'tanggal' => $maintenance->tanggal_pelaksanaan ?? $maintenance->created_at,
                'jenis' => 'perawatan',
                'jenisLabel' => 'Kartu Perawatan',
                'nomor' => $maintenance->nomor_laporan,
                'detail' => $maintenance->jenis_pekerjaan,
                'status' => $maintenance->status,
                'statusLabel' => ReportStatus::from($maintenance->status)->label(),
                'statusColor' => ReportStatus::from($maintenance->status)->color(),
                'link' => ['name' => 'filament.admin.resources.maintenance-reports.view', 'params' => $maintenance->id],
            ]);
        }

        return $rows->sortByDesc('tanggal')->values();
    }
}
