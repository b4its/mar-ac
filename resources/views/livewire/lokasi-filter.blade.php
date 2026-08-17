<div class="space-y-3">
    <p class="font-display text-xs uppercase tracking-widest text-slate-600 dark:text-slate-400 mb-2">Filter Lokasi</p>
    
    {{-- Gedung Select --}}
    <div class="relative" wire:click.outside="$set('searchBuilding', '')">
        <label class="mb-1 block font-display text-xs uppercase tracking-widest">Gedung</label>
        
        <input
            type="text"
            placeholder="Cari gedung..."
            value="{{ $searchBuilding }}"
            wire:model.live.debounce.250ms="searchBuilding"
            autocomplete="off"
            class="bauhaus-input pr-11"
        >
        
        <button
            type="button"
            wire:click="$entangle('openBuilding').toggle()"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-bauhaus-blue"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </button>
        
        @if($searchBuilding !== '' || ($openBuilding ?? false))
        <div class="absolute z-40 mt-1 w-full max-h-60 overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
            @forelse($buildings as $building)
                <button
                    type="button"
                    wire:click="selectBuilding({{ $building->id }})"
                    class="block w-full px-4 py-2 text-left transition hover:bg-blue-50 dark:hover:bg-slate-800"
                >
                    <span class="block text-sm font-semibold">{{ $building->nama_gedung }}</span>
                </button>
            @empty
                <div class="px-4 py-2 text-sm text-slate-500">Tidak ada gedung yang cocok.</div>
            @endforelse
            
            @if(empty($buildings) && empty($searchBuilding))
                <div class="px-4 py-2 text-sm text-slate-500">Tidak ada gedung tersedia.</div>
            @endif
        </div>
        @endif
    </div>
    
    {{-- Jurusan Select --}}
    <div class="relative" wire:click.outside="$set('searchDepartment', '')">
        <label class="mb-1 block font-display text-xs uppercase tracking-widest {{ !$buildingId ? 'text-red-500' : '' }}">Jurusan</label>
        
        <input
            type="text"
            placeholder="{{ $buildingId ? 'Cari jurusan...' : 'Pilih gedung terlebih dahulu' }}"
            value="{{ $searchDepartment }}"
            wire:model.live.debounce.250ms="searchDepartment"
            disabled="{{ !$buildingId }}"
            autocomplete="off"
            class="bauhaus-input pr-11 {{ !$buildingId ? 'cursor-not-allowed bg-slate-100 dark:bg-slate-800' : '' }}"
        >
        
        @if($buildingId)
        <button
            type="button"
            wire:click="$entangle('openDepartment').toggle()"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-bauhaus-blue"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </button>
        
        @if($searchDepartment !== '' || ($openDepartment ?? false))
        <div class="absolute z-40 mt-1 w-full max-h-60 overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
            @forelse($departments as $dept)
                <button
                    type="button"
                    wire:click="selectDepartment({{ $dept->id }})"
                    class="block w-full px-4 py-2 text-left transition hover:bg-blue-50 dark:hover:bg-slate-800"
                >
                    <span class="block text-sm font-semibold">{{ $dept->nama_jurusan }}</span>
                    <span class="block text-xs text-slate-500">{{ $dept->building?->nama_gedung }}</span>
                </button>
            @empty
                <div class="px-4 py-2 text-sm text-slate-500">Tidak ada jurusan yang cocok.</div>
            @endforelse
            
            @if(empty($departments) && empty($searchDepartment))
                <div class="px-4 py-2 text-sm text-slate-500">Belum ada jurusan untuk gedung ini.</div>
            @endif
        </div>
        @endif
        @endif
    </div>
    
    {{-- Ruangan Select --}}
    <div class="relative" wire:click.outside="$set('searchRoom', '')">
        <label class="mb-1 block font-display text-xs uppercase tracking-widest {{ !$departmentId ? 'text-red-500' : '' }}">Ruangan</label>
        
        <input
            type="text"
            placeholder="{{ $departmentId ? 'Cari ruangan...' : 'Pilih jurusan terlebih dahulu' }}"
            value="{{ $searchRoom }}"
            wire:model.live.debounce.250ms="searchRoom"
            disabled="{{ !$departmentId }}"
            autocomplete="off"
            class="bauhaus-input pr-11 {{ !$departmentId ? 'cursor-not-allowed bg-slate-100 dark:bg-slate-800' : '' }}"
        >
        
        @if($departmentId)
        <button
            type="button"
            wire:click="$entangle('openRoom').toggle()"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-bauhaus-blue"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </button>
        
        @if($searchRoom !== '' || ($openRoom ?? false))
        <div class="absolute z-40 mt-1 w-full max-h-60 overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
            @forelse($rooms as $room)
                <button
                    type="button"
                    wire:click="selectRoom({{ $room->id }})"
                    class="block w-full px-4 py-2 text-left transition hover:bg-blue-50 dark:hover:bg-slate-800"
                >
                    <span class="block text-sm font-semibold">{{ $room->nama_ruangan }}</span>
                    <span class="block text-xs text-slate-500">
                        {{ $room->department?->nama_jurusan }} · 
                        {{ $room->department?->building?->nama_gedung }}
                    </span>
                </button>
            @empty
                <div class="px-4 py-2 text-sm text-slate-500">Tidak ada ruangan yang cocok.</div>
            @endforelse
            
            @if(empty($rooms) && empty($searchRoom))
                <div class="px-4 py-2 text-sm text-slate-500">Belum ada ruangan di jurusan ini.</div>
            @endif
        </div>
        @endif
        @endif
    </div>
    
    {{-- Selected Status & Reset Button --}}
    @if($buildingId || $departmentId || $roomId)
    <div class="mt-2 p-3 rounded-lg bg-blue-50 border border-blue-200 dark:bg-blue-950/40 dark:border-blue-900">
        <p class="text-xs font-semibold text-blue-800 dark:text-blue-200 mb-1">Lokasi Terpilih:</p>
        <ul class="text-xs space-y-1 text-blue-700 dark:text-blue-300">
            @if($buildingId)
            <li>• {{ Building::find($buildingId)?->nama_gedung }}</li>
            @endif
            @if($departmentId)
            <li>• {{ Department::find($departmentId)?->nama_jurusan }} · {{ Building::find(Building::where('id', Department::find($departmentId)->building_id)->id)->nama_gedung ?? '' }}</li>
            @endif
            @if($roomId)
            <li>• {{ Room::find($roomId)?->nama_ruangan }}</li>
            @endif
        </ul>
        <button type="button" wire:click="resetFilters" class="mt-2 text-xs text-red-600 hover:text-red-700 underline">
            ← Reset Filter
        </button>
    </div>
    @endif
</div>
