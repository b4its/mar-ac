<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $report->nomor_laporan }}</title>
    @include('pdf.partials.style')
</head>
<body>
    @php
        // Setup data untuk setiap alat/mesin dalam laporan
        $asset = $report->asset;
        $print = $report->print_fields ?? [];
        $field = fn (string $key, mixed $fallback = '-') => filled($print[$key] ?? null) ? $print[$key] : (filled($fallback) ? $fallback : '-');
        
        $sections = collect([
            [
                'nama_alat' => $field('nama_alat', $asset?->nama_alat),
                'no_inventaris' => $field('no_inventaris', $asset?->no_inventaris),
                'gedung' => $field('gedung', $asset?->room?->building?->nama_gedung),
                'kode_alat' => $field('kode_alat', $asset?->kode_alat),
                'lokasi' => $field('lokasi_alat', $field('nama_ruangan', $asset?->room?->nama_ruangan)),
                'jurusan' => $field('jurusan_unit', $field('jurusan', $asset?->department?->nama_jurusan)),
                'uraian' => $report->uraian_pekerjaan ?: $report->jenis_pekerjaan,
                'material' => $field('material_suku_cadang', ''),
                'kode_material' => $field('kode_material', '-'),
                'biaya' => (float) $report->biaya,
                'biaya_jasa' => (float) $report->biaya_jasa,
                'attachments' => $report->attachments->filter(fn ($a) => !str_ends_with($a->slot_key, '_2'))->values(),
                'tanggal_pelaksanaan' => $report->tanggal_pelaksanaan,
            ],
        ]);
        
        // Tambahkan items bagian kedua (alat kedua jika ada)
        foreach ($report->items as $item) {
            $sections->push([
                'nama_alat' => $item->asset?->nama_alat ?? '-',
                'no_inventaris' => $item->asset?->no_inventaris ?? '-',
                'gedung' => $item->asset?->room?->building?->nama_gedung ?? '-',
                'kode_alat' => $item->asset?->kode_alat ?? '-',
                'lokasi' => $item->asset?->room?->nama_ruangan ?? '-',
                'jurusan' => $item->asset?->department?->nama_jurusan ?? '-',
                'uraian' => $item->uraian_pekerjaan ?: $item->jenis_pekerjaan,
                'material' => '',
                'kode_material' => '-',
                'biaya' => (float) $item->biaya,
                'biaya_jasa' => (float) $item->biaya_jasa,
                'attachments' => $report->attachments->filter(fn ($a) => str_ends_with($a->slot_key, '_2'))->values(),
                'tanggal_pelaksanaan' => $item->tanggal_pelaksanaan ?? $report->tanggal_pelaksanaan,
            ]);
        }
        
        $multi = $sections->count() > 1;
    @endphp
    
    <style>
        .row-divider td { background: #f0f0f0; height: 4px; padding: 0 !important; border-left: 0 !important; border-right: 0 !important; }
    </style>

    {{-- Header dokumen --}}
    <table class="doc-meta" style="width: 100%; margin-bottom: 3mm;">
        <tr>
            <td class="left"><b>Kartu Laporan Hasil Perawatan</b></td>
            <td class="right"><b>TANGGAL BERLAKU</b> : {{ $field('tanggal_berlaku', '24 Januari 2024') }}<br><b>KODE DOKUMEN</b> : {{ $field('kode_dokumen', 'FM-Polnes-11-12-11/R3') }}</td>
        </tr>
    </table>

    <table class="paper-form">
        {{-- Header form (sama untuk semua section) --}}
        @include('pdf.partials.doc-header', [
            'colspan' => 9,
            'title' => 'Kartu Laporan<br>Hasil Perawatan',
            'reportLabel' => 'No. Laporan perawatan :',
            'reportNumber' => $field('nomor_laporan', $report->nomor_laporan),
            'logoSource' => $logoSource ?? null,
        ])
        
        {{-- Tabel data: setiap alat/mesin = 1 baris lengkap --}}
        @foreach ($sections as $index => $section)
            <tr>
                <td colspan="2">Nama Alat / Mesin</td>
                <td colspan="3">: {{ $section['nama_alat'] ?: '-' }}</td>
                <td>Gedung</td>
                <td colspan="3">: {{ $section['gedung'] ?: '-' }}</td>
            </tr>
            <tr>
                <td colspan="2">No. Inventaris</td>
                <td colspan="3">: {{ $section['no_inventaris'] ?: '-' }}</td>
                <td>Kode Alat</td>
                <td colspan="3">: {{ $section['kode_alat'] ?: '-' }}</td>
            </tr>
            <tr>
                <td colspan="2">Lokasi Alat</td>
                <td colspan="3">: {{ $section['lokasi'] ?: '-' }}</td>
                <td>Jurusan/Unit</td>
                <td colspan="3">: {{ $section['jurusan'] ?: '-' }}</td>
            </tr>
            <tr>
                <td colspan="7" class="big-space">{{ $section['uraian'] ?: '-' }}</td>
                <td colspan="2" class="center">Material / Suku Cadang<br>{{ $section['material'] }}<br>Kode: {{ $section['kode_material'] }}<br>Harga (Rp.)<br>Rp {{ number_format($section['biaya'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="7" class="right">Biaya</td>
                <td colspan="2" class="center">Rp {{ number_format($section['biaya_jasa'], 0, ',', '.') }}</td>
            </tr>
            
            {{-- Garis pemisah antar alat/mesin --}}
            @if ($multi && $index < $sections->count() - 1)
                <tr class="row-divider">
                    <td colspan="9"></td>
                </tr>
            @endif
        @endforeach
        
        {{-- Tanda tangan sekali di akhir --}}
        <tr class="center">
            <td></td>
            <td colspan="2">Pelaksana</td>
            <td colspan="4">Pemeriksa</td>
            <td colspan="2">Mengetahui</td>
        </tr>
        <tr class="center">
            <td>Nama</td>
            <td colspan="2">{{ $field('pelaksana_nama', $report->vendor?->nama_vendor ?: $report->pelaporUser?->name) }}</td>
            <td colspan="2">{{ $field('pemeriksa_nama', $report->verifikatorUser?->name ?: 'Ka.Sub/Ka.Jur/Ka.Lab/Ka.Beng/Ka.Unit') }}</td>
            <td colspan="2">{{ $report->approverUser?->name ?: 'Teknisi UPA.PP' }}</td>
            <td colspan="2">{{ $field('mengetahui_nama', 'Ka. UPA.PP') }}</td>
        </tr>
        <tr class="center">
            <td>Jabatan</td>
            <td colspan="2">Teknisi</td>
            <td colspan="2">Pemeriksa</td>
            <td colspan="2">UPA.PP</td>
            <td colspan="2">Pimpinan</td>
        </tr>
        <tr class="center">
            <td>Tanggal</td>
            <td colspan="2">{{ $report->tanggal_pelaksanaan?->format('d-m-Y') }}</td>
            <td colspan="2">{{ $report->verified_at?->format('d-m-Y') ?: '-' }}</td>
            <td colspan="2">{{ $report->approved_at?->format('d-m-Y') ?: '-' }}</td>
            <td colspan="2">{{ $report->approved_at?->format('d-m-Y') ?: '-' }}</td>
        </tr>
        <tr class="center signature-space">
            <td>Paraf</td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2"></td>
            <td colspan="2"></td>
        </tr>
    </table>
    
    <div class="print-note">Dokumen dicetak otomatis dari Sistem Informasi Pemeliharaan &amp; Perbaikan Aset UPA.PP Politeknik Negeri Samarinda.</div>

    {{-- Dokumentasi foto per bagian: halaman terpisah --}}
    @foreach ($sections as $section)
        @if ($section['attachments']->isNotEmpty())
            <div class="attachment-page">
                <div class="attachment-header">
                    <div class="unit">Politeknik Negeri Samarinda<br>Unit Penunjang Akademik Perawatan dan Perbaikan</div>
                    <div class="address">Jalan Dr. Ciptomangunkusumo Kampus Gunung Panjang Samarinda 75131</div>
                </div>
                <div class="attachment-title">Kegiatan Perawatan AC Tahun {{ $report->tanggal_pelaksanaan?->format('Y') ?: date('Y') }}</div>
                <div class="attachment-subtitle">Unit Kerja Pengguna AC : {{ $section['gedung'] ?: '-' }}</div>
                <div class="attachment-location">Lokasi : {{ $section['lokasi'] ?: '-' }}</div>
                <table class="attachment-grid">
                    @foreach ($section['attachments']->chunk(2) as $row)
                        <tr>
                            @foreach ($row as $attachment)
                                <td>
                                    @php($imageSource = $storageUrl($attachment))
                                    @if ($imageSource)
                                        <img src="{{ $imageSource }}" alt="{{ $attachment->caption }}">
                                    @else
                                        <div class="caption">Foto tidak tersedia</div>
                                    @endif
                                    <div class="caption">{{ $attachment->caption ?: \App\Models\ReportAttachment::slotLabel($attachment->slot_key) }}</div>
                                </td>
                            @endforeach
                            @if ($row->count() === 1)
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    @endforeach
</body>
</html>
