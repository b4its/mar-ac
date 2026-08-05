<?php

namespace App\Filament\Widgets;

use App\Enums\AssetCondition;
use App\Models\Asset;
use Filament\Widgets\DoughnutChartWidget;

class KondisiAset extends DoughnutChartWidget
{
    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        return 'Distribusi Kondisi Aset';
    }

    public static function canView(): bool
    {
        return auth()->user()->can('lihat dashboard');
    }

    protected function getData(): array
    {
        $hex = [
            'green' => '#4ade80',
            'yellow' => '#fde047',
            'orange' => '#fb923c',
            'red' => '#f87171',
        ];

        $labels = [];
        $data = [];
        $colors = [];

        foreach (AssetCondition::cases() as $condition) {
            $count = Asset::where('status', $condition->value)->count();

            $labels[] = $condition->label();
            $data[] = $count;
            $colors[] = $hex[$condition->color()];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
        ];
    }
}
