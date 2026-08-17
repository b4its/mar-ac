@php
    $colspan = $colspan ?? 9;
    $title = $title ?? '';
    $reportLabel = $reportLabel ?? 'No. Laporan :';
    $reportNumber = $reportNumber ?? '';
    $logoSource = $logoSource ?? null;
@endphp
<tr class="doc-header">
    <td colspan="{{ $colspan }}" class="doc-header-cell">
        <table class="doc-header-inner">
            <tr>
                <td class="doc-header-left">
                    <div class="instansi">Politeknik Negeri Samarinda</div>
                    @if (! empty($logoSource))
                        <img src="{{ $logoSource }}" alt="Logo Polnes" class="brand-logo">
                    @endif
                    <div class="unit">UPA. PP</div>
                </td>
                <td class="doc-header-center">
                    <div class="doc-title">{!! $title !!}</div>
                </td>
                <td class="doc-header-right">
                    <span class="lbl">{{ $reportLabel }}</span>
                    <span class="no">{{ $reportNumber }}</span>
                </td>
            </tr>
        </table>
    </td>
</tr>
