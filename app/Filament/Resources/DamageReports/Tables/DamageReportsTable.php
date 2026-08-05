<?php

namespace App\Filament\Resources\DamageReports\Tables;

use App\Enums\DamageLevel;
use App\Enums\DamageReportStatus;
use App\Filament\Resources\DamageReports\Actions\ApproveAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DamageReportsTable
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
                TextColumn::make('tingkat_kerusakan')
                    ->label('Tingkat Kerusakan')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => DamageLevel::from($state)->label())
                    ->color(fn (string $state): string => DamageLevel::from($state)->color()),
                TextColumn::make('jenis_kerusakan')
                    ->label('Jenis Kerusakan')
                    ->searchable(),
                TextColumn::make('tanggal_laporan')
                    ->label('Tanggal Laporan')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => DamageReportStatus::from($state)->label())
                    ->color(fn (string $state): string => DamageReportStatus::from($state)->color()),
                TextColumn::make('pelaporUser.name')
                    ->label('Pelapor'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(DamageReportStatus::cases())->mapWithKeys(
                        fn (DamageReportStatus $status): array => [$status->value => $status->label()]
                    )),
                SelectFilter::make('tingkat_kerusakan')
                    ->label('Tingkat Kerusakan')
                    ->options(collect(DamageLevel::cases())->mapWithKeys(
                        fn (DamageLevel $level): array => [$level->value => $level->label()]
                    )),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ApproveAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
