<?php

namespace App\Filament\Resources\MaintenanceReports\Tables;

use App\Enums\ReportStatus;
use App\Filament\Resources\MaintenanceReports\Actions\ApproveAction;
use App\Filament\Resources\MaintenanceReports\Actions\VerifikasiAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class MaintenanceReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_laporan')
                    ->label('Nomor Laporan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset.nama_alat')
                    ->label('Aset')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_pekerjaan')
                    ->label('Jenis Pekerjaan')
                    ->searchable(),
                TextColumn::make('tanggal_pelaksanaan')
                    ->label('Tanggal Pelaksanaan')
                    ->date()
                    ->sortable(),
                TextColumn::make('biaya')
                    ->label('Biaya (Rp)')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ReportStatus::from($state)->label())
                    ->color(fn (string $state): string => ReportStatus::from($state)->color()),
                TextColumn::make('pelaporUser.name')
                    ->label('Pelapor'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(self::statusOptions()),
                SelectFilter::make('asset_id')
                    ->label('Aset')
                    ->relationship('asset', 'nama_alat')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                VerifikasiAction::make(),
                ApproveAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private static function statusOptions(): Collection
    {
        return collect(ReportStatus::cases())
            ->mapWithKeys(fn (ReportStatus $status): array => [$status->value => $status->label()]);
    }
}
