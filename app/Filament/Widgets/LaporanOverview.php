<?php

namespace App\Filament\Widgets;

use App\Enums\AssetCondition;
use App\Enums\DamageReportStatus;
use App\Enums\ReportStatus;
use App\Models\Asset;
use App\Models\DamageReport;
use App\Models\MaintenanceReport;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LaporanOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()->can('lihat dashboard');
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $maintenanceDiajukan = MaintenanceReport::where('status', ReportStatus::Diajukan->value)->count();
        $maintenanceDiverifikasi = MaintenanceReport::where('status', ReportStatus::Diverifikasi->value)->count();
        $maintenanceDisetujui = MaintenanceReport::where('status', ReportStatus::Disetujui->value)->count();
        $damageDilaporkan = DamageReport::where('status', DamageReportStatus::Dilaporkan->value)->count();
        $damageDisetujui = DamageReport::where('status', DamageReportStatus::Disetujui->value)->count();
        $assetRusak = Asset::whereNot('status', AssetCondition::Baik->value)->count();
        $menunggu = $maintenanceDiajukan + $maintenanceDiverifikasi + $damageDilaporkan;

        return [
            Stat::make('Laporan Perawatan', MaintenanceReport::count())
                ->description("{$maintenanceDiajukan} diajukan · {$maintenanceDiverifikasi} diverifikasi · {$maintenanceDisetujui} disetujui")
                ->descriptionIcon(Heroicon::OutlinedInformationCircle)
                ->color('primary')
                ->icon(Heroicon::OutlinedClipboardDocumentCheck),
            Stat::make('Laporan Kerusakan', DamageReport::count())
                ->description("{$damageDilaporkan} menunggu persetujuan · {$damageDisetujui} disetujui")
                ->descriptionIcon(Heroicon::OutlinedInformationCircle)
                ->color('warning')
                ->icon(Heroicon::OutlinedClipboardDocumentList),
            Stat::make('Aset Terdaftar', Asset::count())
                ->description("{$assetRusak} aset berstatus rusak")
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color('info')
                ->icon(Heroicon::OutlinedCube),
            Stat::make('Menunggu Tindakan', $menunggu)
                ->description('laporan butuh verifikasi/persetujuan')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($menunggu > 0 ? 'danger' : 'success')
                ->icon(Heroicon::OutlinedShieldExclamation),
        ];
    }
}
