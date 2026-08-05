<?php

namespace App\Filament\Resources\Assets\Infolists;

use App\Models\Asset;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Aset')
                    ->schema([
                        TextEntry::make('nama_alat')
                            ->label('Nama Alat'),
                        TextEntry::make('jenis_alat')
                            ->label('Jenis Alat')
                            ->placeholder('-'),
                        TextEntry::make('kode_alat')
                            ->label('Kode Alat')
                            ->placeholder('-'),
                        TextEntry::make('no_inventaris')
                            ->label('No. Inventaris')
                            ->placeholder('-'),
                        TextEntry::make('room.building.nama_gedung')
                            ->label('Gedung')
                            ->placeholder('-'),
                        TextEntry::make('room.nama_ruangan')
                            ->label('Ruangan')
                            ->placeholder('-'),
                        TextEntry::make('department.nama_jurusan')
                            ->label('Jurusan/Unit')
                            ->placeholder('-'),
                        TextEntry::make('kapasitas')
                            ->label('Kapasitas')
                            ->placeholder('-'),
                        TextEntry::make('merk')
                            ->label('Merk')
                            ->placeholder('-'),
                        TextEntry::make('tahun_pemakaian')
                            ->label('Tahun Pemakaian')
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('last_maintenance_date')
                            ->label('Perawatan Terakhir')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Riwayat Penanganan Aset')
                    ->description('Kerusakan, perbaikan, dan perawatan aset ini menurut kronologis.')
                    ->schema([
                        TextEntry::make('riwayat')
                            ->label('Riwayat Panangan Aset')
                            ->hiddenLabel()
                            ->html()
                            ->state(fn (Asset $record): string => self::riwayatHtml($record)),
                    ]),
            ]);
    }

    private static function badgeHtml(string $label, string $color): string
    {
        $hex = match ($color) {
            'yellow' => '#fde047',
            'blue' => '#60a5fa',
            'green' => '#4ade80',
            'red' => '#f87171',
            default => '#d1d5db',
        };
        $text = in_array($color, ['yellow', 'green', 'gray'], true) ? '#111827' : '#ffffff';

        return sprintf(
            '<span style="display:inline-block;padding:2px 10px;border:1px solid #000;background:%s;color:%s;font-weight:600">%s</span>',
            $hex,
            $text,
            $label,
        );
    }

    private static function riwayatHtml(Asset $record): string
    {
        $rows = $record->laporanRiwayat();

        if ($rows->isEmpty()) {
            return '<p style="color:#555">Belum ada riwayat penanganan untuk aset ini.</p>';
        }

        $html = '<table style="width:100%;border-collapse:collapse;font-size:0.85rem">';
        $html .= '<thead><tr style="text-align:left;border-bottom:2px solid #000">'
            .'<th style="padding:8px">Tanggal</th>'
            .'<th style="padding:8px">Jenis</th>'
            .'<th style="padding:8px">Nomor Laporan</th>'
            .'<th style="padding:8px">Detail</th>'
            .'<th style="padding:8px">Status</th>'
            .'</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr style="border-bottom:1px solid #e5e7eb">';
            $html .= '<td style="padding:8px">'.$row['tanggal']->translatedFormat('d M Y').'</td>';
            $html .= '<td style="padding:8px">'.$row['jenisLabel'].'</td>';
            $html .= '<td style="padding:8px">'.$row['nomor'].'</td>';
            $html .= '<td style="padding:8px">'.$row['detail'].'</td>';
            $html .= '<td style="padding:8px">'.self::badgeHtml($row['statusLabel'], $row['statusColor']).'</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }
}
