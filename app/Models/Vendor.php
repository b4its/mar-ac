<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $fillable = ['nama_vendor', 'kontak', 'telepon', 'alamat', 'keterangan'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function maintenanceReports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class);
    }
}
