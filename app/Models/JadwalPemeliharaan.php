<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPemeliharaan extends Model
{
    protected $table = 'jadwal_pemeliharaan';

    protected $fillable = [
        'asset_id',
        'tanggal_jadwal',
        'jenis_pekerjaan',
        'catatan',
        'status',
        'created_by_user_id',
        'selesai_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_jadwal' => 'date',
            'selesai_at' => 'datetime',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
