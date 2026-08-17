<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 11pt;
        color: #000;
        line-height: 1.4;
    }
    .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    .kop-logo-cell { width: 100pt; text-align: center; vertical-align: middle; padding-right: 8pt; }
    .kop-logo { height: 45pt; max-width: 100pt; object-fit: contain; display: inline-block; }
    .kop-text-cell { text-align: center; vertical-align: middle; }
    .kop-text-cell .instansi { font-size: 13pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
    .kop-text-cell .institusi { font-size: 11pt; font-weight: bold; text-transform: uppercase; }
    .kop-text-cell .alamat { font-size: 8.5pt; }
    .garis { border-bottom: 2.5px solid #000; margin: 4px 0 10px; }
    .garis-tipis { border-bottom: 1px solid #000; margin-bottom: 6px; }
    .judul-form {
        text-align: center;
        margin: 14px 0 2px;
    }
    .judul-form .no-form { font-size: 9pt; text-transform: uppercase; }
    .judul-form .judul {
        font-size: 13pt;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: 1.5px solid #000;
        display: inline-block;
        padding: 4px 30px;
        margin-top: 4px;
    }
    .nomor-laporan { text-align: center; font-size: 11pt; margin: 8px 0 14px; }
    .nomor-laporan b { letter-spacing: 0.5px; }
    table.form { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    table.form td, table.form th {
        border: 1px solid #000;
        padding: 5px 8px;
        vertical-align: top;
    }
    table.form .label { width: 32%; font-weight: bold; }
    table.form .sub-label { font-weight: bold; background: #f2f2f2; }
    .isi { min-height: 18px; }
    .foto-grid { display: flex; flex-wrap: wrap; gap: 12px; margin: 6px 0 14px; }
    .foto-item { width: 31%; border: 1px solid #000; padding: 4px; text-align: center; page-break-inside: avoid; }
    .foto-item img { width: 100%; height: auto; }
    .foto-item .foto-caption { font-size: 9pt; margin-top: 3px; }
    .paper-form { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 14px; }
    .paper-form td, .paper-form th { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
    .paper-form .doc-title { font-size: 16pt; font-weight: bold; text-align: center; text-transform: uppercase; letter-spacing: .5px; }
    .paper-form .section-title { font-weight: bold; background: #efefef; }
    .paper-form .section-gap td { height: 6px; padding: 0; border-left: 0; border-right: 0; background: #f4f4f4; }
    .paper-form .center { text-align: center; }
    .paper-form .right { text-align: right; }
    .paper-form .muted { color: #333; font-size: 9pt; }
    .paper-form .big-space { height: 82px; }
    .paper-form .big-space.compact { height: 38px; }
    .paper-form .signature-space { height: 48px; }
    .paper-form .signature-space.compact { height: 22px; }
    .paper-form .narrow { width: 18%; }
    .paper-form .check-cell { line-height: 1.9; }
    .paper-form .brand { text-align: center; font-weight: bold; }
    .paper-form .brand-logo { height: 30pt; max-width: 100%; object-fit: contain; display: block; margin: 0 auto; }
    .doc-meta { width: 100%; border-collapse: collapse; margin: 0 0 2px; }
    .doc-meta td { padding: 0 2px; vertical-align: top; }
    .doc-meta .left { text-align: left; font-size: 11pt; }
    .doc-meta .right { text-align: right; font-size: 9pt; }
    .paper-form .doc-header-cell { padding: 0; }
    .doc-header-inner { width: 100%; border-collapse: collapse; }
    .doc-header-inner td { border: none; vertical-align: middle; }
    .doc-header-inner .doc-header-left { width: 27%; text-align: center; padding: 5pt 3pt; }
    .doc-header-inner .doc-header-left .instansi { font-weight: bold; font-size: 10pt; text-transform: uppercase; }
    .doc-header-inner .doc-header-left .brand-logo { height: 34pt; max-width: 55pt; margin: 1pt auto; }
    .doc-header-inner .doc-header-left .unit { font-weight: bold; font-size: 10pt; text-transform: uppercase; }
    .doc-header-inner .doc-header-center { width: 46%; text-align: center; padding: 5pt 3pt; }
    .doc-header-inner .doc-header-center .doc-title { font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; line-height: 1.2; }
    .doc-header-inner .doc-header-right { width: 27%; text-align: left; padding: 5pt 6pt; font-size: 9.5pt; vertical-align: top; }
    .doc-header-inner .doc-header-right .lbl { font-weight: bold; }
    .doc-header-inner .doc-header-right .no { font-weight: bold; }
    .print-note { font-size: 9pt; margin-top: 8px; font-style: italic; }
    .attachment-page { page-break-before: always; }
    .attachment-header { text-align: center; margin-bottom: 10px; }
    .attachment-header .unit { font-size: 12pt; font-weight: bold; text-transform: uppercase; }
    .attachment-header .address { font-size: 8.5pt; }
    .attachment-title { margin: 10px 0 8px; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px; text-align: center; font-size: 13pt; font-weight: bold; text-transform: uppercase; background: #333; color: #fff; }
    .attachment-subtitle { margin-bottom: 8px; text-align: center; font-size: 11pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; }
    .attachment-location { border: 1px solid #000; border-bottom: 0; padding: 3px; text-align: center; font-weight: bold; }
    .attachment-grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .attachment-grid td { border: 1px solid #000; padding: 4px; text-align: center; vertical-align: top; page-break-inside: avoid; }
    .attachment-grid img { width: 100%; max-height: 245px; object-fit: contain; }
    .attachment-grid .caption { margin-top: 4px; padding: 3px 4px; background: #333; color: #fff; font-weight: bold; font-size: 10pt; }
    .attachment-grid.small img { max-height: 165px; }
    .attachment-grid.small .caption { font-size: 8.5pt; }
    .ttd-grid {
        display: flex;
        justify-content: space-between;
        margin: 30px 0 0;
        gap: 40px;
    }
    .ttd { width: 50%; text-align: center; }
    .ttd .nama { margin-top: 70px; font-weight: bold; text-decoration: underline; }
    .ttd .nip { font-size: 10pt; }
    .catatan { margin-top: 10px; }
    .keterangan { font-size: 9.5pt; margin: 8px 0 4px; font-style: italic; }
</style>
