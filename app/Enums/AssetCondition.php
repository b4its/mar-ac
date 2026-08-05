<?php

namespace App\Enums;

enum AssetCondition: string
{
    case Baik = 'baik';
    case RusakRingan = 'rusak_ringan';
    case RusakSedang = 'rusak_sedang';
    case RusakBerat = 'rusak_berat';

    public function label(): string
    {
        return match ($this) {
            self::Baik => 'Baik',
            self::RusakRingan => 'Rusak Ringan',
            self::RusakSedang => 'Rusak Sedang',
            self::RusakBerat => 'Rusak Berat',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Baik => 'green',
            self::RusakRingan => 'yellow',
            self::RusakSedang => 'orange',
            self::RusakBerat => 'red',
        };
    }
}
