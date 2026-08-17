<?php

namespace App\Livewire;

use App\Models\Building;
use App\Models\Department;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * Filter lokasi bercabang: Gedung → Jurusan → Ruangan.
 *
 * Setiap pilihan disajikan sebagai select yang dikombinasikan dengan fitur
 * pencarian. Saat salah satu pilihan berubah, komponen mengirim event
 * `lokasi-filter-changed` agar pemilih aset (SearchableSelect) mempersempit
 * daftar aset yang tersedia.
 */
class LokasiFilter extends Component
{
    public ?int $buildingId = null;

    public ?int $departmentId = null;

    public ?int $roomId = null;

    public string $searchBuilding = '';

    public string $searchDepartment = '';

    public string $searchRoom = '';

    public bool $openBuilding = false;

    public bool $openDepartment = false;

    public bool $openRoom = false;

    public function updatedSearchBuilding(): void
    {
        $this->openBuilding = true;
        $this->openDepartment = false;
        $this->openRoom = false;
    }

    public function updatedSearchDepartment(): void
    {
        $this->openDepartment = true;
        $this->openRoom = false;
    }

    public function updatedSearchRoom(): void
    {
        $this->openRoom = true;
    }

    public function selectBuilding(int $id): void
    {
        $this->buildingId = $id;
        $this->departmentId = null;
        $this->roomId = null;
        $this->searchBuilding = Building::find($id)?->nama_gedung ?? '';
        $this->searchDepartment = '';
        $this->searchRoom = '';
        $this->closeAll();
        $this->openDepartment = true;
        $this->dispatchFilter();
    }

    public function clearBuilding(): void
    {
        $this->buildingId = null;
        $this->departmentId = null;
        $this->roomId = null;
        $this->searchBuilding = '';
        $this->searchDepartment = '';
        $this->searchRoom = '';
        $this->closeAll();
        $this->dispatchFilter();
    }

    public function selectDepartment(int $id): void
    {
        $this->departmentId = $id;
        $this->roomId = null;
        $this->searchDepartment = Department::find($id)?->nama_jurusan ?? '';
        $this->searchRoom = '';
        $this->closeAll();
        $this->openRoom = true;
        $this->dispatchFilter();
    }

    public function clearDepartment(): void
    {
        $this->departmentId = null;
        $this->roomId = null;
        $this->searchDepartment = '';
        $this->searchRoom = '';
        $this->closeAll();
        $this->dispatchFilter();
    }

    public function selectRoom(int $id): void
    {
        $this->roomId = $id;
        $this->searchRoom = Room::with('building')->find($id)?->nama_ruangan ?? '';
        $this->closeAll();
        $this->dispatchFilter();
    }

    public function clearRoom(): void
    {
        $this->roomId = null;
        $this->searchRoom = '';
        $this->closeAll();
        $this->dispatchFilter();
    }

    public function getBuildingOptionsProperty(): Collection
    {
        $search = trim($this->searchBuilding);

        return Building::query()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('nama_gedung', 'like', "%{$search}%")
                    ->orWhere('kode_gedung', 'like', "%{$search}%");
            }))
            ->orderBy('nama_gedung')
            ->limit(10)
            ->get()
            ->map(fn (Building $building): array => [
                'id' => $building->id,
                'label' => $building->nama_gedung,
                'description' => $building->kode_gedung ? 'Kode: '.$building->kode_gedung : '',
            ]);
    }

    public function getDepartmentLockedProperty(): bool
    {
        return ! $this->buildingId;
    }

    public function getRoomLockedProperty(): bool
    {
        return ! $this->buildingId || ! $this->departmentId;
    }

    public function getDepartmentOptionsProperty(): Collection
    {
        if (! $this->buildingId) {
            return collect();
        }

        $search = trim($this->searchDepartment);

        return Department::query()
            ->when($this->buildingId, fn ($query) => $query->whereHas('assets.room', function ($query) {
                $query->where('rooms.building_id', $this->buildingId);
            }))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('nama_jurusan', 'like', "%{$search}%")
                    ->orWhere('kode_jurusan', 'like', "%{$search}%");
            }))
            ->orderBy('nama_jurusan')
            ->limit(10)
            ->get()
            ->map(fn (Department $department): array => [
                'id' => $department->id,
                'label' => $department->nama_jurusan,
                'description' => $department->kode_jurusan ? 'Kode: '.$department->kode_jurusan : '',
            ]);
    }

    public function getRoomOptionsProperty(): Collection
    {
        if (! $this->buildingId || ! $this->departmentId) {
            return collect();
        }

        $search = trim($this->searchRoom);

        return Room::query()
            ->with('building')
            ->when($this->buildingId, fn ($query) => $query->where('rooms.building_id', $this->buildingId))
            ->when($this->departmentId, fn ($query) => $query->whereHas('assets', function ($query) {
                $query->where('assets.department_id', $this->departmentId);
            }))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('nama_ruangan', 'like', "%{$search}%")
                    ->orWhere('kode_ruangan', 'like', "%{$search}%");
            }))
            ->orderBy('nama_ruangan')
            ->limit(10)
            ->get()
            ->map(fn (Room $room): array => [
                'id' => $room->id,
                'label' => $room->nama_ruangan,
                'description' => collect([$room->kode_ruangan, $room->building?->nama_gedung])->filter()->implode(' - '),
            ]);
    }

    public function render(): View
    {
        return view('livewire.lokasi-filter');
    }

    private function closeAll(): void
    {
        $this->openBuilding = false;
        $this->openDepartment = false;
        $this->openRoom = false;
    }

    private function dispatchFilter(): void
    {
        $this->dispatch(
            'lokasi-filter-changed',
            buildingId: $this->buildingId,
            departmentId: $this->departmentId,
            roomId: $this->roomId,
        );
    }
}
