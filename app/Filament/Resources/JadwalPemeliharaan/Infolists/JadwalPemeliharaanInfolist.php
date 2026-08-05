<?php

namespace App\Filament\Resources\JadwalPemeliharaan\Infolists;

use App\Enums\JadwalStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JadwalPemeliharaanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Jadwal')
                    ->schema([
                        TextEntry::make('asset.nama_alat')
                            ->label('Aset'),
                        TextEntry::make('asset.room.nama_ruangan')
                            ->label('Lokasi')
                            ->placeholder('-'),
                        TextEntry::make('tanggal_jadwal')
                            ->label('Tanggal Jadwal')
                            ->date(),
                        TextEntry::make('jenis_pekerjaan')
                            ->label('Jenis Pekerjaan'),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => JadwalStatus::from($state)->label())
                            ->color(fn (string $state): string => JadwalStatus::from($state)->color()),
                        TextEntry::make('selesai_at')
                            ->label('Waktu Selesai')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('createdByUser.name')
                            ->label('Dibuat Oleh')
                            ->placeholder('-'),
                        TextEntry::make('catatan')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
