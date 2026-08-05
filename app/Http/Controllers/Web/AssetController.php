<?php

namespace App\Http\Controllers\Web;

use App\Enums\JadwalStatus;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\JadwalPemeliharaan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $assets = Asset::with(['room.building', 'department'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama_alat', 'like', "%{$q}%")
                    ->orWhere('kode_alat', 'like', "%{$q}%")
                    ->orWhere('no_inventaris', 'like', "%{$q}%");
            })
            ->orderBy('nama_alat')
            ->get();

        return view('aset.index', [
            'assets' => $assets,
            'q' => $q,
        ]);
    }

    public function detail(Asset $asset): View
    {
        $asset->load(['room.building', 'department']);

        return view('aset.detail', [
            'asset' => $asset,
            'riwayat' => $asset->laporanRiwayat(),
            'jadwal' => JadwalPemeliharaan::query()
                ->where('asset_id', $asset->id)
                ->where('status', JadwalStatus::Terjadwal->value)
                ->whereDate('tanggal_jadwal', '>=', now()->toDateString())
                ->orderBy('tanggal_jadwal')
                ->get(),
        ]);
    }
}
