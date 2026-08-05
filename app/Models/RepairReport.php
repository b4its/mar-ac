<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RepairReport extends Model
{
    protected $fillable = [
        'nomor_laporan',
        'damage_report_id',
        'asset_id',
        'vendor_id',
        'pelapor_user_id',
        'teknisi_user_id',
        'verifikator_user_id',
        'jenis_pekerjaan',
        'uraian_pekerjaan',
        'tanggal_pelaksanaan',
        'tanggal_selesai',
        'biaya',
        'biaya_jasa',
        'status',
        'verified_at',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pelaksanaan' => 'date',
            'tanggal_selesai' => 'date',
            'biaya' => 'decimal:2',
            'biaya_jasa' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function damageReport(): BelongsTo
    {
        return $this->belongsTo(DamageReport::class);
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

    public function teknisiUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_user_id');
    }

    public function verifikatorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_user_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(ReportAttachment::class, 'attachable')->orderBy('sort_order');
    }
}
