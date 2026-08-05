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
        $materialTotal = (float) ($report->biaya ?? 0);
        $serviceTotal = (float) ($report->biaya_jasa ?? 0);
        $attachments = $report->attachments;
    @endphp

    <table class="paper-form">
        <tr>
            <td colspan="6"><b>Kartu Laporan Hasil Perawatan</b></td>
            <td colspan="3"><b>TANGGAL BERLAKU</b> : {{ $field('tanggal_berlaku', '24 Januari 2024') }}<br><b>KODE DOKUMEN</b> : {{ $field('kode_dokumen', 'FM-Polnes-11-12-11/R3') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="brand">
                Politeknik Negeri Samarinda<br>
                <span class="brand-logo">UPA.PP</span>
            </td>
            <td colspan="5" class="doc-title">Kartu Laporan<br>Hasil Perawatan</td>
            <td colspan="2">No. Laporan perawatan:<br><b>{{ $field('nomor_laporan', $report->nomor_laporan) }}</b></td>
        </tr>
        <tr>
            <td colspan="2">Nama Alat / Mesin</td>
            <td colspan="3">: {{ $field('nama_alat', $asset?->nama_alat) }}</td>
            <td>Gedung</td>
            <td colspan="3">: {{ $field('gedung', $asset?->room?->building?->nama_gedung) }}</td>
        </tr>
        <tr>
            <td colspan="2">No. Inventaris</td>
            <td colspan="3">: {{ $field('no_inventaris', $asset?->no_inventaris) }}</td>
            <td>Kode Alat</td>
            <td colspan="3">: {{ $field('kode_alat', $asset?->kode_alat) }}</td>
        </tr>
        <tr>
            <td colspan="2">Lokasi Alat</td>
            <td colspan="3">: {{ $location }}</td>
            <td>Jurusan/Unit</td>
            <td colspan="3">: {{ $department }}</td>
        </tr>
        <tr>
            <td colspan="7" class="big-space">{{ $report->uraian_pekerjaan ?: $report->jenis_pekerjaan }}</td>
            <td colspan="2" class="center">Material / Suku Cadang<br>{{ $field('material_suku_cadang', '') }}<br>Kode: {{ $field('kode_material', '-') }}<br>Harga (Rp.)<br>Rp {{ number_format($materialTotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="7" class="right">Biaya</td>
            <td colspan="2" class="center">Rp {{ number_format($serviceTotal, 0, ',', '.') }}</td>
        </tr>
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

    @if ($attachments->isNotEmpty())
        <div class="attachment-page">
            <div class="attachment-header">
                <div class="unit">Politeknik Negeri Samarinda<br>Unit Penunjang Akademik Perawatan dan Perbaikan</div>
                <div class="address">Jalan Dr. Ciptomangunkusumo Kampus Gunung Panjang Samarinda 75131</div>
            </div>
            <div class="attachment-title">Kegiatan Perawatan AC Tahun {{ $report->tanggal_pelaksanaan?->format('Y') ?: date('Y') }}</div>
            <div class="attachment-subtitle">Unit Kerja Pengguna AC : {{ $field('gedung', $asset?->room?->building?->nama_gedung) }}</div>
            <div class="attachment-location">Lokasi : {{ $location }}</div>
            <table class="attachment-grid">
                @foreach ($attachments->chunk(2) as $row)
                    <tr>
                        @foreach ($row as $attachment)
                            <td>
                                <img src="{{ $storageUrl($attachment) }}" alt="{{ $attachment->caption }}">
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
</body>
</html>
