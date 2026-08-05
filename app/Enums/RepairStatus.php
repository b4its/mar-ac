<?php

namespace App\Enums;

enum RepairStatus: string
{
    case Diajukan = 'diajukan';
    case Revisi = 'revisi';
    case Disetujui = 'disetujui';

    public function label(): string
    {
        return match ($this) {
            self::Diajukan => 'Diajukan',
            self::Revisi => 'Revisi',
            self::Disetujui => 'Disetujui',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Diajukan => 'yellow',
            self::Revisi => 'orange',
            self::Disetujui => 'green',
        };
    }
}
