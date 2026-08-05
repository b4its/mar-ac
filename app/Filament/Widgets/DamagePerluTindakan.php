<?php

namespace App\Filament\Widgets;

use App\Enums\DamageLevel;
use App\Enums\DamageReportStatus;
use App\Models\DamageReport;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;

class DamagePerluTindakan extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->can('lihat dashboard');
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return 'Laporan Kerusakan Menunggu Persetujuan';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DamageReport::query()
                    ->where('status', DamageReportStatus::Dilaporkan->value)
                    ->latest()
            )
            ->columns([
                TextColumn::make('nomor_laporan')
                    ->label('Nomor Laporan'),
                TextColumn::make('asset.nama_alat')
                    ->label('Aset'),
                TextColumn::make('jenis_kerusakan')
                    ->label('Jenis Kerusakan'),
                TextColumn::make('tanggal_laporan')
                    ->label('Tanggal')
                    ->date(),
                TextColumn::make('tingkat_kerusakan')
                    ->label('Tingkat')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => DamageLevel::from($state)->label())
                    ->color(fn (string $state): string => DamageLevel::from($state)->color()),
                TextColumn::make('pelaporUser.name')
                    ->label('Pelapor'),
            ])
            ->paginated(false)
            ->recordUrl(fn (DamageReport $record): string => route('filament.admin.resources.damage-reports.view', $record));
    }
}
