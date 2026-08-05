<?php

namespace App\Filament\Resources\JadwalPemeliharaan\Schemas;

use App\Enums\JadwalStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JadwalPemeliharaanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('asset_id')
                    ->label('Aset')
                    ->relationship('asset', 'nama_alat')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nama_alat')
                            ->label('Nama Aset')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('kode_alat')
                            ->label('Kode Alat')
                            ->maxLength(255),
                        TextInput::make('no_inventaris')
                            ->label('No. Inventaris')
                            ->maxLength(255),
                    ])
                    ->required(),
                DatePicker::make('tanggal_jadwal')
                    ->label('Tanggal Jadwal')
                    ->native(false)
                    ->required(),
                TextInput::make('jenis_pekerjaan')
                    ->label('Jenis Pekerjaan')
                    ->required()
                    ->maxLength(255),
                Textarea::make('catatan')
                    ->label('Catatan')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(collect(JadwalStatus::cases())->mapWithKeys(
                        fn (JadwalStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->searchable()
                    ->default(JadwalStatus::Terjadwal->value)
                    ->required(),
                DateTimePicker::make('selesai_at')
                    ->label('Waktu Selesai')
                    ->native(false),
            ])
            ->columns(2);
    }
}
