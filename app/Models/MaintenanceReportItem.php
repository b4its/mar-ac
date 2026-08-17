<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceReportItem extends Model
{
    /**
     * Bagian tambahan dari kartu pelaporan hasil perawatan.
     *
     * Bagian pertama (bagian = 1) disimpan pada kolom utama tabel
     * maintenance_reports, sedangkan bagian kedua (bagian = 2) disimpan
     * pada model ini agar masing-masing bagian menyimpan aset, pekerjaan,
     * dan biayanya sendiri secara terpisah namun tetap satu dokumen laporan.
     */
    protected $table = 'maintenance_report_items';

    protected $fillable = [
        'maintenance_report_id',
        'bagian',
        'asset_id',
        'jenis_pekerjaan',
        'uraian_pekerjaan',
        'tanggal_pelaksanaan',
        'biaya',
        'biaya_jasa',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'bagian' => 'integer',
            'tanggal_pelaksanaan' => 'date',
            'biaya' => 'decimal:2',
            'biaya_jasa' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(MaintenanceReport::class, 'maintenance_report_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
