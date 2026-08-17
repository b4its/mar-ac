<?php

namespace App\Livewire;

use App\Enums\AssetCondition;
use App\Models\Asset;
use App\Models\Department;
use App\Models\Room;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;
use Livewire\Component;

class SearchableSelect extends Component
{
    public string $type = 'asset';

    public string $name = 'asset_id';

    public ?string $label = null;

    public ?int $selectedId = null;

    public string $selectedLabel = '';

    public string $search = '';

    public string $placeholder = 'Cari data...';

    public bool $required = false;

    public bool $open = false;

    public bool $creating = false;

    /**
     * Filter lokasi bercabang (gedung → jurusan → ruangan) dari LokasiFilter.
     * Hanya dipakai saat type = asset untuk mempersempit daftar aset.
     */
    public ?int $filterBuildingId = null;

    public ?int $filterDepartmentId = null;

    public ?int $filterRoomId = null;

    /**
     * Saat true (pemilih aset di form perawatan), alat baru dapat dipilih
     * setelah ruangan dipilih pada filter lokasi (gedung → jurusan → ruangan).
     */
    public bool $requireRoom = false;

    public string $newName = '';

    public string $newCode = '';

    public string $newInventory = '';

    public string $newAssetType = '';

    public string $newRoomId = '';

    public string $newDepartmentId = '';

    public string $newCapacity = '';

    public string $newBrand = '';

    public string $newYear = '';

    public string $newStatus = 'baik';

    public string $newLastMaintenanceDate = '';

    public string $newDescription = '';

    public string $newContact = '';

    public string $newPhone = '';

    public string $newAddress = '';

    public string $newVendorDescription = '';

    public function mount(
        string $type,
        string $name,
        ?string $label = null,
        ?int $selected = null,
        string $placeholder = 'Cari data...',
        bool $required = false,
        bool $requireRoom = false,
    ): void {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->selectedId = $selected;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->requireRoom = $requireRoom;

        if ($selected) {
            $record = $this->modelQuery()->find($selected);
            if ($record) {
                $this->selectedLabel = $this->formatLabel($record);
                $this->search = $this->selectedLabel;
            }
        }
    }

    public function updatedSearch(): void
    {
        $this->open = true;
        $this->creating = false;

        if ($this->search !== $this->selectedLabel) {
            $this->selectedId = null;
        }
    }

    /**
     * Menerapkan filter lokasi bercabang (gedung → jurusan → ruangan) dari LokasiFilter.
     */
    #[On('lokasi-filter-changed')]
    public function applyLocationFilter($buildingId = null, $departmentId = null, $roomId = null): void
    {
        if ($this->type !== 'asset') {
            return;
        }

        $this->filterBuildingId = $buildingId ? (int) $buildingId : null;
        $this->filterDepartmentId = $departmentId ? (int) $departmentId : null;
        $this->filterRoomId = $roomId ? (int) $roomId : null;

        if ($this->requireRoom && ! $this->filterRoomId && $this->selectedId) {
            $this->selectedId = null;
            $this->selectedLabel = '';
            $this->search = '';

            return;
        }

        if ($this->selectedId) {
            $record = $this->modelQuery()->find($this->selectedId);

            if ($record && ! $this->assetMatchesFilters($record)) {
                $this->selectedId = null;
                $this->selectedLabel = '';
                $this->search = '';
            }
        }
    }

    public function selectOption(int $id): void
    {
        $record = $this->modelQuery()->findOrFail($id);

        $this->selectedId = $record->id;
        $this->selectedLabel = $this->formatLabel($record);
        $this->search = $this->selectedLabel;
        $this->open = false;
        $this->creating = false;
    }

    public function startCreate(): void
    {
        $this->creating = true;
        $this->open = true;
        $this->newName = $this->search;
    }

    public function cancelCreate(): void
    {
        $this->creating = false;
    }

    public function createOption(): void
    {
        if ($this->type === 'vendor') {
            $data = Validator::make([
                'nama_vendor' => $this->newName,
                'kontak' => $this->newContact,
                'telepon' => $this->newPhone,
                'alamat' => $this->newAddress,
                'keterangan' => $this->newVendorDescription,
            ], [
                'nama_vendor' => ['required', 'string', 'max:255'],
                'kontak' => ['nullable', 'string', 'max:255'],
                'telepon' => ['nullable', 'string', 'max:255'],
                'alamat' => ['nullable', 'string', 'max:2000'],
                'keterangan' => ['nullable', 'string', 'max:2000'],
            ])->validate();

            $record = Vendor::create($data);
        } else {
            $data = Validator::make([
                'nama_alat' => $this->newName,
                'jenis_alat' => $this->newAssetType,
                'kode_alat' => $this->newCode,
                'no_inventaris' => $this->newInventory,
                'room_id' => filled($this->newRoomId) ? (int) $this->newRoomId : null,
                'department_id' => filled($this->newDepartmentId) ? (int) $this->newDepartmentId : null,
                'kapasitas' => $this->newCapacity,
                'merk' => $this->newBrand,
                'tahun_pemakaian' => $this->newYear,
                'status' => $this->newStatus,
                'last_maintenance_date' => filled($this->newLastMaintenanceDate) ? $this->newLastMaintenanceDate : null,
                'keterangan' => $this->newDescription,
            ], [
                'nama_alat' => ['required', 'string', 'max:255'],
                'jenis_alat' => ['nullable', 'string', 'max:255'],
                'kode_alat' => ['nullable', 'string', 'max:255'],
                'no_inventaris' => ['nullable', 'string', 'max:255'],
                'room_id' => ['nullable', 'exists:rooms,id'],
                'department_id' => ['nullable', 'exists:departments,id'],
                'kapasitas' => ['nullable', 'string', 'max:255'],
                'merk' => ['nullable', 'string', 'max:255'],
                'tahun_pemakaian' => ['nullable', 'integer', 'min:1900', 'max:'.((int) now()->format('Y'))],
                'status' => ['required', 'in:baik,rusak_ringan,rusak_sedang,rusak_berat'],
                'last_maintenance_date' => ['nullable', 'date'],
                'keterangan' => ['nullable', 'string', 'max:2000'],
            ])->validate();

            $record = Asset::create($data);
        }

        $this->selectOption($record->id);
        $this->resetCreateFields();

        $this->dispatch('toast', type: 'success', title: 'Data ditambahkan', message: $this->selectedLabel.' berhasil ditambahkan.');
    }

    public function getOptionsProperty(): Collection
    {
        if ($this->type === 'asset' && $this->requireRoom && ! $this->filterRoomId) {
            return collect();
        }

        $search = trim($this->search);

        return $this->modelQuery()
            ->when($search !== '', function ($query) use ($search) {
                if ($this->type === 'vendor') {
                    $query->where(function ($query) use ($search) {
                        $query->where('nama_vendor', 'like', "%{$search}%")
                            ->orWhere('kontak', 'like', "%{$search}%")
                            ->orWhere('telepon', 'like', "%{$search}%");
                    });
                } else {
                    $query->where(function ($query) use ($search) {
                        $query->where('nama_alat', 'like', "%{$search}%")
                            ->orWhere('kode_alat', 'like', "%{$search}%")
                            ->orWhere('no_inventaris', 'like', "%{$search}%");
                    });
                }
            })
            ->when($this->type === 'asset', fn ($query) => $this->applyLocationFilters($query))
            ->limit(10)
            ->get()
            ->map(fn (Model $record): array => [
                'id' => $record->id,
                'label' => $this->formatLabel($record),
                'description' => $this->formatDescription($record),
            ]);
    }

    public function getExactMatchProperty(): bool
    {
        $needle = mb_strtolower(trim($this->search));

        if ($needle === '') {
            return true;
        }

        return $this->options->contains(fn (array $option): bool => mb_strtolower($option['label']) === $needle);
    }

    public function getLocationLockedProperty(): bool
    {
        return $this->type === 'asset' && $this->requireRoom && ! $this->filterRoomId;
    }

    public function getRoomOptionsProperty(): Collection
    {
        return Room::query()
            ->with('building')
            ->orderBy('nama_ruangan')
            ->get()
            ->map(fn (Room $room): array => [
                'id' => $room->id,
                'label' => collect([$room->nama_ruangan, $room->building?->nama_gedung])->filter()->implode(' - '),
            ]);
    }

    public function getDepartmentOptionsProperty(): Collection
    {
        return Department::query()
            ->orderBy('nama_jurusan')
            ->get()
            ->map(fn (Department $department): array => [
                'id' => $department->id,
                'label' => $department->nama_jurusan,
            ]);
    }

    public function getStatusOptionsProperty(): array
    {
        return collect(AssetCondition::cases())
            ->mapWithKeys(fn (AssetCondition $condition): array => [$condition->value => $condition->label()])
            ->all();
    }

    public function render(): View
    {
        return view('livewire.searchable-select');
    }

    private function modelQuery()
    {
        if ($this->type === 'vendor') {
            return Vendor::query()->orderBy('nama_vendor');
        }

        return Asset::query()->with(['room.building', 'department'])->orderBy('nama_alat');
    }

    /**
     * Mempersempit query aset berdasarkan filter lokasi bercabang yang aktif.
     */
    private function applyLocationFilters($query)
    {
        return $query
            ->when($this->filterBuildingId, fn ($query) => $query->whereHas('room.building', fn ($building) => $building->where('buildings.id', $this->filterBuildingId)))
            ->when($this->filterDepartmentId, fn ($query) => $query->where('department_id', $this->filterDepartmentId))
            ->when($this->filterRoomId, fn ($query) => $query->where('room_id', $this->filterRoomId));
    }

    private function assetMatchesFilters(Asset $asset): bool
    {
        if ($this->filterBuildingId && $asset->room?->building_id !== $this->filterBuildingId) {
            return false;
        }

        if ($this->filterDepartmentId && $asset->department_id !== $this->filterDepartmentId) {
            return false;
        }

        if ($this->filterRoomId && $asset->room_id !== $this->filterRoomId) {
            return false;
        }

        return true;
    }

    private function formatLabel(Model $record): string
    {
        if ($record instanceof Vendor) {
            return $record->nama_vendor;
        }

        return collect([$record->nama_alat, $record->kode_alat])->filter()->implode(' - ');
    }

    private function formatDescription(Model $record): string
    {
        if ($record instanceof Vendor) {
            return collect([$record->kontak, $record->telepon])->filter()->implode(' - ');
        }

        return collect([
            $record->no_inventaris,
            $record->room?->building?->nama_gedung,
            $record->room?->nama_ruangan,
            $record->department?->nama_jurusan,
        ])->filter()->implode(' - ');
    }

    private function resetCreateFields(): void
    {
        $this->creating = false;
        $this->newName = '';
        $this->newCode = '';
        $this->newInventory = '';
        $this->newAssetType = '';
        $this->newRoomId = '';
        $this->newDepartmentId = '';
        $this->newCapacity = '';
        $this->newBrand = '';
        $this->newYear = '';
        $this->newStatus = 'baik';
        $this->newLastMaintenanceDate = '';
        $this->newDescription = '';
        $this->newContact = '';
        $this->newPhone = '';
        $this->newAddress = '';
        $this->newVendorDescription = '';
    }
}
