<?php

namespace App\Filament\Widgets;

use App\Models\DamageReport;
use App\Models\MaintenanceReport;
use App\Models\RepairReport;
use Filament\Widgets\ChartWidget;

class TrenLaporan extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Tren Laporan 6 Bulan Terakhir';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->can('lihat dashboard');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $labels = collect(range(5, 0))
            ->map(fn (int $offset): string => now()->subMonths($offset)->translatedFormat('M Y'));

        $perBulan = function ($query, string $column): array {
            $bulanIndex = [];

            foreach ((clone $query)->whereBetween($column, [now()->subMonths(5)->startOfMonth(), now()->endOfMonth()])->pluck($column) as $date) {
                $bulanIndex[(string) $date?->format('Y-m')] = ($bulanIndex[(string) $date?->format('Y-m')] ?? 0) + 1;
            }

            return collect(range(5, 0))
                ->map(fn (int $offset): int => $bulanIndex[now()->subMonths($offset)->format('Y-m')] ?? 0)
                ->all();
        };

        return [
            'labels' => $labels->all(),
            'datasets' => [
                [
                    'label' => 'Kerusakan',
                    'data' => $perBulan(DamageReport::query(), 'tanggal_laporan'),
                    'backgroundColor' => '#f87171',
                ],
                [
                    'label' => 'Perbaikan',
                    'data' => $perBulan(RepairReport::query(), 'tanggal_pelaksanaan'),
                    'backgroundColor' => '#60a5fa',
                ],
                [
                    'label' => 'Perawatan',
                    'data' => $perBulan(MaintenanceReport::query(), 'tanggal_pelaksanaan'),
                    'backgroundColor' => '#fde047',
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                ],
                'y' => [
                    'stacked' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
