<?php

namespace App\Filament\Resources\RepairReports\Schemas;

use App\Enums\RepairStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class RepairReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Perbaikan')
                    ->description('Informasi pekerjaan perbaikan aset.')
                    ->schema([
                        Select::make('damage_report_id')
                            ->label('Laporan Kerusakan')
                            ->relationship('damageReport', 'nomor_laporan')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Select::make('asset_id')
                            ->label('Aset')
                            ->relationship('asset', 'nama_alat')
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->required(),
                        Select::make('vendor_id')
                            ->label('Vendor')
                            ->relationship('vendor', 'nama_vendor')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('nama_vendor')
                                    ->label('Nama Vendor')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('kontak')
                                    ->maxLength(255),
                                TextInput::make('telepon')
                                    ->maxLength(255),
                            ]),
                        Select::make('pelapor_user_id')
                            ->label('Pelapor')
                            ->relationship('pelaporUser', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->default(fn (): ?int => auth()->id()),
                        Select::make('teknisi_user_id')
                            ->label('Teknisi')
                            ->relationship('teknisiUser', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        TextInput::make('jenis_pekerjaan')
                            ->label('Jenis Pekerjaan')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('uraian_pekerjaan')
                            ->label('Uraian Pekerjaan')
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Pelaksanaan & Biaya')
                    ->description('Waktu pelaksanaan dan rincian biaya hasil perbaikan.')
                    ->schema([
                        DatePicker::make('tanggal_pelaksanaan')
                            ->label('Tanggal Pelaksanaan'),
                        TextInput::make('biaya')
                            ->label('Biaya Sparepart')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->prefix('Rp')
                            ->minValue(0),
                        TextInput::make('biaya_jasa')
                            ->label('Biaya Jasa')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->prefix('Rp')
                            ->minValue(0),
                    ])
                    ->columns(2),
                Section::make('Status')
                    ->description('Status dan catatan laporan perbaikan.')
                    ->schema([
                        Select::make('status')
                            ->options(collect(RepairStatus::cases())->mapWithKeys(
                                fn (RepairStatus $status): array => [$status->value => $status->label()]
                            ))
                            ->searchable()
                            ->disabled(),
                        DatePicker::make('verified_at')
                            ->label('Tanggal Verifikasi')
                            ->disabled(),
                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
