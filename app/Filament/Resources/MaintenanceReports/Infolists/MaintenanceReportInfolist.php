<?php

namespace App\Filament\Resources\MaintenanceReports\Infolists;

use App\Enums\ReportStatus;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class MaintenanceReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Laporan')
                    ->schema([
                        TextEntry::make('nomor_laporan')
                            ->label('Nomor Laporan')
                            ->copyable(),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ReportStatus::from($state)->label())
                            ->color(fn (string $state): string => ReportStatus::from($state)->color()),
                        TextEntry::make('asset.nama_alat')
                            ->label('Aset'),
                        TextEntry::make('asset.room.nama_ruangan')
                            ->label('Lokasi'),
                        TextEntry::make('asset.room.building.nama_gedung')
                            ->label('Gedung'),
                        TextEntry::make('pelaporUser.name')
                            ->label('Pelapor'),
                        TextEntry::make('jenis_pekerjaan')
                            ->label('Jenis Pekerjaan'),
                        TextEntry::make('tanggal_pelaksanaan')
                            ->label('Tanggal Pelaksanaan')
                            ->date(),
                        TextEntry::make('biaya')
                            ->label('Biaya Bahan/Sparepart')
                            ->money('IDR'),
                        TextEntry::make('biaya_jasa')
                            ->label('Biaya Jasa')
                            ->money('IDR'),
                        TextEntry::make('vendor.nama_vendor')
                            ->label('Vendor')
                            ->placeholder('-'),
                        TextEntry::make('uraian_pekerjaan')
                            ->label('Uraian Pekerjaan')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Alur Persetujuan')
                    ->schema([
                        TextEntry::make('verifikatorUser.name')
                            ->label('Verifikator')
                            ->placeholder('-'),
                        TextEntry::make('verified_at')
                            ->label('Waktu Verifikasi')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('approverUser.name')
                            ->label('Approver')
                            ->placeholder('-'),
                        TextEntry::make('approved_at')
                            ->label('Waktu Persetujuan')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('catatan')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Lampiran Foto Perawatan')
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
                                    ->label('Label'),
                            ])
                            ->columns(2)
                            ->placeholder('Belum ada lampiran.'),
                    ]),
            ]);
    }
}
