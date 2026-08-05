<?php

namespace App\Enums;

enum DamageLevel: string
{
    case Ringan = 'ringan';
    case Sedang = 'sedang';
    case Berat = 'berat';

    public function label(): string
    {
        return match ($this) {
            self::Ringan => 'Ringan',
            self::Sedang => 'Sedang',
            self::Berat => 'Berat',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Ringan => 'yellow',
            self::Sedang => 'orange',
            self::Berat => 'red',
        };
    }
}
