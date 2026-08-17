<x-bauhaus.layout title="Registrasi Aset">
    <div class="w-full max-w-3xl">
        <div class="mb-8 flex items-center gap-4">
            <x-bauhaus.shape type="square" color="yellow" class="h-12 w-12" />
            <div>
                <h1 class="bauhaus-title text-2xl lg:text-4xl">Registrasi Aset</h1>
                <p class="mt-1 text-xs font-bold uppercase tracking-[0.25em] text-bauhaus-blue">Data alat &amp; inventaris UPA.PP</p>
            </div>
        </div>

        <form method="GET" action="{{ route('aset.index') }}" class="bauhaus-card p-6 lg:p-8">
            <div class="flex flex-col gap-4 sm:flex-row">
                <input
                    type="text"
                    name="q"
                    value="{{ $q }}"
                    placeholder="Cari nama alat, kode alat, atau no. inventaris…"
                    class="bauhaus-input flex-1"
                >
                <button type="submit" class="bauhaus-btn bg-bauhaus-black px-8 text-white">Cari</button>
            </div>
        </form>

        @if ($assets->isEmpty())
            <div class="mt-8 flex items-center gap-4 border border-bauhaus-black bg-bauhaus-paper p-8">
                <x-bauhaus.shape type="circle-hole" color="red" class="h-10 w-10" />
                <p class="font-display text-sm uppercase tracking-widest">Tidak ada aset ditemukan.</p>
            </div>
        @else
            <div class="mt-8 border border-bauhaus-black bg-bauhaus-paper">
                <ul class="divide-y divide-bauhaus-black">
                    @foreach ($assets as $asset)
                        <li class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="font-semibold">{{ $asset->nama_alat }}</p>
                                <p class="break-words text-xs font-bold uppercase tracking-widest text-bauhaus-blue">
                                    {{ $asset->kode_alat }} · {{ $asset->no_inventaris }}
                                </p>
                                <p class="mt-1 text-xs text-bauhaus-ink">
                                    {{ $asset->room?->building?->nama_gedung }} · {{ $asset->room?->nama_ruangan }} · {{ $asset->department?->nama_jurusan }}
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 sm:justify-end">
                                <span class="border border-bauhaus-black px-3 py-1 font-display text-xs uppercase tracking-widest {{ $asset->status === 'baik' ? 'bg-bauhaus-yellow' : 'bg-bauhaus-paper text-red-600' }}">
                                    {{ $asset->status }}
                                </span>
                                <a href="{{ route('aset.detail', $asset) }}" class="bauhaus-btn bg-bauhaus-paper px-4 py-2 text-xs">Detail →</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('welcome') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Beranda</a>
        </div>
    </div>
</x-bauhaus.layout>
