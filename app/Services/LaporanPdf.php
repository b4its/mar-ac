<?php

namespace App\Services;

use App\Models\DamageReport;
use App\Models\MaintenanceReport;
use App\Models\RepairReport;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class LaporanPdf
{
    public static function damage(DamageReport $report, bool $download = false): Response
    {
        return self::render('pdf.damage-report', ['report' => $report], self::damageFilename($report), $download);
    }

    public static function maintenance(MaintenanceReport $report, bool $download = false): Response
    {
        return self::render('pdf.maintenance-report', ['report' => $report], self::maintenanceFilename($report), $download);
    }

    public static function repair(RepairReport $report, bool $download = false): Response
    {
        return self::render('pdf.repair-report', ['report' => $report], self::repairFilename($report), $download);
    }

    public static function damageFilename(DamageReport $report): string
    {
        return self::safeFilename('FM-Polnes-11-02-03-R3-'.$report->nomor_laporan.'.pdf');
    }

    public static function maintenanceFilename(MaintenanceReport $report): string
    {
        return self::safeFilename('FM-Polnes-11-12-11-R3-'.$report->nomor_laporan.'.pdf');
    }

    public static function repairFilename(RepairReport $report): string
    {
        return self::safeFilename('FM-Polnes-11-02-04-R3-'.$report->nomor_laporan.'.pdf');
    }

    private static function render(string $view, array $data, string $filename, bool $download): Response
    {
        $data['storageUrl'] = fn ($attachment): string => self::attachmentDataUri($attachment);
        $data['logoSource'] = self::logoDataUri();

        $previousHome = getenv('HOME') ?: null;
        putenv('HOME=/tmp');
        $_ENV['HOME'] = '/tmp';

        try {
            $content = Pdf::view($view, $data)
                ->format('a4')
                ->margins(16, 16, 16, 16)
                ->name($filename)
                ->generatePdfContent();

            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => ($download ? 'attachment' : 'inline').'; filename="'.$filename.'"',
            ]);
        } finally {
            if ($previousHome === null) {
                putenv('HOME');
                unset($_ENV['HOME']);
            } else {
                putenv("HOME=$previousHome");
                $_ENV['HOME'] = $previousHome;
            }
        }
    }

    private static function safeFilename(string $filename): string
    {
        return str_replace('/', '-', $filename);
    }

    private static function logoDataUri(): string
    {
        $path = public_path('images/logoPolnes.png');

        if (! File::exists($path)) {
            return '';
        }

        return 'data:image/png;base64,'.base64_encode(File::get($path));
    }

    private static function attachmentDataUri($attachment): string
    {
        $path = str_starts_with($attachment->file_path, 'media/')
            ? public_path($attachment->file_path)
            : Storage::disk('public')->path($attachment->file_path);

        if (! File::exists($path)) {
            return '';
        }

        $mimeType = File::mimeType($path) ?: $attachment->mime_type ?: 'image/jpeg';

        return 'data:'.$mimeType.';base64,'.base64_encode(File::get($path));
    }
}
