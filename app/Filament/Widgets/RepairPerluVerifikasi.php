<?php

namespace App\Filament\Widgets;

use App\Enums\RepairStatus;
use App\Models\RepairReport;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;

class RepairPerluVerifikasi extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->can('verifikasi laporan');
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return 'Laporan Hasil Perbaikan Menunggu Verifikasi';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                RepairReport::query()
                    ->whereIn('status', [RepairStatus::Diajukan->value, RepairStatus::Revisi->value])
                    ->latest()
            )
            ->columns([
                TextColumn::make('nomor_laporan')
                    ->label('Nomor Laporan'),
                TextColumn::make('damageReport.nomor_laporan')
                    ->label('Laporan Kerusakan'),
                TextColumn::make('asset.nama_alat')
                    ->label('Aset'),
                TextColumn::make('jenis_pekerjaan')
                    ->label('Jenis Pekerjaan'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => RepairStatus::from($state)->label())
                    ->color(fn (string $state): string => RepairStatus::from($state)->color()),
                TextColumn::make('teknisiUser.name')
                    ->label('Teknisi'),
            ])
            ->paginated(false)
            ->recordUrl(fn (RepairReport $record): string => route('filament.admin.resources.repair-reports.view', $record));
    }
}
