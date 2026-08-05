<?php

namespace App\Filament\Resources\DamageReports\Schemas;

use App\Enums\DamageLevel;
use App\Enums\DamageReportStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DamageReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Laporan')
                    ->description('Informasi laporan kerusakan aset.')
                    ->schema([
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
                        Select::make('pelapor_user_id')
                            ->label('Pelapor')
                            ->relationship('pelaporUser', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn (): ?int => auth()->id()),
                        Select::make('tingkat_kerusakan')
                            ->label('Tingkat Kerusakan')
                            ->options(collect(DamageLevel::cases())->mapWithKeys(
                                fn (DamageLevel $level): array => [$level->value => $level->label()]
                            ))
                            ->searchable()
                            ->default(DamageLevel::Ringan)
                            ->required(),
                        TextInput::make('jenis_kerusakan')
                            ->label('Jenis Kerusakan')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('tanggal_laporan')
                            ->label('Tanggal Laporan')
                            ->default(now())
                            ->required(),
                        Textarea::make('uraian_kerusakan')
                            ->label('Uraian Kerusakan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Alur Persetujuan')
                    ->description('Status dan petugas yang memproses laporan ini.')
                    ->schema([
                        Select::make('status')
                            ->options(collect(DamageReportStatus::cases())->mapWithKeys(
                                fn (DamageReportStatus $status): array => [$status->value => $status->label()]
                            ))
                            ->searchable()
                            ->disabled(),
                        DateTimePicker::make('approved_at')
                            ->label('Waktu Persetujuan')
                            ->disabled(),
                        Textarea::make('catatan')
                            ->label('Catatan'),
                    ])
                    ->columns(2),
            ]);
    }
}
