<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetCondition;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_alat')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kode_alat')
                    ->searchable(),
                TextColumn::make('no_inventaris')
                    ->searchable(),
                TextColumn::make('room.building.nama_gedung')
                    ->label('Gedung')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('room.nama_ruangan')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.nama_jurusan')
                    ->label('Jurusan/Unit')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('last_maintenance_date')
                    ->label('Perawatan Terakhir')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(AssetCondition::class),
                SelectFilter::make('room_id')
                    ->relationship('room', 'nama_ruangan')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('department_id')
                    ->relationship('department', 'nama_jurusan')
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
            ]);
    }
}
