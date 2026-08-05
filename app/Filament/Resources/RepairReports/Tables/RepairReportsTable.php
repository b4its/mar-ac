<?php

namespace App\Filament\Resources\RepairReports\Tables;

use App\Enums\RepairStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RepairReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_laporan')
                    ->label('Nomor Laporan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => RepairStatus::from($state)->label())
                    ->color(fn (string $state): string => RepairStatus::from($state)->color())
                    ->sortable(),
                TextColumn::make('asset.nama_alat')
                    ->label('Aset')
                    ->searchable(),
                TextColumn::make('asset.room.nama_ruangan')
                    ->label('Lokasi'),
                TextColumn::make('vendor.nama_vendor')
                    ->label('Vendor')
                    ->placeholder('-'),
                TextColumn::make('teknisiUser.name')
                    ->label('Teknisi')
                    ->placeholder('-'),
                TextColumn::make('tanggal_pelaksanaan')
                    ->label('Pelaksanaan')
                    ->date()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('biaya')
                    ->label('Biaya Sparepart')
                    ->money('IDR')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(RepairStatus::cases())->mapWithKeys(
                        fn (RepairStatus $status): array => [$status->value => $status->label()]
                    )),
                SelectFilter::make('asset_id')
                    ->label('Aset')
                    ->relationship('asset', 'nama_alat')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn ($record) => $record ? route('filament.admin.resources.repair-reports.view', $record) : null);
    }
}
