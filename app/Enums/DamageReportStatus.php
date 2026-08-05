<?php

namespace App\Enums;

enum DamageReportStatus: string
{
    case Dilaporkan = 'dilaporkan';
    case Disetujui = 'disetujui';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Dilaporkan => 'Dilaporkan',
            self::Disetujui => 'Disetujui',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Dilaporkan => 'yellow',
            self::Disetujui => 'blue',
            self::Selesai => 'green',
            self::Ditolak => 'red',
        };
    }
}
