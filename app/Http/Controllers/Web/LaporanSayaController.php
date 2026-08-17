<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Building;
use App\Models\DamageReport;
use App\Models\MaintenanceReport;
use App\Models\Room;
use App\Models\Department;
use App\Models\RepairReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanSayaController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();
        
        // Get all buildings, departments, rooms for filters (simplified to avoid missing columns)
        $buildings = Building::all();
        $allDepartments = Department::with('building')->orderBy('nama_jurusan')->get();
        $allRooms = Room::all();

        return view('laporan.saya', [
            'buildings' => $buildings,
            'departments' => $allDepartments,
            'rooms' => $allRooms,
        ]);
    }

    /**
     * Get paginated reports with AJAX
     */
    public function getReports(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $type = $request->input('type', 'damage'); // damage, maintenance, repair
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 10);
        
        // Filter parameters
        $buildingId = $request->input('building_id');
        $departmentId = $request->input('department_id');
        $roomId = $request->input('room_id');
        
        $reportTypeMap = [
            'damage' => DamageReport::with(['asset', 'repairReport', 'pelapor']),
            'maintenance' => MaintenanceReport::with(['asset', 'items.asset.room.building', 'items.asset.department', 'pelapor']),
            'repair' => RepairReport::with(['asset', 'damageReport', 'pelapor', 'teknisi']),
        ];
        
        $query = $reportTypeMap[$type] ?? $reportTypeMap['damage'];
        
        // Apply user filter based on report type
        if ($type === 'damage' || $type === 'maintenance') {
            $query->where('pelapor_user_id', $userId);
        } elseif ($type === 'repair') {
            $query->where(function ($q) use ($userId) {
                $q->where('teknisi_user_id', $userId)
                  ->orWhereHas('damageReport', fn ($qr) => $qr->where('pelapor_user_id', $userId));
            });
        }
        
        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_laporan', 'like', "%{$search}%")
                  ->orWhere('jenis_kerusakan', 'like', "%{$search}%")
                  ->orWhere('jenis_pekerjaan', 'like', "%{$search}%")
                  ->orWhereHas('asset', function ($q2) use ($search) {
                      $q2->where('nama_alat', 'like', "%{$search}%");
                  });
            });
        }
        
        // Location filters
        if ($buildingId) {
            $query->whereHas('asset.room.building', function ($q2) use ($buildingId) {
                $q2->where('id', $buildingId);
            });
        }
        
        if ($departmentId) {
            $query->whereHas('asset.department', function ($q2) use ($departmentId) {
                $q2->where('id', $departmentId);
            });
        }
        
        if ($roomId) {
            $query->whereHas('asset.room', function ($q2) use ($roomId) {
                $q2->where('id', $roomId);
            });
        }
        
        $reports = $query->latest()->paginate($perPage)->through(function ($report) use ($type) {
            if ($type === 'damage') {
                return [
                    'id' => $report->id,
                    'nomor_laporan' => $report->nomor_laporan,
                    'nama_alat' => $report->asset?->nama_alat ?? '-',
                    'jenis_kerusakan' => $report->jenis_kerusakan,
                    'tanggal_laporan' => $report->tanggal_laporan,
                    'tingkat_kerusakan' => $report->tingkat_kerusakan,
                    'status' => $report->status,
                    'gedung' => $report->asset?->room?->building?->nama_gedung ?? '-',
                    'ruangan' => $report->asset?->room?->nama_ruangan ?? '-',
                    'jurusan' => $report->asset?->department?->nama_jurusan ?? '-',
                    'user_name' => $report->pelapor?->name ?? '-',
                ];
            } elseif ($type === 'maintenance') {
                return [
                    'id' => $report->id,
                    'nomor_laporan' => $report->nomor_laporan,
                    'nama_alat' => $report->asset?->nama_alat ?? '-',
                    'jenis_pekerjaan' => $report->jenis_pekerjaan,
                    'tanggal_pelaksanaan' => $report->tanggal_pelaksanaan,
                    'status' => $report->status,
                    'gedung' => $report->asset?->room?->building?->nama_gedung ?? '-',
                    'ruangan' => $report->asset?->room?->nama_ruangan ?? '-',
                    'jurusan' => $report->asset?->department?->nama_jurusan ?? '-',
                    'user_name' => $report->pelapor?->name ?? '-',
                ];
            } else { // repair
                return [
                    'id' => $report->id,
                    'nomor_laporan' => $report->nomor_laporan,
                    'nama_alat' => $report->asset?->nama_alat ?? '-',
                    'jenis_pekerjaan' => $report->jenis_pekerjaan,
                    'tanggal_pelaksanaan' => $report->tanggal_pelaksanaan,
                    'status' => $report->status,
                    'nomor_kerusakan' => $report->damageReport?->nomor_laporan ?? '-',
                    'gedung' => $report->asset?->room?->building?->nama_gedung ?? '-',
                    'ruangan' => $report->asset?->room?->nama_ruangan ?? '-',
                    'jurusan' => $report->asset?->department?->nama_jurusan ?? '-',
                    'user_name' => $report->teknisi?->name ?? '-',
                ];
            }
        });
        
        return response()->json([
            'data' => $reports->items(),
            'current_page' => $reports->currentPage(),
            'last_page' => $reports->lastPage(),
            'total' => $reports->total(),
            'per_page' => $reports->perPage(),
            'from' => $reports->firstItem(),
            'to' => $reports->lastItem(),
        ]);
    }

    /**
     * Get searchable buildings
     */
    public function getBuildings(): JsonResponse
    {
        $buildings = Building::select('id', 'nama_gedung as text', 'id as value')->get();
        return response()->json($buildings);
    }

    /**
     * Get searchable departments by building
     */
    public function getDepartments(Request $request): JsonResponse
    {
        $buildingId = $request->input('building_id');
        $query = Department::select('id', 'nama_jurusan as text', 'id as value');
        
        if ($buildingId) {
            $query->where('building_id', $buildingId);
        }
        
        $departments = $query->orderBy('nama_jurusan')->get();
        return response()->json($departments);
    }

    /**
     * Get searchable rooms by department
     */
    public function getRooms(Request $request): JsonResponse
    {
        $departmentId = $request->input('department_id');
        $query = Room::select('id', 'nama_ruangan as text', 'id as value');
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        
        $rooms = $query->orderBy('nama_ruangan')->get();
        return response()->json($rooms);
    }
}
