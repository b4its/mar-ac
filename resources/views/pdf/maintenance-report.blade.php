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
        $field = fn (string $key, mixed $fallback = '') => filled($print[$key] ?? null) ? $print[$key] : (filled($fallback) ? $fallback : '');

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
                'kode_material' => $field('kode_material', ''),
                'biaya' => (float) $report->biaya,
                'biaya_jasa' => (float) $report->biaya_jasa,
                'attachments' => $report->attachments->filter(fn ($a) => !str_ends_with($a->slot_key, '_2'))->values(),
                'tanggal_pelaksanaan' => $report->tanggal_pelaksanaan,
            ],
        ]);

        // Tambahkan items bagian kedua (alat kedua jika ada)
        foreach ($report->items as $item) {
            $sections->push([
                'nama_alat' => $item->asset?->nama_alat ?? '',
                'no_inventaris' => $item->asset?->no_inventaris ?? '',
                'gedung' => $item->asset?->room?->building?->nama_gedung ?? '',
                'kode_alat' => $item->asset?->kode_alat ?? '',
                'lokasi' => $item->asset?->room?->nama_ruangan ?? '',
                'jurusan' => $item->asset?->department?->nama_jurusan ?? '',
                'uraian' => $item->uraian_pekerjaan ?: $item->jenis_pekerjaan,
                'material' => '',
                'kode_material' => '',
                'biaya' => (float) $item->biaya,
                'biaya_jasa' => (float) $item->biaya_jasa,
                'attachments' => $report->attachments->filter(fn ($a) => str_ends_with($a->slot_key, '_2'))->values(),
                'tanggal_pelaksanaan' => $item->tanggal_pelaksanaan ?? $report->tanggal_pelaksanaan,
            ]);
        }
    @endphp

    <style>
        @page { size: A4; margin: 4mm; }
        body { zoom: {{ $sections->count() > 1 ? '0.78' : '1' }}; }

        .maintenance-card {
            margin-bottom: 3mm;
            page-break-inside: avoid;
        }
        .maintenance-card .doc-meta {
            margin-bottom: 1mm;
        }
        .maintenance-card .doc-meta td {
            font-size: 8.5pt;
        }
        .paper-form {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
            font-size: 8.4pt;
            line-height: 1.15;
        }
        .paper-form td, .paper-form th {
            border: 0.8pt solid #000 !important;
            padding: 1.8pt 2.4pt;
        }
        .paper-form tr td { border: 0.8pt solid #000 !important; }
        .doc-header-inner td { border: none !important; }
        .doc-header-inner .doc-header-left,
        .doc-header-inner .doc-header-center { border-right: 0.8pt solid #000 !important; }
        .doc-header-inner .doc-header-center .doc-title { font-size: 13pt; }
        .doc-header-inner .doc-header-left .brand-logo { height: 24pt; }
        .doc-header-inner .doc-header-left,
        .doc-header-inner .doc-header-center,
        .doc-header-inner .doc-header-right { padding: 3pt 4pt; }
        .paper-form .work-top td { height: 5mm; }
        .paper-form .work-cell { height: 23mm; vertical-align: top; }
        .paper-form .material-row td { height: 5mm; }
        .paper-form .material-empty td { height: 18mm; }
        .paper-form .cost-cell { height: 17mm; vertical-align: middle; }
        .paper-form .signature-space td { height: 12mm; }
    </style>

    {{-- Tabel data: setiap alat/mesin = 1 kartu laporan lengkap --}}
    @foreach ($sections as $section)
        <div class="maintenance-card">
            <table class="doc-meta">
                <tr>
                    <td class="left"><b>Kartu Laporan Hasil Perawatan</b></td>
                    <td class="right" style="text-align: right;"><b>TANGGAL BERLAKU</b> : {{ $field('tanggal_berlaku', '24 Januari 2024') }}<br><b>KODE DOKUMEN</b> : {{ $field('kode_dokumen', 'FM-Polnes-11-12-11/R3') }}</td>
                </tr>
            </table>

            <table class="paper-form">
                <colgroup>
                    <col style="width: 8%;">
                    <col style="width: 10%;">
                    <col style="width: 13%;">
                    <col style="width: 13%;">
                    <col style="width: 13%;">
                    <col style="width: 11%;">
                    <col style="width: 14%;">
                    <col style="width: 9%;">
                    <col style="width: 9%;">
                </colgroup>
                @include('pdf.partials.doc-header', [
                    'colspan' => 9,
                    'title' => 'Kartu Laporan<br>Hasil Perawatan',
                    'reportLabel' => 'No. Laporan perawatan :',
                    'reportNumber' => $report->nomor_laporan,
                    'logoSource' => $logoSource ?? null,
                ])

                <tr>
                    <td colspan="2">Nama Alat / Mesin</td>
                    <td colspan="3">: {{ $section['nama_alat'] ?: '' }}</td>
                    <td>Gedung</td>
                    <td colspan="3">: {{ $section['gedung'] ?: '' }}</td>
                </tr>
                <tr>
                    <td colspan="2">No. Inventaris</td>
                    <td colspan="3">: {{ $section['no_inventaris'] ?: '' }}</td>
                    <td>Kode Alat</td>
                    <td colspan="3">: {{ $section['kode_alat'] ?: '' }}</td>
                </tr>
                <tr>
                    <td colspan="2">Lokasi Alat</td>
                    <td colspan="3">: {{ $section['lokasi'] ?: '' }}</td>
                    <td>Jurusan/Unit</td>
                    <td colspan="3">: {{ $section['jurusan'] ?: '' }}</td>
                </tr>
                <tr class="work-top">
                    <td colspan="7"></td>
                    <td colspan="2" class="center"><b>Material / Suku Cadang</b></td>
                </tr>
                <tr class="center material-row">
                    <td colspan="7" rowspan="2" class="work-cell">{{ filled($section['uraian']) && $section['uraian'] !== '' ? $section['uraian'] : '' }}</td>
                    <td>Kode Alat</td>
                    <td>Harga (Rp.)</td>
                </tr>
                <tr class="center material-empty">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="center">
                    <td></td>
                    <td>Pelaksana</td>
                    <td colspan="5">Pemeriksa</td>
                    <td colspan="2">B i a y a</td>
                </tr>
                <tr class="center">
                    <td>Nama</td>
                    <td>{{ $field('pelaksana_nama', $report->vendor?->nama_vendor ?: $report->pelaporUser?->name) }}</td>
                    <td colspan="3"></td>
                    <td>{{ $field('pemeriksa_nama', $report->verifikatorUser?->name ?: '') }}</td>
                    <td>{{ $field('mengetahui_nama', $report->approverUser?->name ?: '') }}</td>
                    <td colspan="2" rowspan="4" class="cost-cell">Rp {{ number_format($section['biaya_jasa'], 0, ',', '.') }},-</td>
                </tr>
                <tr class="center">
                    <td>Jabatan</td>
                    <td>Teknisi</td>
                    <td colspan="3">Ka.Sub/Ka.Jur/Ka.Lab/Ka.Beng/Ka.Unit</td>
                    <td>Teknisi</td>
                    <td>Ka. UPA.PP</td>
                </tr>
                <tr class="center">
                    <td>Tanggal</td>
                    <td>{{ $section['tanggal_pelaksanaan']?->format('d-m-Y') }}</td>
                    <td colspan="3"></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="center signature-space">
                    <td>Paraf</td>
                    <td></td>
                    <td colspan="3"></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    @endforeach

    {{-- Dokumentasi foto per bagian: halaman terpisah --}}
    @foreach ($sections as $section)
        @if ($section['attachments']->isNotEmpty())
            <div class="attachment-page">
                <div class="attachment-header">
                    <div class="unit">Politeknik Negeri Samarinda<br>Unit Penunjang Akademik Perawatan dan Perbaikan</div>
                    <div class="address">Jalan Dr. Ciptomangunkusumo Kampus Gunung Panjang Samarinda 75131</div>
                </div>
                <div class="attachment-title">Kegiatan Perawatan AC Tahun {{ $report->tanggal_pelaksanaan?->format('Y') ?: date('Y') }}</div>
                <div class="attachment-subtitle">Unit Kerja Pengguna AC : {{ $section['gedung'] ?: '' }}</div>
                <div class="attachment-location">Lokasi : {{ $section['lokasi'] ?: '' }}</div>
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
