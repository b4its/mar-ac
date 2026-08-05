<?php

namespace App\Filament\Resources\Buildings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BuildingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_gedung')
                    ->required(),
                TextInput::make('kode_gedung'),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
