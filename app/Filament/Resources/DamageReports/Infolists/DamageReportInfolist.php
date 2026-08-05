<?php

namespace App\Filament\Resources\DamageReports\Infolists;

use App\Enums\DamageLevel;
use App\Enums\DamageReportStatus;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class DamageReportInfolist
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
                            ->formatStateUsing(fn (string $state): string => DamageReportStatus::from($state)->label())
                            ->color(fn (string $state): string => DamageReportStatus::from($state)->color()),
                        TextEntry::make('asset.nama_alat')
                            ->label('Aset'),
                        TextEntry::make('asset.room.nama_ruangan')
                            ->label('Lokasi'),
                        TextEntry::make('asset.room.building.nama_gedung')
                            ->label('Gedung'),
                        TextEntry::make('pelaporUser.name')
                            ->label('Pelapor'),
                        TextEntry::make('tingkat_kerusakan')
                            ->label('Tingkat Kerusakan')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => DamageLevel::from($state)->label())
                            ->color(fn (string $state): string => DamageLevel::from($state)->color()),
                        TextEntry::make('jenis_kerusakan')
                            ->label('Jenis Kerusakan'),
                        TextEntry::make('tanggal_laporan')
                            ->label('Tanggal Laporan')
                            ->date(),
                        TextEntry::make('uraian_kerusakan')
                            ->label('Uraian Kerusakan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Alur Persetujuan')
                    ->schema([
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
                Section::make('Lampiran Foto Kerusakan')
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
