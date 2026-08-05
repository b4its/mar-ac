<?php

namespace App\Filament\Widgets;

use App\Enums\JadwalStatus;
use App\Models\JadwalPemeliharaan;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;

class JadwalTerdekat extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->can('kelola jadwal') || auth()->user()->can('lihat dashboard');
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return 'Jadwal Pemeliharaan Terdekat';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JadwalPemeliharaan::query()
                    ->where('status', JadwalStatus::Terjadwal->value)
                    ->whereDate('tanggal_jadwal', '>=', now()->toDateString())
                    ->orderBy('tanggal_jadwal')
            )
            ->columns([
                TextColumn::make('tanggal_jadwal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('asset.nama_alat')
                    ->label('Aset'),
                TextColumn::make('asset.room.nama_ruangan')
                    ->label('Lokasi'),
                TextColumn::make('jenis_pekerjaan')
                    ->label('Jenis Pekerjaan'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => JadwalStatus::from($state)->label())
                    ->color(fn (string $state): string => JadwalStatus::from($state)->color()),
            ])
            ->paginated(false)
            ->recordUrl(fn (JadwalPemeliharaan $record): string => route('filament.admin.resources.jadwal-pemeliharaans.view', $record));
    }
}
