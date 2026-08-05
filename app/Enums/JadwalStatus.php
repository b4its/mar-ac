<?php

namespace App\Enums;

enum JadwalStatus: string
{
    case Terjadwal = 'terjadwal';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Terjadwal => 'Terjadwal',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Terjadwal => 'warning',
            self::Selesai => 'success',
            self::Dibatalkan => 'gray',
        };
    }
}
