<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReportNumberService
{
    private const PREFIX = 'UPA.PP';

    private const TYPE_CODE = [
        'maintenance' => 'PRW',
        'damage' => 'KSR',
        'repair' => 'PRB',
    ];

    public function generate(string $type): string
    {
        $code = self::TYPE_CODE[$type] ?? 'LPR';
        $year = now()->format('Y');
        $table = $type === 'damage' ? 'damage_reports' : ($type === 'maintenance' ? 'maintenance_reports' : 'repair_reports');

        $sequence = (int) DB::table($table)
            ->whereYear('created_at', $year)
            ->count();

        do {
            $sequence++;

            $number = sprintf(
                '%03d/%s/%s/%s',
                $sequence,
                self::PREFIX,
                $code,
                $year,
            );
        } while (DB::table($table)->where('nomor_laporan', $number)->exists());

        return $number;
    }
}
