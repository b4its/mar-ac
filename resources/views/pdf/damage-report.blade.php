<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $report->nomor_laporan }}</title>
    @include('pdf.partials.style')
</head>
<body>
    @php
        $asset = $report->asset;
        $print = $report->print_fields ?? [];
        $field = fn (string $key, mixed $fallback = '-') => filled($print[$key] ?? null) ? $print[$key] : (filled($fallback) ? $fallback : '-');
        $roomName = $field('nama_ruangan', $asset?->room?->nama_ruangan);
        $location = $field('lokasi_alat', $roomName);
        $department = $field('jurusan_unit', $field('jurusan', $asset?->department?->nama_jurusan));
        $building = $field('gedung', $asset?->room?->building?->nama_gedung);
        $level = \App\Enums\DamageLevel::from($report->tingkat_kerusakan);
        $attachments = $report->attachments;
    @endphp

    <table class="paper-form">
        <tr>
            <td colspan="5"><b>Laporan Kerusakan</b></td>
            <td colspan="4"><b>Tanggal Revisi</b> : {{ $field('tanggal_revisi', '24 Januari 2024') }}<br><b>Tanggal Berlaku</b> : {{ $field('tanggal_berlaku', '24 Januari 2024') }}<br><b>Kode Dokumen</b> : {{ $field('kode_dokumen', 'FM-Polnes-11-02-03/R3') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="brand">
                Politeknik Negeri Samarinda<br>
                <span class="brand-logo">UPA.PP</span>
            </td>
            <td colspan="5" class="doc-title">Laporan Kerusakan</td>
            <td colspan="2">Nomor : <b>{{ $field('nomor_laporan', $report->nomor_laporan) }}</b><br>Tanggal : {{ $report->tanggal_laporan?->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td colspan="2">Nama Alat / Mesin</td>
            <td colspan="5">: {{ $field('nama_alat', $asset?->nama_alat) }}</td>
            <td colspan="2" rowspan="4" class="check-cell">
                <b>Tingkat Kerusakan :</b><br>
                Rusak Ringan .......... {{ $level === \App\Enums\DamageLevel::Ringan ? '[x]' : '[ ]' }}<br>
                Rusak Sedang .......... {{ $level === \App\Enums\DamageLevel::Sedang ? '[x]' : '[ ]' }}<br>
                Rusak Berat ........... {{ $level === \App\Enums\DamageLevel::Berat ? '[x]' : '[ ]' }}
            </td>
        </tr>
        <tr>
            <td colspan="2">No. Inventaris</td>
            <td colspan="5">: {{ $field('no_inventaris', $asset?->no_inventaris) }}</td>
        </tr>
        <tr>
            <td colspan="2">Lokasi Alat / Mesin</td>
            <td colspan="5">: {{ $location }}</td>
        </tr>
        <tr>
            <td colspan="2">Kode Alat</td>
            <td colspan="5">: {{ $field('kode_alat', $asset?->kode_alat) }}</td>
        </tr>
        <tr>
            <td colspan="2">Jurusan / Unit</td>
            <td colspan="7">: {{ $department }}</td>
        </tr>
        <tr>
            <td colspan="9"><b>Jenis Kerusakan :</b><br>{{ $report->jenis_kerusakan }}</td>
        </tr>
        <tr>
            <td colspan="9" class="big-space"><b>Uraian Kerusakan :</b><br>{{ $report->uraian_kerusakan ?: '-' }}</td>
        </tr>
        <tr class="center">
            <td colspan="3">Wadir/Kajur/Sekjur/Ka.Lab/Ka.Beng/Ka.Unit</td>
            <td colspan="3">Teknisi UPA.PP</td>
            <td colspan="3">Ka. UPA.PP</td>
        </tr>
        <tr class="center signature-space">
            <td colspan="3">{{ $field('pelapor_nama', $report->approvedByUser?->name ?: '') }}</td>
            <td colspan="3">{{ $field('teknisi_nama', $report->pelaporUser?->name ?: '') }}</td>
            <td colspan="3">{{ $field('mengetahui_nama', '') }}</td>
        </tr>
    </table>

    <div class="print-note">Status: {{ \App\Enums\DamageReportStatus::from($report->status)->label() }}. Dokumen dicetak otomatis dari Sistem Informasi Pemeliharaan &amp; Perbaikan Aset UPA.PP Politeknik Negeri Samarinda.</div>

    @if ($attachments->isNotEmpty())
        <div class="attachment-page">
            <div class="attachment-header">
                <div class="unit">Politeknik Negeri Samarinda<br>Unit Penunjang Akademik Perawatan dan Perbaikan</div>
                <div class="address">Jalan Dr. Ciptomangunkusumo Kampus Gunung Panjang Samarinda 75131</div>
            </div>
            <div class="attachment-title">Lampiran Foto Laporan Kerusakan</div>
            <div class="attachment-subtitle">{{ $building }}</div>
            <div class="attachment-location">Lokasi : {{ $location }}</div>
            <table class="attachment-grid small">
                @foreach ($attachments->chunk(4) as $row)
                    <tr>
                        @foreach ($row as $attachment)
                            <td>
                                <img src="{{ $storageUrl($attachment) }}" alt="{{ $attachment->caption }}">
                                <div class="caption">{{ $attachment->caption ?: 'Foto Kerusakan' }}</div>
                            </td>
                        @endforeach
                        @for ($i = $row->count(); $i < 4; $i++)
                            <td></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
</body>
</html>
