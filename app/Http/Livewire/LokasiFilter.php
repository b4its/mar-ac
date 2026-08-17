<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Building;
use App\Models\Department;
use App\Models\Room;

class LokasiFilter extends Component
{
    public ?int $buildingId = null;
    public ?int $departmentId = null;
    public ?int $roomId = null;
    
    public string $searchBuilding = '';
    public string $searchDepartment = '';
    public string $searchRoom = '';
    
    public function updated($propertyName)
    {
        // Reset dependent selections when parent changes
        if ($propertyName === 'buildingId') {
            $this->departmentId = null;
            $this->roomId = null;
            $this->searchDepartment = '';
            $this->searchRoom = '';
        }
        
        if ($propertyName === 'departmentId') {
            $this->roomId = null;
            $this->searchRoom = '';
        }
    }

    protected function getBuildingsProperty()
    {
        $query = Building::query();
        
        if ($this->searchBuilding) {
            $query->where('nama_gedung', 'like', "%{$this->searchBuilding}%");
        }
        
        return $query->orderBy('nama_gedung')->limit(20)->get()->toArray();
    }

    protected function getDepartmentsProperty()
    {
        $query = Department::with('building');
        
        // Only show departments of selected building
        if ($this->buildingId) {
            $query->where('building_id', $this->buildingId);
        }
        
        if ($this->searchDepartment) {
            $query->where('nama_jurusan', 'like', "%{$this->searchDepartment}%");
        }
        
        return $query->orderBy('nama_jurusan')->limit(20)->get()->toArray();
    }

    protected function getRoomsProperty()
    {
        $query = Room::with('department');
        
        // Only show rooms of selected department
        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }
        
        if ($this->searchRoom) {
            $query->where('nama_ruangan', 'like', "%{$this->searchRoom}%");
        }
        
        return $query->orderBy('nama_ruangan')->limit(20)->get()->toArray();
    }

    public function selectBuilding(int $id): void
    {
        $this->buildingId = $id;
        $this->searchBuilding = '';
        $this->resetDependentData();
        $this->dispatch('location-filter-updated', [
            'building_id' => $id,
            'department_id' => $this->departmentId,
            'room_id' => $this->roomId,
        ]);
    }

    public function selectDepartment(int $id): void
    {
        $this->departmentId = $id;
        $this->searchDepartment = '';
        $this->roomId = null;
        $this->searchRoom = '';
        $this->dispatch('location-filter-updated', [
            'building_id' => $this->buildingId,
            'department_id' => $id,
            'room_id' => $this->roomId,
        ]);
    }

    public function selectRoom(int $id): void
    {
        $this->roomId = $id;
        $this->searchRoom = '';
        $this->dispatch('location-filter-updated', [
            'building_id' => $this->buildingId,
            'department_id' => $this->departmentId,
            'room_id' => $id,
        ]);
    }

    public function resetFilters(): void
    {
        $this->buildingId = null;
        $this->departmentId = null;
        $this->roomId = null;
        $this->searchBuilding = '';
        $this->searchDepartment = '';
        $this->searchRoom = '';
        $this->resetDependentData();
        
        $this->dispatch('location-filter-reset');
    }

    protected function resetDependentData(): void
    {
        // Clear all cached data
        $this->properties['buildings'] = null;
        $this->properties['departments'] = null;
        $this->properties['rooms'] = null;
    }

    public function render()
    {
        return view('livewire.lokasi-filter');
    }
}
