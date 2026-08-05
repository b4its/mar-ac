<?php

namespace App\Filament\Resources\Rooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('building_id')
                    ->label('Gedung')
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
                    ->label('Nama Ruangan')
                    ->required(),
                TextInput::make('kode_ruangan')
                    ->label('Kode Ruangan'),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
