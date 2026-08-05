<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\MaintenanceReport;
use App\Models\RepairReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanSayaController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $damages = DamageReport::with(['asset', 'repairReport'])
            ->where('pelapor_user_id', $userId)
            ->latest()
            ->get();

        $maintenances = MaintenanceReport::with(['asset'])
            ->where('pelapor_user_id', $userId)
            ->latest()
            ->get();

        $repairs = RepairReport::with(['asset', 'damageReport'])
            ->where('teknisi_user_id', $userId)
            ->orWhereHas('damageReport', fn ($query) => $query->where('pelapor_user_id', $userId))
            ->latest()
            ->get();

        return view('laporan.saya', [
            'damages' => $damages,
            'maintenances' => $maintenances,
            'repairs' => $repairs,
        ]);
    }
}
