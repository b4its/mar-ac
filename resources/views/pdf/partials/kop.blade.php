<table class="kop-table">
    <tr>
        <td class="kop-logo-cell">
            @if (! empty($logoSource))
                <img src="{{ $logoSource }}" alt="Logo Polnes" class="kop-logo">
            @endif
        </td>
        <td class="kop-text-cell">
            <div class="institusi">Politeknik Negeri Samarinda</div>
            <div class="instansi">Unit Pelaksana Teknis Pengelolaan &amp; Pemeliharaan Aset</div>
            <div class="alamat">Jl. Cipto Mangunkusumo, Sungai Dama, Kec. Samarinda Ilir, Kota Samarinda, Kalimantan Timur 75242</div>
        </td>
    </tr>
</table>
<div class="garis"></div>

<div class="judul-form">
    <div class="no-form">{{ $formNumber }}</div>
    <div class="judul">{{ $title }}</div>
</div>
<div class="nomor-laporan">Nomor: <b>{{ $reportNumber }}</b></div>
