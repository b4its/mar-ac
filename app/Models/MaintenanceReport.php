<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MaintenanceReport extends Model
{
    protected $fillable = [
        'nomor_laporan',
        'asset_id',
        'vendor_id',
        'pelapor_user_id',
        'verifikator_user_id',
        'approver_user_id',
        'jenis_pekerjaan',
        'uraian_pekerjaan',
        'tanggal_pelaksanaan',
        'biaya',
        'biaya_jasa',
        'status',
        'verified_at',
        'approved_at',
        'catatan',
        'print_fields',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pelaksanaan' => 'date',
            'biaya' => 'decimal:2',
            'biaya_jasa' => 'decimal:2',
            'verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'print_fields' => 'array',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function pelaporUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_user_id');
    }

    public function verifikatorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_user_id');
    }

    public function approverUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(ReportAttachment::class, 'attachable')->orderBy('sort_order');
    }

    /**
     * Bagian tambahan (section ke-2 dst) dari kartu laporan perawatan.
     * Bagian pertama disimpan pada kolom utama model ini.
     */
    public function items(): HasMany
    {
        return $this->hasMany(MaintenanceReportItem::class)->orderBy('sort_order');
    }
}
