<x-bauhaus.layout title="Lacak Status">
    <div class="w-full max-w-5xl">
        <div class="mb-8 flex items-center gap-4">
            <x-bauhaus.shape type="square" color="yellow" class="h-12 w-12" />
            <div class="min-w-0">
                <h1 class="bauhaus-title text-2xl lg:text-4xl">Lacak Status Laporan</h1>
                <p class="mt-1 text-xs font-bold uppercase tracking-[0.25em] text-bauhaus-blue">Masukkan nomor laporan atau cari berdasarkan kategori</p>
            </div>
        </div>

        {{-- Enhanced Search Form --}}
        <form method="GET" action="{{ route('laporan.status') }}" class="bauhaus-card p-6 lg:p-8 mb-6">
            <div class="flex flex-col gap-4 md:flex-row items-stretch">
                <input
                    type="text"
                    name="nomor"
                    value="{{ request('nomor') }}"
                    placeholder="Contoh: 001/UPA.PP/KRS/2026 atau KPRW..."
                    class="bauhaus-input flex-1"
                >
                
                {{-- Report Type Filter (Optional) --}}
                <select name="type" class="bauhaus-input flex-none min-w-[150px]">
                    <option value="">Semua Jenis Laporan</option>
                    <option value="damage" {{ request('type') == 'damage' ? 'selected' : '' }}>Laporan Kerusakan</option>
                    <option value="maintenance" {{ request('type') == 'maintenance' ? 'selected' : '' }}>Hasil Perawatan</option>
                    <option value="repair" {{ request('type') == 'repair' ? 'selected' : '' }}>Hasil Perbaikan</option>
                </select>
                
                <button type="submit" class="bauhaus-btn bg-bauhaus-black px-8 text-white whitespace-nowrap">
                    Cari
                </button>
                
                @if(request()->hasAny(['nomor', 'type']))
                <a href="{{ route('laporan.status') }}" class="bauhaus-btn bg-slate-200 px-5 text-slate-700 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 whitespace-nowrap">
                    ← Reset
                </a>
                @endif
            </div>
            
            {{-- Quick Filters --}}
            <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                <span class="font-display text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Filter cepat:</span>
                <a href="{{ route('laporan.status') . '?' . http_build_query(array_merge(request()->only(['q']), ['type' => 'damage'])) }}" 
                   class="inline-flex items-center gap-1 px-3 py-1 border border-bauhaus-black bg-bauhaus-paper text-xs uppercase tracking-widest hover:bg-bauhaus-yellow rounded transition-colors">
                    <span class="h-2 w-2 bg-blue-500 rounded-full"></span>
                    Kerusakan
                </a>
                <a href="{{ route('laporan.status') . '?' . http_build_query(array_merge(request()->only(['q']), ['type' => 'maintenance'])) }}" 
                   class="inline-flex items-center gap-1 px-3 py-1 border border-bauhaus-black bg-bauhaus-paper text-xs uppercase tracking-widest hover:bg-bauhaus-yellow rounded transition-colors">
                    <span class="h-2 w-2 bg-yellow-500 rounded-full"></span>
                    Perawatan
                </a>
                <a href="{{ route('laporan.status') . '?' . http_build_query(array_merge(request()->only(['q']), ['type' => 'repair'])) }}" 
                   class="inline-flex items-center gap-1 px-3 py-1 border border-bauhaus-black bg-bauhaus-paper text-xs uppercase tracking-widest hover:bg-bauhaus-yellow rounded transition-colors">
                    <span class="h-2 w-2 bg-red-500 rounded-full"></span>
                    Perbaikan
                </a>
            </div>
        </form>

        {{-- Results Summary --}}
        @if(!empty(request('nomor')))
        <div class="mb-4 text-sm text-slate-600 dark:text-slate-400">
            Pencarian untuk nomor: <span class="font-semibold">{{ request('nomor') }}</span>
        </div>
        @endif

        {{-- Content Area --}}
        @php
            $foundSomething = ($nomor !== '' && ! $maintenance && ! $damage && ! $repair);
        @endphp

        @if($foundSomething)
            <div class="mt-8 flex items-center gap-4 border border-bauhaus-red bg-bauhaus-paper p-6">
                <x-bauhaus.shape type="triangle" color="red" class="h-10 w-10" />
                <div>
                    <p class="font-display text-sm uppercase tracking-widest text-red-600">Laporan tidak ditemukan</p>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                        Nomor laporan <span class="font-semibold">{{ $nomor }}</span> tidak tersedia di database.
                        Silakan periksa kembali nomor laporan Anda.
                    </p>
                </div>
            </div>
        @endif

        {{-- Maintenance Report --}}
        @if($maintenance)
            <!-- Content here... -->
        @endif

        {{-- Damage Report --}}
        @if($damage)
            <!-- Content here... -->
        @endif

        {{-- Repair Report --}}
        @if($repair)
            <!-- Content here... -->
        @endif
        
        {{-- General Messages from Session --}}
        @if(session('success'))
        <div class="mt-8 rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-lg dark:border-emerald-900 dark:bg-emerald-950/40">
            <div class="flex items-start gap-3">
                <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
                    @if(session('nomor'))
                        <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-300">Nomor Laporan: <span class="font-mono">{{ session('nomor') }}</span></p>
                    @endif
                </div>
                <a href="{{ route('laporan.status') . '?nomor=' . urlencode(session('nomor', '')) }}" 
                   class="ml-auto bauhaus-btn bg-white text-emerald-700 px-3 py-1 text-xs hover:bg-emerald-100 dark:bg-slate-800 dark:text-emerald-300">
                    Lacak →
                </a>
            </div>
        </div>
        @endif

        {{-- Back Navigation --}}
        @if(!isset($maintenance) && !isset($damage) && !isset($repair))
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('laporan.saya') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Laporan Saya</a>
            <a href="{{ route('welcome') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Beranda</a>
        </div>
        @endif
    </div>
</x-bauhaus.layout>
