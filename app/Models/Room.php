<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'department_id',
        'nama_ruangan', 
        'kode_ruangan', 
        'keterangan'
    ];

    public function building(): BelongsTo
    {
        // Get building through department
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
