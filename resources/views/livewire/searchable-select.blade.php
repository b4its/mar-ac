<div class="relative" wire:click.outside="$set('open', false)">
    @if ($label)
        <label class="mb-2 block font-display text-sm uppercase tracking-widest">{{ $label }}</label>
    @endif

    <input type="hidden" name="{{ $name }}" value="{{ $selectedId }}" @if ($required) required @endif>

    <div class="relative">
        <input
            type="search"
            wire:model.live.debounce.250ms="search"
            wire:focus="$set('open', true)"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            class="bauhaus-input pr-11 {{ $locationLocked ? 'cursor-not-allowed bg-slate-100 dark:bg-slate-800' : '' }}"
            @if ($required) aria-required="true" @endif
            @if ($locationLocked) disabled @endif
        >
        <button
            type="button"
            wire:click="$set('open', {{ $open ? 'false' : 'true' }})"
            class="absolute right-2 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-100"
            aria-label="Buka pilihan"
            @if ($locationLocked) disabled @endif
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m6 9 6 6 6-6" />
            </svg>
        </button>
    </div>

    @if ($open && ! $locationLocked)
        <div class="absolute z-30 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl shadow-slate-200/70 dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/30">
            @if ($locationLocked)
                <div class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">
                    Pilih gedung, lalu jurusan, lalu ruangan di atas dulu ya. Daftar alat akan muncul di sini.
                </div>
            @elseif (!auth()->check() || !auth()->user()->hasRole('admin'))
                {{-- Show all options without "Create New" for non-admin users --}}
                <div class="max-h-72 overflow-y-auto py-1">
                    @forelse ($options as $option)
                        <button
                            type="button"
                            wire:click="selectOption({{ $option['id'] }})"
                            class="block w-full px-4 py-3 text-left transition hover:bg-blue-50 dark:hover:bg-slate-800"
                        >
                            <span class="block text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $option['label'] }}</span>
                            @if ($option['condition'])
                                <span class="mt-0.5 block text-xs font-semibold {{ $option['condition']['textClass'] }}">{{ $option['condition']['label'] }}</span>
                                @if ($option['condition']['riwayat'])
                                    <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">{{ $option['condition']['riwayat'] }}</span>
                                @endif
                            @endif
                            @if ($option['description'])
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">{{ $option['description'] }}</span>
                            @endif
                        </button>
                    @empty
                        <div class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">Data tidak ditemukan.</div>
                    @endforelse
                </div>
            @else
                {{-- Admins can see all options plus "Create New" --}}
                <div class="max-h-72 overflow-y-auto py-1">
                    @forelse ($options as $option)
                        <button
                            type="button"
                            wire:click="selectOption({{ $option['id'] }})"
                            class="block w-full px-4 py-3 text-left transition hover:bg-blue-50 dark:hover:bg-slate-800"
                        >
                            <span class="block text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $option['label'] }}</span>
                            @if ($option['condition'])
                                <span class="mt-0.5 block text-xs font-semibold {{ $option['condition']['textClass'] }}">{{ $option['condition']['label'] }}</span>
                                @if ($option['condition']['riwayat'])
                                    <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">{{ $option['condition']['riwayat'] }}</span>
                                @endif
                            @endif
                            @if ($option['description'])
                                <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">{{ $option['description'] }}</span>
                            @endif
                        </button>
                    @empty
                        <div class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">Data tidak ditemukan.</div>
                    @endforelse
                </div>

                @php
                    $addLabel = trim($search) !== '' && ! $this->exactMatch ? '"'.$search.'"' : '';
                @endphp
                <div class="border-t border-slate-200 p-3 dark:border-slate-700">
                    <button type="button" wire:click="startCreate" class="bauhaus-btn w-full bg-bauhaus-blue px-4 py-2 text-xs text-white hover:bg-bauhaus-blue-dark">
                        + Tambah {{ $type === 'vendor' ? 'Vendor' : 'Aset' }} Baru{{ $addLabel }}
                    </button>
                </div>
            @endif
        </div>
    @endif

    @if ($locationLocked)
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pilih lokasi alat di atas dulu: gedung → jurusan → ruangan.</p>
    @elseif ($required && ! $selectedId && trim($search) !== '')
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pilih data dari daftar yang tersedia.</p>
    @endif
</div>
