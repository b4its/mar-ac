<x-bauhaus.layout title="Registrasi Aset">
    <div class="w-full max-w-5xl">
        <div class="mb-8 flex items-center gap-4">
            <x-bauhaus.shape type="square" color="yellow" class="h-12 w-12" />
            <div>
                <h1 class="bauhaus-title text-2xl lg:text-4xl">Registrasi Aset</h1>
                <p class="mt-1 text-xs font-bold uppercase tracking-[0.25em] text-bauhaus-blue">Data alat & inventaris UPA.PP</p>
            </div>
        </div>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('aset.index') }}" class="bauhaus-card p-6 lg:p-8 mb-6">
            <div class="flex flex-col gap-4 sm:flex-row items-stretch">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari nama alat, kode alat, atau no. inventaris…"
                    class="bauhaus-input flex-1"
                >
                
                {{-- Department Filter (Optional) --}}
                @if(auth()->user()->hasRole('admin'))
                <select name="department_id" class="bauhaus-input flex-none min-w-[180px]">
                    <option value="">Semua Jurusan</option>
                    @foreach($departments ?? [] as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->nama_jurusan }}
                        </option>
                    @endforeach
                </select>
                @endif
                
                <button type="submit" class="bauhaus-btn bg-bauhaus-black px-8 text-white whitespace-nowrap">
                    Cari
                </button>
                
                {{-- Reset Button --}}
                @if(request()->hasAny(['q', 'department_id']))
                <a href="{{ route('aset.index') }}" class="bauhaus-btn bg-slate-200 px-5 text-slate-700 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 whitespace-nowrap">
                    ← Reset
                </a>
                @endif
            </div>
        </form>

        {{-- Results Summary --}}
        @if(!empty(request('q')))
        <div class="mb-4 text-sm text-slate-600 dark:text-slate-400">
            Menampilkan hasil pencarian untuk: <span class="font-semibold">{{ request('q') }}</span>
        </div>
        @endif

        {{-- Assets List --}}
        @if($assets->isEmpty())
            <div class="mt-8 flex items-center gap-4 border border-bauhaus-red bg-bauhaus-paper p-8">
                <x-bauhaus.shape type="triangle" color="red" class="h-10 w-10" />
                <p class="font-display text-sm uppercase tracking-widest">Tidak ada aset ditemukan.</p>
            </div>
        @else
            <div class="border border-bauhaus-black bg-bauhaus-paper rounded-lg overflow-hidden mb-6">
                <ul class="divide-y divide-bauhaus-black">
                    @forelse($assets as $asset)
                        <li class="flex flex-col gap-4 p-5 hover:bg-bauhaus-yellow/30 transition-colors sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="font-semibold text-lg break-words">{{ $asset->nama_alat }}</p>
                                <p class="break-words text-xs font-bold uppercase tracking-widest text-bauhaus-blue">
                                    {{ $asset->kode_alat }} · {{ $asset->no_inventaris ?: 'No. Inv. belum terdaftar' }}
                                </p>
                                <p class="mt-1 text-xs text-bauhaus-ink line-clamp-1">
                                    {{ $asset->room?->building?->nama_gedung }} · 
                                    {{ $asset->room?->nama_ruangan ?? '-' }} · 
                                    {{ $asset->department?->nama_jurusan ?? '-' }}
                                </p>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-3 sm:justify-end">
                                <span class="border border-bauhaus-black px-3 py-1 font-display text-xs uppercase tracking-widest rounded {{ $asset->status === 'baik' ? 'bg-bauhaus-yellow' : 'bg-bauhaus-paper text-red-600' }}">
                                    {{ ucfirst($asset->status) }}
                                </span>
                                
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('aset.detail', $asset) }}" class="bauhaus-btn bg-bauhaus-paper px-4 py-2 text-xs whitespace-nowrap">
                                        Detail →
                                    </a>
                                    
                                    @if(auth()->user()->hasRole('admin'))
                                    <a href="{{ route('filament.admin.assets.edit', $asset) }}" class="bauhaus-btn bg-bauhaus-blue text-white px-4 py-2 text-xs whitespace-nowrap hover:bg-bauhaus-blue-dark">
                                        Edit
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="p-8 text-center">
                            <p class="text-slate-500 dark:text-slate-400">Tidak ada data aset yang ditampilkan.</p>
                        </li>
                    @endforelse
                </ul>
                
                {{-- Pagination Footer --}}
                @if($assets instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="bg-slate-50 dark:bg-slate-900 px-6 py-4 border-t border-bauhaus-black flex flex-col sm:flex-row items-center justify-between gap-4">
                    {{-- Previous Button --}}
                    @if($assets->onFirstPage())
                        <span class="bauhaus-btn bg-slate-200 text-slate-400 cursor-not-allowed dark:bg-slate-700 dark:border-slate-600">
                            ← Prev
                        </span>
                    @else
                        <a href="{{ $assets->previousPageUrl() }}" class="bauhaus-btn bg-white text-slate-700 hover:bg-blue-50 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-blue-900/20">
                            ← Prev
                        </a>
                    @endif
                    
                    {{-- Page Info --}}
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">
                        Halaman {{ $assets->currentPage() }} dari {{ $assets->lastPage() }}
                        (<strong>{{ $assets->total() }} total aset</strong>)
                    </p>
                    
                    {{-- Next Button --}}
                    @if($assets->hasMorePages())
                        <a href="{{ $assets->nextPageUrl() }}" class="bauhaus-btn bg-white text-slate-700 hover:bg-blue-50 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-blue-900/20">
                            Next →
                        </a>
                    @else
                        <span class="bauhaus-btn bg-slate-200 text-slate-400 cursor-not-allowed dark:bg-slate-700 dark:border-slate-600">
                            Next →
                        </span>
                    @endif
                </div>
                @endif
            </div>
        @endif

        {{-- Back Button --}}
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('welcome') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Beranda</a>
            
            @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('filament.admin.pages.dashboard') }}" class="bauhaus-btn bg-bauhaus-blue text-white px-5 py-2.5 text-xs">
                Panel Admin
            </a>
            @endif
        </div>
    </div>
</x-bauhaus.layout>
