<div class="grid gap-4 md:grid-cols-3" data-lokasi-filter>
    {{-- Gedung --}}
    <div class="relative" wire:click.outside="$set('openBuilding', false)">
        <label class="mb-2 block font-display text-sm uppercase tracking-widest">
            1. Pilih Gedung
            @if ($buildingId)
                <span class="ml-1 font-bold normal-case text-emerald-600">✓</span>
            @endif
        </label>
        <div class="relative">
            <input
                type="search"
                wire:model.live.debounce.250ms="searchBuilding"
                wire:focus="$set('openBuilding', true)"
                placeholder="Pilih gedung..."
                autocomplete="off"
                class="bauhaus-input pr-11"
            >
            <button
                type="button"
                wire:click="$set('openBuilding', {{ $openBuilding ? 'false' : 'true' }})"
                class="absolute right-2 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-100"
                aria-label="Buka pilihan gedung"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </button>
            @if ($buildingId)
                <button
                    type="button"
                    wire:click="clearBuilding"
                    class="absolute right-9 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950"
                    aria-label="Hapus pilihan gedung"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        @if ($openBuilding)
            <div class="absolute z-30 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl shadow-slate-200/70 dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/30">
                <div class="max-h-72 overflow-y-auto py-1">
                    @forelse ($this->buildingOptions as $option)
                        <button
                            type="button"
                            wire:click="selectBuilding({{ $option['id'] }})"
                            class="block w-full px-4 py-3 text-left transition hover:bg-blue-50 dark:hover:bg-slate-800"
                        >
                            <span class="block text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $option['label'] }}</span>
                            @if ($option['description'])
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">{{ $option['description'] }}</span>
                            @endif
                        </button>
                    @empty
                        <div class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">Data tidak ditemukan.</div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    {{-- Jurusan --}}
    <div class="relative" wire:click.outside="$set('openDepartment', false)">
        <label class="mb-2 block font-display text-sm uppercase tracking-widest">
            2. Pilih Jurusan
            @if ($departmentId)
                <span class="ml-1 font-bold normal-case text-emerald-600">✓</span>
            @endif
        </label>
        <div class="relative">
            <input
                type="search"
                wire:model.live.debounce.250ms="searchDepartment"
                wire:focus="$set('openDepartment', true)"
                placeholder="{{ $this->departmentLocked ? 'Pilih gedung terlebih dahulu...' : 'Pilih jurusan...' }}"
                autocomplete="off"
                class="bauhaus-input pr-11"
                @if ($this->departmentLocked) disabled @endif
            >
            <button
                type="button"
                wire:click="$set('openDepartment', {{ $openDepartment ? 'false' : 'true' }})"
                class="absolute right-2 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-100"
                aria-label="Buka pilihan jurusan"
                @if ($this->departmentLocked) disabled @endif
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </button>
            @if ($departmentId)
                <button
                    type="button"
                    wire:click="clearDepartment"
                    class="absolute right-9 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950"
                    aria-label="Hapus pilihan jurusan"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        @if ($openDepartment && ! $this->departmentLocked)
            <div class="absolute z-30 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl shadow-slate-200/70 dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/30">
                <div class="max-h-72 overflow-y-auto py-1">
                    @forelse ($this->departmentOptions as $option)
                        <button
                            type="button"
                            wire:click="selectDepartment({{ $option['id'] }})"
                            class="block w-full px-4 py-3 text-left transition hover:bg-blue-50 dark:hover:bg-slate-800"
                        >
                            <span class="block text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $option['label'] }}</span>
                            @if ($option['description'])
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">{{ $option['description'] }}</span>
                            @endif
                        </button>
                    @empty
                        <div class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">Data tidak ditemukan.</div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    {{-- Ruangan --}}
    <div class="relative" wire:click.outside="$set('openRoom', false)">
        <label class="mb-2 block font-display text-sm uppercase tracking-widest">
            3. Pilih Ruangan
            @if ($roomId)
                <span class="ml-1 font-bold normal-case text-emerald-600">✓</span>
            @endif
        </label>
        <div class="relative">
            <input
                type="search"
                wire:model.live.debounce.250ms="searchRoom"
                wire:focus="$set('openRoom', true)"
                placeholder="{{ $this->roomLocked ? ($this->departmentLocked ? 'Pilih gedung terlebih dahulu...' : 'Pilih jurusan terlebih dahulu...') : 'Pilih ruangan...' }}"
                autocomplete="off"
                class="bauhaus-input pr-11"
                @if ($this->roomLocked) disabled @endif
            >
            <button
                type="button"
                wire:click="$set('openRoom', {{ $openRoom ? 'false' : 'true' }})"
                class="absolute right-2 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-100"
                aria-label="Buka pilihan ruangan"
                @if ($this->roomLocked) disabled @endif
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </button>
            @if ($roomId)
                <button
                    type="button"
                    wire:click="clearRoom"
                    class="absolute right-9 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-red-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950"
                    aria-label="Hapus pilihan ruangan"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        @if ($openRoom && ! $this->roomLocked)
            <div class="absolute z-30 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl shadow-slate-200/70 dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/30">
                <div class="max-h-72 overflow-y-auto py-1">
                    @forelse ($this->roomOptions as $option)
                        <button
                            type="button"
                            wire:click="selectRoom({{ $option['id'] }})"
                            class="block w-full px-4 py-3 text-left transition hover:bg-blue-50 dark:hover:bg-slate-800"
                        >
                            <span class="block text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $option['label'] }}</span>
                            @if ($option['description'])
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">{{ $option['description'] }}</span>
                            @endif
                        </button>
                    @empty
                        <div class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">Data tidak ditemukan.</div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>
