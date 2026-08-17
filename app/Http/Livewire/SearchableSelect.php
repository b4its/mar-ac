<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Asset;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;

class SearchableSelect extends Component
{
    public string $type; // 'asset', 'vendor'
    public string $name;
    public ?string $label = null;
    public string $placeholder = '';
    public int $selectedId = 0;
    public bool $required = false;
    
    // Search state
    public string $search = '';
    public bool $open = false;
    public bool $exactMatch = false;
    
    // Location lock state
    public bool $locationLocked = false;
    
    public function mount(string $type, string $name, ?string $label = null, string $placeholder = '', int $selectedId = 0, bool $required = false): void
    {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->selectedId = $selectedId;
        $this->required = $required;
        
        // Check if user has permission to create
        $userCanCreate = auth()->check() && auth()->user()->hasRole('admin');
        
        if (!$userCanCreate) {
            // For non-admin users, disable creating option entirely
            $this->placeholder .= ' (hanya dapat memilih yang sudah tersedia)';
        }
    }

    #[On('location-filter-updated')]
    public function updateLocation(array $filters): void
    {
        if (! isset($filters['building_id'], $filters['department_id'], $filters['room_id'])) {
            return;
        }
        
        $this->locationLocked = true;
        $this->locationLockMessage = 'Silakan pilih alat pada lokasi ini.';
    }

    protected function getOptionsQuery(): Builder
    {
        if ($this->type === 'asset') {
            $query = Asset::with(['room.building', 'department'])
                ->orderBy('nama_alat')->limit(20);
            
            if ($this->search) {
                $query->where(function(Builder $q) {
                    $q->where('nama_alat', 'like', "%{$this->search}%")
                      ->orWhere('kode_alat', 'like', "%{$this->search}%")
                      ->orWhere('no_inventaris', 'like', "%{$this->search}%");
                });
            }
            
            return $query;
        } elseif ($this->type === 'vendor') {
            $query = Vendor::where(function(Builder $q) {
                if ($this->search) {
                    $q->where('nama_vendor', 'like', "%{$this->search}%")
                      ->orWhere('kontak', 'like', "%{$this->search}%")
                      ->orWhere('telepon', 'like', "%{$this->search}%");
                }
            })
            ->orderBy('nama_vendor')->limit(20);
            
            return $query;
        }
        
        throw new \InvalidArgumentException("Unknown type: {$this->type}");
    }

    public function getOptionsProperty(): array
    {
        if ($this->search === '' && $this->locationLocked) {
            return [['id' => null, 'label' => 'Pilih gedung, lalu jurusan, lalu ruangan di atas dulu ya.']];
        }
        
        $options = $this->getOptionsQuery()
            ->pluck(fn($item) => [
                'id' => $item->id,
                'label' => $item->nama_alat ?: $item->nama_vendor,
                'condition' => null,
                'description' => null,
            ], fn($item) => $this->type === 'asset' ? $item->nama_alat : $item->nama_vendor)
            ->toArray();
        
        if ($options) {
            $first = reset($options);
            $this->exactMatch = strcasecmp($first['label'] ?? '', $this->search) === 0;
        } else {
            $this->exactMatch = false;
        }
        
        return $options;
    }

    public function selectOption(int $id): void
    {
        $this->selectedId = $id;
        $this->search = '';
        $this->open = false;
        $this->dispatch('option-selected', id: $id);
        $this->dispatchUp('updateSelectValue', ['name' => $this->name, 'value' => $id]);
    }

    public function render()
    {
        return view('livewire.searchable-select');
    }
}
