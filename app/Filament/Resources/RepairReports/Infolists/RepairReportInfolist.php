<?php

namespace App\Filament\Resources\RepairReports\Infolists;

use App\Enums\RepairStatus;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class RepairReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Perbaikan')
                    ->schema([
                        TextEntry::make('nomor_laporan')
                            ->label('Nomor Laporan')
                            ->copyable(),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => RepairStatus::from($state)->label())
                            ->color(fn (string $state): string => RepairStatus::from($state)->color()),
                        TextEntry::make('damageReport.nomor_laporan')
                            ->label('Laporan Kerusakan')
                            ->url(fn ($record) => $record->damageReport
                                ? route('filament.admin.resources.damage-reports.view', $record->damageReport)
                                : null)
                            ->placeholder('-'),
                        TextEntry::make('asset.nama_alat')
                            ->label('Aset'),
                        TextEntry::make('asset.room.nama_ruangan')
                            ->label('Lokasi'),
                        TextEntry::make('asset.room.building.nama_gedung')
                            ->label('Gedung'),
                        TextEntry::make('vendor.nama_vendor')
                            ->label('Vendor')
                            ->placeholder('-'),
                        TextEntry::make('pelaporUser.name')
                            ->label('Pelapor'),
                        TextEntry::make('teknisiUser.name')
                            ->label('Teknisi')
                            ->placeholder('-'),
                        TextEntry::make('jenis_pekerjaan')
                            ->label('Jenis Pekerjaan'),
                        TextEntry::make('uraian_pekerjaan')
                            ->label('Uraian Pekerjaan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Pelaksanaan & Biaya')
                    ->schema([
                        TextEntry::make('tanggal_pelaksanaan')
                            ->label('Tanggal Pelaksanaan')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('biaya')
                            ->label('Biaya Sparepart')
                            ->money('IDR')
                            ->placeholder('-'),
                        TextEntry::make('biaya_jasa')
                            ->label('Biaya Jasa')
                            ->money('IDR')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Status')
                    ->schema([
                        TextEntry::make('verifikatorUser.name')
                            ->label('Verifikator')
                            ->placeholder('-'),
                        TextEntry::make('verified_at')
                            ->label('Waktu Verifikasi')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('catatan')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make('Lampiran Foto Perbaikan')
                    ->schema([
                        RepeatableEntry::make('attachments')
                            ->label('Lampiran')
                            ->schema([
                                TextEntry::make('file_path')
                                    ->label('Foto')
                                    ->formatStateUsing(fn ($state, $record): HtmlString => new HtmlString(
                                        '<a href="'.e($record->url()).'" target="_blank" rel="noopener"><img src="'.e($record->url()).'" alt="'.e($record->caption ?: 'Lampiran foto').'" style="max-height: 180px; max-width: 100%; border-radius: 8px; object-fit: contain;"></a>'
                                    ))
                                    ->html(),
                                TextEntry::make('caption')
                                    ->label('Caption'),
                            ])
                            ->columns(2)
                            ->placeholder('Belum ada lampiran.'),
                    ]),
            ]);
    }
}
