<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\MaintenanceReport;
use App\Models\RepairReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportStatusController extends Controller
{
    public function show(Request $request): View
    {
        $nomor = trim((string) $request->query('nomor', ''));

        $maintenance = null;
        $damage = null;
        $repair = null;

        if ($nomor !== '') {
            $maintenance = MaintenanceReport::with(['asset', 'pelaporUser', 'attachments'])->where('nomor_laporan', $nomor)->first();
            $damage = DamageReport::with(['asset', 'pelaporUser', 'repairReport.attachments', 'repairReport.vendor', 'repairReport.teknisiUser'])->where('nomor_laporan', $nomor)->first();
            $repair = RepairReport::with(['asset', 'damageReport', 'attachments', 'vendor', 'teknisiUser'])->where('nomor_laporan', $nomor)->first();

            if (! $this->canView($damage, $maintenance, $repair)) {
                $maintenance = null;
                $damage = null;
                $repair = null;
            }
        }

        return view('laporan.status', [
            'nomor' => $nomor,
            'maintenance' => $maintenance,
            'damage' => $damage,
            'repair' => $repair,
        ]);
    }

    private function canView(?DamageReport $damage, ?MaintenanceReport $maintenance, ?RepairReport $repair): bool
    {
        if (auth()->user()->can('verifikasi laporan') || auth()->user()->can('approve laporan')) {
            return true;
        }

        $ownerIds = collect([$damage?->pelapor_user_id, $maintenance?->pelapor_user_id, $repair?->teknisi_user_id])
            ->filter()
            ->unique();

        return $ownerIds->contains(auth()->id());
    }
}
