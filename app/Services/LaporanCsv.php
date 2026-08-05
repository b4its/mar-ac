<?php

namespace App\Services;

use App\Enums\DamageLevel;
use App\Enums\DamageReportStatus;
use App\Enums\RepairStatus;
use App\Enums\ReportStatus;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanCsv
{
    public static function damage(Collection $reports): StreamedResponse
    {
        return self::stream('rekap-laporan-kerusakan', function () use ($reports) {
            self::row(['Nomor', 'Tanggal', 'Aset', 'Lokasi', 'Tingkat', 'Jenis Kerusakan', 'Uraian', 'Status', 'Pelapor', 'Catatan']);

            foreach ($reports as $report) {
                self::row([
                    $report->nomor_laporan,
                    $report->tanggal_laporan?->translatedFormat('d/m/Y'),
                    $report->asset?->nama_alat,
                    $report->asset?->room?->building?->nama_gedung.' - '.$report->asset?->room?->nama_ruangan,
                    DamageLevel::from($report->tingkat_kerusakan)->label(),
                    $report->jenis_kerusakan,
                    $report->uraian_kerusakan,
                    DamageReportStatus::from($report->status)->label(),
                    $report->pelaporUser?->name,
                    $report->catatan,
                ]);
            }
        });
    }

    public static function maintenance(Collection $reports): StreamedResponse
    {
        return self::stream('rekap-laporan-perawatan', function () use ($reports) {
            self::row(['Nomor', 'Tanggal', 'Aset', 'Lokasi', 'Jenis Pekerjaan', 'Uraian', 'Biaya', 'Biaya Jasa', 'Vendor', 'Status', 'Pelapor']);

            foreach ($reports as $report) {
                self::row([
                    $report->nomor_laporan,
                    $report->tanggal_pelaksanaan?->translatedFormat('d/m/Y'),
                    $report->asset?->nama_alat,
                    $report->asset?->room?->building?->nama_gedung.' - '.$report->asset?->room?->nama_ruangan,
                    $report->jenis_pekerjaan,
                    strip_tags((string) $report->uraian_pekerjaan),
                    $report->biaya,
                    $report->biaya_jasa,
                    $report->vendor?->nama_vendor,
                    ReportStatus::from($report->status)->label(),
                    $report->pelaporUser?->name,
                ]);
            }
        });
    }

    public static function repair(Collection $reports): StreamedResponse
    {
        return self::stream('rekap-laporan-perbaikan', function () use ($reports) {
            self::row(['Nomor', 'Tanggal', 'Aset', 'Laporan Kerusakan', 'Jenis Pekerjaan', 'Uraian', 'Biaya', 'Biaya Jasa', 'Vendor', 'Teknisi', 'Status']);

            foreach ($reports as $report) {
                self::row([
                    $report->nomor_laporan,
                    $report->tanggal_pelaksanaan?->translatedFormat('d/m/Y'),
                    $report->asset?->nama_alat,
                    $report->damageReport?->nomor_laporan,
                    $report->jenis_pekerjaan,
                    strip_tags((string) $report->uraian_pekerjaan),
                    $report->biaya,
                    $report->biaya_jasa,
                    $report->vendor?->nama_vendor,
                    $report->teknisiUser?->name,
                    RepairStatus::from($report->status)->label(),
                ]);
            }
        });
    }

    private static function stream(string $filename, callable $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($writer) {
            echo "\xEF\xBB\xBF";
            $writer();
        }, $filename.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private static function row(array $values): void
    {
        fputcsv(fopen('php://output', 'w'), $values);
    }
}
