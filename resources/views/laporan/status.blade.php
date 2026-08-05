<x-bauhaus.layout title="Lacak Status">
    <div class="w-full max-w-3xl">
        <div class="mb-8 flex items-center gap-4">
            <x-bauhaus.shape type="square" color="yellow" class="h-12 w-12" />
            <div>
                <h1 class="bauhaus-title text-2xl lg:text-4xl">Lacak Status Laporan</h1>
                <p class="mt-1 text-xs font-bold uppercase tracking-[0.25em] text-bauhaus-blue">Masukkan nomor laporan</p>
            </div>
        </div>

        <form method="GET" action="{{ route('laporan.status') }}" class="bauhaus-card p-6 lg:p-8">
            <div class="flex flex-col gap-4 sm:flex-row">
                <input
                    type="text"
                    name="nomor"
                    value="{{ $nomor }}"
                    placeholder="Contoh: 001/UPA.PP/KSR/2026"
                    class="bauhaus-input flex-1"
                >
                <button type="submit" class="bauhaus-btn bg-bauhaus-black px-8 text-white">
                    Cari
                </button>
            </div>
        </form>

        @if ($nomor !== '' && ! $maintenance && ! $damage && ! $repair)
            <div class="mt-8 flex items-center gap-4 border border-bauhaus-red bg-bauhaus-paper p-6">
                <x-bauhaus.shape type="triangle" color="red" class="h-10 w-10" />
                <p class="font-display text-sm uppercase tracking-widest">Laporan dengan nomor <span class="text-red-600">{{ $nomor }}</span> tidak ditemukan.</p>
            </div>
        @endif

        @if ($maintenance)
            <div class="mt-8 border border-bauhaus-black bg-bauhaus-paper">
                <div class="flex items-center gap-4 border-b border-bauhaus-black bg-bauhaus-yellow p-5">
                    <x-bauhaus.shape type="circle-hole" color="red" class="h-10 w-10" />
                    <div>
                        <h2 class="bauhaus-title text-lg lg:text-xl">Kartu Pelaporan Hasil Perawatan</h2>
                        <p class="text-xs font-bold uppercase tracking-widest">{{ $maintenance->nomor_laporan }}</p>
                    </div>
                </div>
                <div class="grid gap-x-8 gap-y-4 p-6 text-sm sm:grid-cols-2">
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Alat</span><span class="font-semibold">{{ $maintenance->asset->nama_alat }}</span></div>
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Jenis Pekerjaan</span><span class="font-semibold">{{ $maintenance->jenis_pekerjaan }}</span></div>
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Tanggal Pelaksanaan</span><span class="font-semibold">{{ $maintenance->tanggal_pelaksanaan?->translatedFormat('d M Y') ?: '-' }}</span></div>
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Pelapor</span><span class="font-semibold">{{ $maintenance->pelaporUser?->name ?: '-' }}</span></div>
                </div>
                @if ($maintenance->attachments->isNotEmpty())
                    <div class="border-t border-bauhaus-black p-6">
                        <p class="mb-4 font-display text-xs uppercase tracking-widest">Lampiran Foto</p>
                        <div class="grid gap-4 md:grid-cols-3">
                            @foreach ($maintenance->attachments as $attachment)
                                <figure class="border border-bauhaus-black bg-bauhaus-paper p-2">
                                    <img src="{{ $attachment->url() }}" alt="{{ $attachment->caption }}" class="h-40 w-full object-cover">
                                    <figcaption class="mt-2 font-display text-xs uppercase tracking-widest">{{ $attachment->caption }}</figcaption>
                                </figure>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="border-t border-bauhaus-black p-6">
                    <p class="mb-3 font-display text-xs uppercase tracking-widest">Status</p>
                    <div class="flex flex-wrap items-center gap-3">
                        @php
                            $steps = ['diajukan' => 'Diajukan', 'diverifikasi' => 'Diverifikasi', 'disetujui' => 'Disetujui'];
                            $idx = array_search($maintenance->status, array_keys($steps));
                        @endphp
                        @foreach ($steps as $key => $label)
                            <span class="border px-3 py-1.5 font-display text-xs uppercase tracking-widest {{ $maintenance->status === $key ? 'border-bauhaus-red bg-bauhaus-blue text-white' : ($idx !== false && array_search($key, array_keys($steps)) < $idx ? 'border-bauhaus-black bg-bauhaus-yellow' : 'border-bauhaus-black bg-bauhaus-paper text-bauhaus-ink') }}">
                                {{ $label }}
                            </span>
                            @if (! $loop->last)<span class="font-bold">→</span>@endif
                        @endforeach
                        @if (in_array($maintenance->status, ['ditolak', 'revisi']))
                            <span class="border border-bauhaus-black bg-bauhaus-paper px-3 py-1.5 font-display text-xs uppercase tracking-widest text-red-600">{{ \App\Enums\ReportStatus::from($maintenance->status)->label() }}</span>
                        @endif
                    </div>
                    @if ($maintenance->catatan)
                        <p class="mt-4 border-l border-bauhaus-blue pl-3 text-sm text-bauhaus-ink">{{ $maintenance->catatan }}</p>
                    @endif
                    <div class="mt-5">
                        <a href="{{ route('laporan.pdf.perawatan', $maintenance) }}" class="bauhaus-btn bg-bauhaus-black px-5 py-2.5 text-xs text-white">Preview PDF Formulir</a>
                    </div>
                </div>
            </div>
        @endif

        @if ($damage)
            <div class="mt-8 border border-bauhaus-black bg-bauhaus-paper">
                <div class="flex items-center gap-4 border-b border-bauhaus-black bg-bauhaus-blue p-5 text-white">
                    <x-bauhaus.shape type="triangle" color="yellow" class="h-10 w-10" />
                    <div>
                        <h2 class="bauhaus-title text-lg lg:text-xl">Laporan Kerusakan</h2>
                        <p class="text-xs font-bold uppercase tracking-widest">{{ $damage->nomor_laporan }}</p>
                    </div>
                </div>
                <div class="grid gap-x-8 gap-y-4 p-6 text-sm sm:grid-cols-2">
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Alat</span><span class="font-semibold">{{ $damage->asset->nama_alat }}</span></div>
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Tingkat Kerusakan</span><span class="font-semibold">{{ \App\Enums\DamageLevel::from($damage->tingkat_kerusakan)->label() }}</span></div>
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Jenis Kerusakan</span><span class="font-semibold">{{ $damage->jenis_kerusakan }}</span></div>
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Tanggal Laporan</span><span class="font-semibold">{{ $damage->tanggal_laporan->translatedFormat('d M Y') }}</span></div>
                </div>
                @if ($damage->attachments->isNotEmpty())
                    <div class="border-t border-bauhaus-black p-6">
                        <p class="mb-4 font-display text-xs uppercase tracking-widest">Lampiran Foto</p>
                        <div class="grid gap-4 md:grid-cols-3">
                            @foreach ($damage->attachments as $attachment)
                                <figure class="border border-bauhaus-black bg-bauhaus-paper p-2">
                                    <img src="{{ $attachment->url() }}" alt="{{ $attachment->caption }}" class="h-40 w-full object-cover">
                                    <figcaption class="mt-2 font-display text-xs uppercase tracking-widest">{{ $attachment->caption }}</figcaption>
                                </figure>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="border-t border-bauhaus-black p-6">
                    <p class="mb-3 font-display text-xs uppercase tracking-widest">Status</p>
                    <span class="inline-block border border-bauhaus-black bg-bauhaus-yellow px-4 py-2 font-display text-sm uppercase tracking-widest">
                        {{ \App\Enums\DamageReportStatus::from($damage->status)->label() }}
                    </span>
                    @if ($damage->catatan)
                        <p class="mt-4 border-l border-bauhaus-red pl-3 text-sm text-bauhaus-ink">{{ $damage->catatan }}</p>
                    @endif

                    <div class="mt-5 flex flex-wrap gap-3">
                        @if ($damage->status === \App\Enums\DamageReportStatus::Disetujui->value && $damage->pelapor_user_id === auth()->id() && ! $damage->repairReport)
                            <a href="{{ route('laporan.perbaikan', $damage) }}" class="bauhaus-btn bg-bauhaus-blue px-5 py-2.5 text-xs text-white">Kirim Laporan Perbaikan</a>
                        @endif
                        <a href="{{ route('laporan.pdf.kerusakan', $damage) }}" class="bauhaus-btn bg-bauhaus-black px-5 py-2.5 text-xs text-white">Preview PDF Formulir</a>
                    </div>
                </div>
                @if ($damage->repairReport)
                    @php($linkedRepair = $damage->repairReport)
                    <div class="border-t border-bauhaus-black p-6">
                        <p class="mb-4 font-display text-xs uppercase tracking-widest">Laporan Hasil Perbaikan</p>
                        <div class="grid gap-x-8 gap-y-4 text-sm sm:grid-cols-2">
                            <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Nomor Perbaikan</span><span class="font-semibold">{{ $linkedRepair->nomor_laporan }}</span></div>
                            <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Status</span><span class="font-semibold">{{ \App\Enums\RepairStatus::from($linkedRepair->status)->label() }}</span></div>
                            <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Jenis Pekerjaan</span><span class="font-semibold">{{ $linkedRepair->jenis_pekerjaan }}</span></div>
                            <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Tanggal</span><span class="font-semibold">{{ $linkedRepair->tanggal_pelaksanaan?->translatedFormat('d M Y') ?: '-' }}</span></div>
                        </div>
                        @if ($linkedRepair->status === \App\Enums\RepairStatus::Revisi->value && $damage->pelapor_user_id === auth()->id())
                            <div class="mt-5">
                                <a href="{{ route('laporan.perbaikan', $damage) }}" class="bauhaus-btn bg-bauhaus-yellow px-5 py-2.5 text-xs">Perbaiki Revisi</a>
                            </div>
                        @endif
                        @if ($linkedRepair->attachments->isNotEmpty())
                            <div class="mt-5 grid gap-4 md:grid-cols-3">
                                @foreach ($linkedRepair->attachments as $attachment)
                                    <figure class="border border-bauhaus-black bg-bauhaus-paper p-2">
                                        <img src="{{ $attachment->url() }}" alt="{{ $attachment->caption }}" class="h-40 w-full object-cover">
                                        <figcaption class="mt-2 text-xs font-semibold">{{ $attachment->caption }}</figcaption>
                                    </figure>
                                @endforeach
                            </div>
                        @endif
                        <div class="mt-5">
                            <a href="{{ route('laporan.pdf.perbaikan', $linkedRepair) }}" class="bauhaus-btn bg-bauhaus-black px-5 py-2.5 text-xs text-white">Preview PDF Formulir</a>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if ($repair && ! $damage)
            <div class="mt-8 border border-bauhaus-black bg-bauhaus-paper">
                <div class="flex items-center gap-4 border-b border-bauhaus-black bg-bauhaus-blue p-5 text-white">
                    <x-bauhaus.shape type="triangle" color="yellow" class="h-10 w-10" />
                    <div>
                        <h2 class="bauhaus-title text-lg lg:text-xl">Laporan Hasil Perbaikan</h2>
                        <p class="text-xs font-bold uppercase tracking-widest">{{ $repair->nomor_laporan }}</p>
                    </div>
                </div>
                <div class="grid gap-x-8 gap-y-4 p-6 text-sm sm:grid-cols-2">
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Laporan Kerusakan</span><span class="font-semibold">{{ $repair->damageReport?->nomor_laporan ?: '-' }}</span></div>
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Status</span><span class="font-semibold">{{ \App\Enums\RepairStatus::from($repair->status)->label() }}</span></div>
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Aset</span><span class="font-semibold">{{ $repair->asset->nama_alat }}</span></div>
                    <div><span class="block font-display text-xs uppercase tracking-widest text-bauhaus-ink">Jenis Pekerjaan</span><span class="font-semibold">{{ $repair->jenis_pekerjaan }}</span></div>
                </div>
                @if ($repair->attachments->isNotEmpty())
                    <div class="border-t border-bauhaus-black p-6">
                        <p class="mb-4 font-display text-xs uppercase tracking-widest">Lampiran Foto</p>
                        <div class="grid gap-4 md:grid-cols-3">
                            @foreach ($repair->attachments as $attachment)
                                <figure class="border border-bauhaus-black bg-bauhaus-paper p-2">
                                    <img src="{{ $attachment->url() }}" alt="{{ $attachment->caption }}" class="h-40 w-full object-cover">
                                    <figcaption class="mt-2 text-xs font-semibold">{{ $attachment->caption }}</figcaption>
                                </figure>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="border-t border-bauhaus-black p-6">
                    <a href="{{ route('laporan.pdf.perbaikan', $repair) }}" class="bauhaus-btn bg-bauhaus-black px-5 py-2.5 text-xs text-white">Preview PDF Formulir</a>
                </div>
            </div>
        @endif

        <div class="mt-8 flex gap-3">
            <a href="{{ route('laporan.saya') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Laporan Saya</a>
            <a href="{{ route('welcome') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Beranda</a>
        </div>
    </div>
</x-bauhaus.layout>
