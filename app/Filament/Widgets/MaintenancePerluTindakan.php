<?php

namespace App\Filament\Widgets;

use App\Enums\ReportStatus;
use App\Models\MaintenanceReport;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;

class MaintenancePerluTindakan extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->can('lihat dashboard');
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return 'Laporan Perawatan Menunggu Tindakan';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MaintenanceReport::query()
                    ->whereIn('status', [ReportStatus::Diajukan->value, ReportStatus::Diverifikasi->value])
                    ->latest()
            )
            ->columns([
                TextColumn::make('nomor_laporan')
                    ->label('Nomor Laporan'),
                TextColumn::make('asset.nama_alat')
                    ->label('Aset'),
                TextColumn::make('jenis_pekerjaan')
                    ->label('Jenis Pekerjaan'),
                TextColumn::make('tanggal_pelaksanaan')
                    ->label('Tanggal')
                    ->date(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ReportStatus::from($state)->label())
                    ->color(fn (string $state): string => ReportStatus::from($state)->color()),
                TextColumn::make('pelaporUser.name')
                    ->label('Pelapor'),
            ])
            ->paginated(false)
            ->recordUrl(fn (MaintenanceReport $record): string => route('filament.admin.resources.maintenance-reports.view', $record));
    }
}
