<x-bauhaus.layout title="Detail Aset">
    <div class="w-full max-w-3xl">
        <div class="mb-8 flex items-center gap-4">
            <x-bauhaus.shape type="square" color="yellow" class="h-12 w-12" />
            <div>
                <h1 class="bauhaus-title text-2xl lg:text-4xl">{{ $asset->nama_alat }}</h1>
                <p class="mt-1 text-xs font-bold uppercase tracking-[0.25em] text-bauhaus-blue">{{ $asset->kode_alat }} · {{ $asset->no_inventaris }}</p>
            </div>
        </div>

        <div class="border border-bauhaus-black bg-bauhaus-paper">
            <div class="flex items-center gap-4 border-b border-bauhaus-black bg-bauhaus-blue p-5 text-white">
                <x-bauhaus.shape type="square" color="yellow" class="h-10 w-10" />
                <h2 class="bauhaus-title text-lg">Informasi Aset</h2>
            </div>
            <div class="grid gap-x-8 gap-y-4 p-6 text-sm sm:grid-cols-2">
                <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Jenis Alat</span><span class="font-semibold">{{ $asset->jenis_alat ?: '-' }}</span></div>
                <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Kapasitas</span><span class="font-semibold">{{ $asset->kapasitas ?: '-' }}</span></div>
                <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Merk</span><span class="font-semibold">{{ $asset->merk ?: '-' }}</span></div>
                <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Tahun Pemakaian</span><span class="font-semibold">{{ $asset->tahun_pemakaian ?: '-' }}</span></div>
                <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Lokasi</span><span class="font-semibold">{{ $asset->room?->building?->nama_gedung }} · {{ $asset->room?->nama_ruangan }}</span></div>
                <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Jurusan/Unit</span><span class="font-semibold">{{ $asset->department?->nama_jurusan ?: '-' }}</span></div>
                <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Kondisi</span><span class="font-semibold">{{ $asset->status }}</span></div>
                <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Perawatan Terakhir</span><span class="font-semibold">{{ $asset->last_maintenance_date?->translatedFormat('d M Y') ?: 'Belum ada' }}</span></div>
            </div>
            @if ($asset->keterangan)
                <div class="border-t border-bauhaus-black p-6">
                    <p class="mb-2 font-display text-xs uppercase tracking-widest">Keterangan</p>
                    <p class="text-sm text-bauhaus-ink">{{ $asset->keterangan }}</p>
                </div>
            @endif
        </div>

        @if ($jadwal->isNotEmpty())
            <div class="mt-6 border border-bauhaus-black bg-bauhaus-paper">
                <div class="flex items-center gap-4 border-b border-bauhaus-black bg-bauhaus-yellow p-4">
                    <x-bauhaus.shape type="circle-hole" color="red" class="h-8 w-8" />
                    <h2 class="bauhaus-title text-lg">Jadwal Pemeliharaan Mendatang</h2>
                </div>
                <ul class="divide-y divide-bauhaus-black">
                    @foreach ($jadwal as $item)
                        <li class="p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold">{{ $item->jenis_pekerjaan }}</p>
                                    <p class="text-xs font-bold uppercase tracking-widest text-bauhaus-blue">{{ $item->tanggal_jadwal->translatedFormat('d M Y') }}</p>
                                </div>
                                <span class="border border-bauhaus-black bg-bauhaus-paper px-3 py-1 font-display text-xs uppercase tracking-widest">Terjadwal</span>
                            </div>
                            @if ($item->catatan)
                                <p class="mt-3 border-l border-bauhaus-blue pl-3 text-xs text-bauhaus-ink">{{ $item->catatan }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-6 border border-bauhaus-black bg-bauhaus-paper">
            <div class="flex items-center gap-4 border-b border-bauhaus-black bg-bauhaus-blue p-4 text-white">
                <x-bauhaus.shape type="triangle" color="yellow" class="h-8 w-8" />
                <h2 class="bauhaus-title text-lg">Riwayat Penanganan Aset</h2>
            </div>
            @if ($riwayat->isEmpty())
                <p class="p-6 font-display text-sm uppercase tracking-widest text-bauhaus-ink">Belum ada riwayat penanganan untuk aset ini.</p>
            @else
                <ul class="divide-y divide-bauhaus-black">
                    @foreach ($riwayat as $row)
                        <li class="p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold">{{ $row['jenisLabel'] }} <span class="text-bauhaus-blue">· {{ $row['nomor'] }}</span></p>
                                    <p class="mt-1 text-xs text-bauhaus-ink">{{ $row['detail'] }} · {{ $row['tanggal']->translatedFormat('d M Y') }}</p>
                                </div>
                                <span class="border border-bauhaus-black px-3 py-1 font-display text-xs uppercase tracking-widest {{ in_array($row['status'], ['disetujui', 'selesai'], true) ? 'bg-bauhaus-yellow' : 'bg-bauhaus-paper' }}">
                                    {{ $row['statusLabel'] }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="mt-8 flex gap-3">
            <a href="{{ route('aset.index') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Registrasi Aset</a>
            <a href="{{ route('welcome') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Beranda</a>
        </div>
    </div>
</x-bauhaus.layout>