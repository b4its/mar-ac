<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $report->nomor_laporan }}</title>
    @include('pdf.partials.style')
</head>
<body>
    @include('pdf.partials.kop', [
        'formNumber' => 'FM-Polnes-11-02-04/R3',
        'title' => 'Laporan Hasil Perbaikan Aset',
        'reportNumber' => $report->nomor_laporan,
    ])

    <table class="form">
        <tr>
            <td class="label">Laporan Kerusakan</td>
            <td>: {{ $report->damageReport?->nomor_laporan ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Data Aset</td>
            <td class="isi">
                <b>{{ $report->asset?->nama_alat }}</b><br>
                Kode: {{ $report->asset?->kode_alat }} &nbsp;|&nbsp; No. Inventaris: {{ $report->asset?->no_inventaris }}<br>
                Jenis: {{ $report->asset?->jenis_alat }}<br>
                Lokasi: {{ $report->asset?->room?->nama_ruangan }} ({{ $report->asset?->room?->building?->nama_gedung }}), {{ $report->asset?->department?->nama_jurusan }}
            </td>
        </tr>
        <tr>
            <td class="label">Jenis Pekerjaan</td>
            <td class="isi">{{ $report->jenis_pekerjaan }}</td>
        </tr>
        <tr>
            <td class="label">Uraian Pekerjaan</td>
            <td class="isi">{{ $report->uraian_pekerjaan }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Pelaksanaan</td>
            <td>: {{ $report->tanggal_pelaksanaan?->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Biaya Sparepart</td>
            <td>: Rp {{ number_format($report->biaya ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Biaya Jasa</td>
            <td>: Rp {{ number_format($report->biaya_jasa ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Vendor</td>
            <td class="isi">{{ $report->vendor?->nama_vendor ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Teknisi</td>
            <td class="isi">{{ $report->teknisiUser?->name ?: $report->pelaporUser?->name }}</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td>: {{ \App\Enums\RepairStatus::from($report->status)->label() }}</td>
        </tr>
        @if ($report->status === \App\Enums\RepairStatus::Disetujui->value && $report->verified_at)
            <tr>
                <td class="label">Verifikasi</td>
                <td class="isi">
                    Disetujui {{ $report->verified_at->translatedFormat('d F Y H:i') }}
                    @if ($report->verifikatorUser)
                        oleh {{ $report->verifikatorUser->name }}
                    @endif
                </td>
            </tr>
        @endif
        @if ($report->catatan)
            <tr>
                <td class="label">Catatan</td>
                <td class="isi">{{ $report->catatan }}</td>
            </tr>
        @endif
    </table>

    @if ($report->attachments->isNotEmpty())
        <table class="form">
            <tr>
                <td class="sub-label">Dokumentasi Perbaikan</td>
            </tr>
        </table>
        <div class="foto-grid">
            @foreach ($report->attachments as $attachment)
                <div class="foto-item">
                    <img src="{{ $storageUrl($attachment) }}" alt="{{ $attachment->slot_key }}">
                    <div class="foto-caption">{{ $attachment->caption }}</div>
                </div>
            @endforeach
        </div>
    @endif

    @include('pdf.partials.signatures', [
        'blocks' => [
            ['label' => 'Teknisi Pelaksana', 'name' => $report->teknisiUser?->name ?? $report->pelaporUser?->name ?? '.........................'],
            [
                'label' => 'Mengetahui,<br>Kepala UPA.PP',
                'name' => $report->verifikatorUser?->name ?? '.........................',
            ],
        ],
    ])

    <div class="keterangan">Dokumen ini dicetak otomatis dari Sistem Informasi Pemeliharaan &amp; Perbaikan Aset UPA.PP Politeknik Negeri Samarinda.</div>
</body>
</html>
