<x-bauhaus.layout title="Laporan Saya">
    <div class="w-full max-w-3xl">
        <div class="mb-8 flex items-center gap-4">
            <x-bauhaus.shape type="square" color="yellow" class="h-12 w-12" />
            <div>
                <h1 class="bauhaus-title text-2xl lg:text-4xl">Laporan Saya</h1>
                <p class="mt-1 text-xs font-bold uppercase tracking-[0.25em] text-bauhaus-blue">{{ auth()->user()->name }}</p>
            </div>
        </div>

        @if ($damages->isEmpty() && $maintenances->isEmpty() && $repairs->isEmpty())
            <div class="flex items-center gap-4 border border-bauhaus-black bg-bauhaus-paper p-8">
                <x-bauhaus.shape type="circle-hole" color="red" class="h-12 w-12" />
                <p class="font-display text-sm uppercase tracking-widest">Belum ada laporan yang Anda kirim.</p>
            </div>
        @endif

        @if ($maintenances->isNotEmpty())
            <section class="mt-6 border border-bauhaus-black bg-bauhaus-paper">
                <div class="flex items-center gap-4 border-b border-bauhaus-black bg-bauhaus-yellow p-4">
                    <x-bauhaus.shape type="circle-hole" color="red" class="h-8 w-8" />
                    <h2 class="bauhaus-title text-lg">Kartu Pelaporan Hasil Perawatan</h2>
                </div>
                <ul class="divide-y divide-bauhaus-black">
                    @foreach ($maintenances as $report)
                        <li class="p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold">{{ $report->asset->nama_alat }}</p>
                                    <p class="text-xs font-bold uppercase tracking-widest text-bauhaus-blue">{{ $report->nomor_laporan }} · {{ $report->jenis_pekerjaan }}</p>
                                    <p class="mt-1 text-xs text-bauhaus-ink">{{ $report->tanggal_pelaksanaan?->translatedFormat('d M Y') ?: '-' }}</p>
                                </div>
                                <span class="border border-bauhaus-black px-3 py-1 font-display text-xs uppercase tracking-widest {{ $report->status === 'disetujui' ? 'bg-bauhaus-yellow' : ($report->status === 'ditolak' ? 'bg-bauhaus-paper text-red-600' : 'bg-bauhaus-paper') }}">
                                    {{ \App\Enums\ReportStatus::from($report->status)->label() }}
                                </span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-3">
                                <a href="{{ route('laporan.status', ['nomor' => $report->nomor_laporan]) }}" class="bauhaus-btn bg-bauhaus-paper px-4 py-2 text-xs">Lihat Detail</a>
                                <a href="{{ route('laporan.pdf.perawatan', $report) }}" class="bauhaus-btn bg-bauhaus-black px-4 py-2 text-xs text-white">Preview PDF Formulir</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($damages->isNotEmpty())
            <section class="mt-6 border border-bauhaus-black bg-bauhaus-paper">
                <div class="flex items-center gap-4 border-b border-bauhaus-black bg-bauhaus-blue p-4 text-white">
                    <x-bauhaus.shape type="triangle" color="yellow" class="h-8 w-8" />
                    <h2 class="bauhaus-title text-lg">Laporan Kerusakan</h2>
                </div>
                <ul class="divide-y divide-bauhaus-black">
                    @foreach ($damages as $report)
                        <li class="p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold">{{ $report->asset->nama_alat }}</p>
                                    <p class="text-xs font-bold uppercase tracking-widest text-bauhaus-blue">{{ $report->nomor_laporan }} · {{ $report->jenis_kerusakan }}</p>
                                    <p class="mt-1 text-xs text-bauhaus-ink">{{ $report->tanggal_laporan->translatedFormat('d M Y') }} · {{ \App\Enums\DamageLevel::from($report->tingkat_kerusakan)->label() }}</p>
                                </div>
                                <span class="border border-bauhaus-black px-3 py-1 font-display text-xs uppercase tracking-widest {{ $report->status === 'disetujui' ? 'bg-bauhaus-yellow' : ($report->status === 'ditolak' ? 'bg-bauhaus-paper text-red-600' : 'bg-bauhaus-paper') }}">
                                    {{ \App\Enums\DamageReportStatus::from($report->status)->label() }}
                                </span>
                            </div>
                            @if ($report->status === \App\Enums\DamageReportStatus::Disetujui->value && ! $report->repairReport)
                                <p class="mt-3 border-l border-bauhaus-blue pl-3 text-xs text-bauhaus-ink">Disetujui — lanjutkan dengan laporan hasil perbaikan.</p>
                            @elseif ($report->repairReport)
                                <p class="mt-3 border-l border-bauhaus-red pl-3 text-xs text-bauhaus-ink">
                                    Laporan perbaikan: {{ $report->repairReport->nomor_laporan }} · {{ \App\Enums\RepairStatus::from($report->repairReport->status)->label() }}
                                </p>
                            @endif
                            <div class="mt-4 flex flex-wrap gap-3">
                                @if ($report->status === \App\Enums\DamageReportStatus::Disetujui->value && ! $report->repairReport)
                                    <a href="{{ route('laporan.perbaikan', $report) }}" class="bauhaus-btn bg-bauhaus-blue px-4 py-2 text-xs text-white">Kirim Laporan Perbaikan</a>
                                @endif
                                <a href="{{ route('laporan.status', ['nomor' => $report->nomor_laporan]) }}" class="bauhaus-btn bg-bauhaus-paper px-4 py-2 text-xs">Lihat Detail</a>
                                <a href="{{ route('laporan.pdf.kerusakan', $report) }}" class="bauhaus-btn bg-bauhaus-black px-4 py-2 text-xs text-white">Preview PDF Formulir</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($repairs->isNotEmpty())
            <section class="mt-6 border border-bauhaus-black bg-bauhaus-paper">
                <div class="flex items-center gap-4 border-b border-bauhaus-black bg-bauhaus-blue p-4 text-white">
                    <x-bauhaus.shape type="square" color="yellow" class="h-8 w-8" />
                    <h2 class="bauhaus-title text-lg">Laporan Hasil Perbaikan</h2>
                </div>
                <ul class="divide-y divide-bauhaus-black">
                    @foreach ($repairs as $report)
                        <li class="p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold">{{ $report->asset->nama_alat }}</p>
                                    <p class="text-xs font-bold uppercase tracking-widest text-bauhaus-blue">{{ $report->nomor_laporan }} · {{ $report->jenis_pekerjaan }}</p>
                                    <p class="mt-1 text-xs text-bauhaus-ink">Kerusakan: {{ $report->damageReport?->nomor_laporan ?: '-' }} · {{ $report->tanggal_pelaksanaan?->translatedFormat('d M Y') ?: '-' }}</p>
                                </div>
                                <span class="border border-bauhaus-black px-3 py-1 font-display text-xs uppercase tracking-widest {{ $report->status === 'disetujui' ? 'bg-bauhaus-yellow' : ($report->status === 'ditolak' ? 'bg-bauhaus-paper text-red-600' : 'bg-bauhaus-paper') }}">
                                    {{ \App\Enums\RepairStatus::from($report->status)->label() }}
                                </span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-3">
                                @if ($report->status === \App\Enums\RepairStatus::Revisi->value && $report->damageReport)
                                    <a href="{{ route('laporan.perbaikan', $report->damageReport) }}" class="bauhaus-btn bg-bauhaus-yellow px-4 py-2 text-xs">Perbaiki Revisi</a>
                                @endif
                                <a href="{{ route('laporan.status', ['nomor' => $report->nomor_laporan]) }}" class="bauhaus-btn bg-bauhaus-paper px-4 py-2 text-xs">Lihat Detail</a>
                                <a href="{{ route('laporan.pdf.perbaikan', $report) }}" class="bauhaus-btn bg-bauhaus-black px-4 py-2 text-xs text-white">Preview PDF Formulir</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <div class="mt-8">
            <a href="{{ route('welcome') }}" class="bauhaus-btn bg-bauhaus-paper px-5 py-2.5 text-xs">← Kembali</a>
        </div>
    </div>
</x-bauhaus.layout>
