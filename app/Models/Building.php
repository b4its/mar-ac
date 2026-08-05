<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $fillable = ['nama_gedung', 'kode_gedung', 'keterangan'];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
