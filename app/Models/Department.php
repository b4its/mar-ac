<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['nama_jurusan', 'kode_jurusan', 'keterangan'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
