<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = ['building_id', 'nama_ruangan', 'kode_ruangan', 'keterangan'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
