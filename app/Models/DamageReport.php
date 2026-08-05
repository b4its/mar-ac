<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DamageReport extends Model
{
    protected $fillable = [
        'nomor_laporan',
        'asset_id',
        'pelapor_user_id',
        'tingkat_kerusakan',
        'jenis_kerusakan',
        'uraian_kerusakan',
        'tanggal_laporan',
        'status',
        'approved_at',
        'approved_by_user_id',
        'catatan',
        'print_fields',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_laporan' => 'date',
            'approved_at' => 'datetime',
            'print_fields' => 'array',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function pelaporUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_user_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function repairReports(): HasMany
    {
        return $this->hasMany(RepairReport::class);
    }

    public function repairReport(): HasOne
    {
        return $this->hasOne(RepairReport::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(ReportAttachment::class, 'attachable')->orderBy('sort_order');
    }
}
