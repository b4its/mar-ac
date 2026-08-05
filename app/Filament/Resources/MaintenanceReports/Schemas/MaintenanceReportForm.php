<?php

namespace App\Filament\Resources\MaintenanceReports\Schemas;

use App\Enums\ReportStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\Collection;

class MaintenanceReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Laporan')
                    ->description('Informasi pelaporan hasil perawatan aset.')
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
                        TextInput::make('jenis_pekerjaan')
                            ->label('Jenis Pekerjaan')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('tanggal_pelaksanaan')
                            ->label('Tanggal Pelaksanaan')
                            ->required(),
                        TextInput::make('biaya')
                            ->label('Biaya Bahan/Sparepart (Rp)')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->prefix('Rp')
                            ->default(0)
                            ->minValue(0),
                        TextInput::make('biaya_jasa')
                            ->label('Biaya Jasa (Rp)')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->prefix('Rp')
                            ->default(0)
                            ->minValue(0),
                        Select::make('vendor_id')
                            ->label('Vendor (jika ada)')
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
                        Textarea::make('uraian_pekerjaan')
                            ->label('Uraian Pekerjaan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Alur Persetujuan')
                    ->description('Status dan petugas yang memverifikasi laporan ini.')
                    ->schema([
                        Select::make('status')
                            ->options(self::statusOptions())
                            ->searchable()
                            ->disabled(),
                        Select::make('verifikator_user_id')
                            ->label('Verifikator')
                            ->relationship('verifikatorUser', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        DateTimePicker::make('verified_at')
                            ->label('Waktu Verifikasi')
                            ->disabled(),
                        Select::make('approver_user_id')
                            ->label('Approver')
                            ->relationship('approverUser', 'name')
                            ->searchable()
                            ->preload()
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

    private static function statusOptions(): Collection
    {
        return collect(ReportStatus::cases())
            ->mapWithKeys(fn (ReportStatus $status): array => [$status->value => $status->label()]);
    }
}
