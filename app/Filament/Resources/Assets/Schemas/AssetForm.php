<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Enums\AssetCondition;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->components([
                        TextInput::make('nama_alat')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('jenis_alat')
                            ->maxLength(255),
                        TextInput::make('kode_alat')
                            ->maxLength(255),
                        TextInput::make('no_inventaris')
                            ->maxLength(255),
                        Select::make('room_id')
                            ->relationship('room', 'nama_ruangan')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Grid::make(2)
                                    ->components([
                                        Select::make('building_id')
                                            ->relationship('building', 'nama_gedung')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('nama_gedung')
                                                    ->label('Nama Gedung')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('kode_gedung')
                                                    ->label('Kode Gedung')
                                                    ->maxLength(255),
                                            ])
                                            ->required(),
                                        TextInput::make('nama_ruangan')
                                            ->required(),
                                        TextInput::make('kode_ruangan'),
                                    ]),
                            ]),
                        Select::make('department_id')
                            ->relationship('department', 'nama_jurusan')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('nama_jurusan')
                                    ->label('Nama Jurusan / Unit')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('kode_jurusan')
                                    ->label('Kode Jurusan / Unit')
                                    ->maxLength(255),
                            ]),
                        TextInput::make('kapasitas')
                            ->maxLength(255),
                        TextInput::make('merk')
                            ->maxLength(255),
                        TextInput::make('tahun_pemakaian')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue((int) now()->format('Y')),
                        Select::make('status')
                            ->options(AssetCondition::class)
                            ->searchable()
                            ->default(AssetCondition::Baik)
                            ->required(),
                        DatePicker::make('last_maintenance_date'),
                    ]),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
