<?php

namespace App\Filament\Resources\JadwalPemeliharaan\Tables;

use App\Enums\JadwalStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JadwalPemeliharaanTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.nama_alat')
                    ->label('Aset')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_jadwal')
                    ->label('Tanggal Jadwal')
                    ->date()
                    ->sortable(),
                TextColumn::make('jenis_pekerjaan')
                    ->label('Jenis Pekerjaan')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => JadwalStatus::from($state)->label())
                    ->color(fn (string $state): string => JadwalStatus::from($state)->color()),
                TextColumn::make('createdByUser.name')
                    ->label('Dibuat Oleh'),
                TextColumn::make('selesai_at')
                    ->label('Waktu Selesai')
                    ->dateTime()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(JadwalStatus::cases())->mapWithKeys(
                        fn (JadwalStatus $status): array => [$status->value => $status->label()]
                    )),
                SelectFilter::make('asset_id')
                    ->label('Aset')
                    ->relationship('asset', 'nama_alat')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_jadwal', 'asc');
    }
}
