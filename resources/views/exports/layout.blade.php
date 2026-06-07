<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laporan BPKP')</title>
    <style>
        @page {
            margin: 2.2cm 1.5cm 2cm 1.5cm;
            size: @yield('orientation', 'landscape');
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.5;
        }

        /* ── KOP: logo + teks satu grup, di-center bersama ── */
        table.kop { border: none; border-collapse: collapse; margin: 0 auto; }
        table.kop td { border: none; padding: 0; vertical-align: middle; background: transparent; }
        td.kop-logo { text-align: right; vertical-align: middle; padding-right: 16px; white-space: nowrap; }
        td.kop-logo img { width: 88px; }
        td.kop-teks { text-align: center; vertical-align: middle; white-space: nowrap; }
        .kop-instansi { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #0f172a; letter-spacing: 0.2px; }
        .kop-perwakilan { font-size: 13.5px; font-weight: bold; text-transform: uppercase; color: #0f172a; letter-spacing: 0.2px; }
        .kop-alamat { font-size: 8.5px; color: #334155; margin-top: 3px; line-height: 1.6; }

        /* Garis kop double */
        .kop-garis-wrap { margin: 12px auto 16px auto; width: 85%; }
        hr.garis-tebal { border: none; border-top: 2.5pt solid #0f172a; margin: 0; }
        hr.garis-tipis { border: none; border-top: 0.75pt solid #0f172a; margin: 2px 0 0 0; }

        /* Judul */
        .judul-wrap { text-align: center; margin-bottom: 4px; }
        .judul-teks { font-size: 12.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; }
        .judul-sub { font-size: 9.5px; font-weight: bold; color: #475569; margin-top: 3px; }
        .judul-garis { width: 200px; margin: 5px auto 0 auto; border: none; border-top: 2pt solid #1d4ed8; }

        /* Metadata */
        .meta-wrap { width: 96%; margin: 12px auto 5px auto; }
        table.meta { border: none; border-collapse: collapse; margin: 0; }
        table.meta td { border: none; padding: 1px 0; background: transparent; font-size: 8.5px; color: #475569; vertical-align: top; }
        table.meta td.lbl { width: 95px; }
        table.meta td.sep { width: 14px; text-align: center; }
        table.meta td.val { font-weight: bold; color: #1e293b; }

        /* Tabel data */
        .tabel-outer { width: 96%; margin: 0 auto; }
        table.data { width: 100%; margin: 0 0 1cm 0; border-collapse: collapse; border: 1pt solid #93c5fd; page-break-inside: auto; }
        table.data thead { display: table-header-group; }
        table.data thead td {
            background-color: #1e3a6e; color: #f0f9ff; font-size: 8.5px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.5px; padding: 9px 8px;
            border-right: 0.5pt solid #2d5294; border-bottom: none;
        }
        table.data thead td:last-child { border-right: none; }
        table.data tbody tr { page-break-inside: avoid; page-break-after: auto; }
        table.data tbody tr.baris-ganjil td { background-color: #ffffff; }
        table.data tbody tr.baris-genap td { background-color: #eff6ff; }
        table.data tbody td {
            font-size: 8.5px; color: #334155; padding: 5.5px 8px;
            border-right: 0.5pt solid #bfdbfe; border-bottom: 0.5pt solid #bfdbfe; vertical-align: middle;
        }
        table.data tbody td:last-child { border-right: none; }

        /* Kelas kolom umum */
        .c   { text-align: center; }
        .r   { text-align: right; }
        .b   { font-weight: bold; }
        .col-no    { text-align: center; color: #94a3b8; font-size: 8px; }
        .col-mono  { font-size: 8px; color: #1e40af; font-weight: bold; }
        .col-nama  { font-weight: bold; color: #0f172a; }
        .col-sub   { font-size: 8px; color: #475569; }
        .col-tgl   { text-align: center; white-space: nowrap; font-size: 8px; color: #475569; }
        .col-num   { text-align: center; font-weight: bold; color: #1e40af; }
        .col-status{ text-align: center; font-weight: bold; font-size: 8px; text-transform: uppercase; letter-spacing: 0.3px; color: #1e293b; }

        /* Footer */
        table.footer { position: fixed; bottom: 0; left: 0; right: 0; width: 100%; border-collapse: collapse; border-top: 0.5pt solid #bfdbfe; }
        table.footer td { font-size: 7.5px; color: #94a3b8; padding: 5px 0 0 0; border: none; background: transparent; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="kop" align="center">
        <tr>
            <td class="kop-logo">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo-bpkp.png'))) }}" alt="BPKP">
            </td>
            <td class="kop-teks">
                <div class="kop-instansi">BADAN PENGAWASAN KEUANGAN DAN PEMBANGUNAN</div>
                <div class="kop-perwakilan">PERWAKILAN BPKP PROVINSI JAWA BARAT</div>
                <div class="kop-alamat">
                    Jalan Raya Cibeureum, Nomor 50, Bandung<br>
                    Telepon: (022) 6015108, Faksimile: (022) 6032096<br>
                    E-mail: jabar@bpkp.go.id, Website: https://www.bpkp.go.id
                </div>
            </td>
        </tr>
    </table>

    <div class="kop-garis-wrap">
        <hr class="garis-tebal">
        <hr class="garis-tipis">
    </div>

    {{-- JUDUL --}}
    <div class="judul-wrap">
        <div class="judul-teks">@yield('judul')</div>
        @hasSection('subjudul')<div class="judul-sub">@yield('subjudul')</div>@endif
        <hr class="judul-garis">
    </div>

    {{-- METADATA --}}
    <div class="meta-wrap">
        <table class="meta">
            @yield('meta')
            <tr>
                <td class="lbl">Waktu Cetak</td>
                <td class="sep">:</td>
                <td class="val">{{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('DD MMMM YYYY, HH:mm') }} WIB</td>
            </tr>
        </table>
    </div>

    {{-- TABEL DATA --}}
    <div class="tabel-outer">
        <table class="data">
            <thead>
                <tr>@yield('thead')</tr>
            </thead>
            <tbody>
                @yield('tbody')
            </tbody>
        </table>
    </div>

    {{-- FOOTER --}}
    <table class="footer">
        <tr>
            <td style="text-align:left; padding-left:2px">Dicetak secara otomatis melalui Sistem Monitoring IPMS BPKP Jabar</td>
            <td style="text-align:right; padding-right:2px">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</td>
        </tr>
    </table>

</body>
</html>
