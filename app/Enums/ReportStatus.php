<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Diajukan = 'diajukan';
    case Diverifikasi = 'diverifikasi';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';
    case Revisi = 'revisi';

    public function label(): string
    {
        return match ($this) {
            self::Diajukan => 'Diajukan',
            self::Diverifikasi => 'Diverifikasi',
            self::Disetujui => 'Disetujui',
            self::Ditolak => 'Ditolak',
            self::Revisi => 'Revisi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Diajukan => 'yellow',
            self::Diverifikasi => 'blue',
            self::Disetujui => 'green',
            self::Ditolak => 'red',
            self::Revisi => 'gray',
        };
    }

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::Diajukan => in_array($to, [self::Diverifikasi, self::Ditolak]),
            self::Diverifikasi => in_array($to, [self::Disetujui, self::Revisi]),
            self::Disetujui => false,
            self::Ditolak => false,
            self::Revisi => in_array($to, [self::Diajukan, self::Ditolak]),
        };
    }
}
