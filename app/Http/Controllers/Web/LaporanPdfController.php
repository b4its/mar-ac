<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\MaintenanceReport;
use App\Models\RepairReport;
use App\Services\LaporanPdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LaporanPdfController extends Controller
{
    public function damage(DamageReport $damageReport): View
    {
        abort_unless($this->canViewDamage($damageReport), 403);

        return view('laporan.pdf-preview', [
            'title' => 'Preview PDF Laporan Kerusakan',
            'nomor' => $damageReport->nomor_laporan,
            'fileUrl' => route('laporan.pdf.kerusakan.file', $damageReport),
            'downloadUrl' => route('laporan.pdf.kerusakan.file', [$damageReport, 'download' => 1]),
        ]);
    }

    public function damageFile(Request $request, DamageReport $damageReport): Response
    {
        abort_unless($this->canViewDamage($damageReport), 403);

        return LaporanPdf::damage($damageReport, $request->boolean('download'));
    }

    public function maintenance(MaintenanceReport $maintenanceReport): View
    {
        abort_unless($this->canViewMaintenance($maintenanceReport), 403);

        return view('laporan.pdf-preview', [
            'title' => 'Preview PDF Laporan Perawatan',
            'nomor' => $maintenanceReport->nomor_laporan,
            'fileUrl' => route('laporan.pdf.perawatan.file', $maintenanceReport),
            'downloadUrl' => route('laporan.pdf.perawatan.file', [$maintenanceReport, 'download' => 1]),
        ]);
    }

    public function maintenanceFile(Request $request, MaintenanceReport $maintenanceReport): Response
    {
        abort_unless($this->canViewMaintenance($maintenanceReport), 403);

        return LaporanPdf::maintenance($maintenanceReport, $request->boolean('download'));
    }

    public function repair(RepairReport $repairReport): View
    {
        abort_unless($this->canViewRepair($repairReport), 403);

        return view('laporan.pdf-preview', [
            'title' => 'Preview PDF Laporan Perbaikan',
            'nomor' => $repairReport->nomor_laporan,
            'fileUrl' => route('laporan.pdf.perbaikan.file', $repairReport),
            'downloadUrl' => route('laporan.pdf.perbaikan.file', [$repairReport, 'download' => 1]),
        ]);
    }

    public function repairFile(Request $request, RepairReport $repairReport): Response
    {
        abort_unless($this->canViewRepair($repairReport), 403);

        return LaporanPdf::repair($repairReport, $request->boolean('download'));
    }

    private function canViewDamage(DamageReport $report): bool
    {
        if (auth()->user()->can('approve laporan')) {
            return true;
        }

        return $report->pelapor_user_id === auth()->id();
    }

    private function canViewMaintenance(MaintenanceReport $report): bool
    {
        if (auth()->user()->can('approve laporan')) {
            return true;
        }

        return $report->pelapor_user_id === auth()->id();
    }

    private function canViewRepair(RepairReport $report): bool
    {
        if (auth()->user()->can('approve laporan')) {
            return true;
        }

        return $report->teknisi_user_id === auth()->id()
            || $report->damageReport?->pelapor_user_id === auth()->id();
    }
}
