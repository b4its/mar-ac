<?php

namespace App\Http\Controllers\Web;

use App\Enums\JadwalStatus;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Department;
use App\Models\JadwalPemeliharaan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $query = Asset::with(['room.building', 'department'])
                      ->orderBy('nama_alat');
        
        // Apply search query
        if ($request->filled('q')) {
            $searchTerm = $request->input('q');
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama_alat', 'like', "%{$searchTerm}%")
                  ->orWhere('kode_alat', 'like', "%{$searchTerm}%")
                  ->orWhere('no_inventaris', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filter by department (admin only)
        if (auth()->check() && auth()->user()->hasRole('admin') && $request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }
        
        // Paginate with 15 items per page
        $assets = $query->paginate(15)->withQueryString();
        
        // Get departments for filter dropdown
        $departments = Department::with('building')->orderBy('nama_jurusan')->get();
        
        return view('aset.index', compact('assets', 'departments'));
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
