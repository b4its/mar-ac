<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_vendor')
                    ->required(),
                TextInput::make('kontak'),
                TextInput::make('telepon')
                    ->tel(),
                Textarea::make('alamat')
                    ->columnSpanFull(),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
