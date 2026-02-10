<!DOCTYPE html>
<html>
<head>
    <title>Rekap Capaian Kinerja per Bidang</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; background-color: #fff; }
        
        .kop-container { position: relative; padding-bottom: 15px; border-bottom: 2.5pt solid #1e293b; margin-bottom: 2px; }
        .kop-border-thin { border-bottom: 1pt solid #1e293b; margin-bottom: 20px; }
        .logo-bpkp { position: absolute; left: 0; top: -5px; width: 85px; }
        .kop-text { text-align: center; margin-left: 30px; }
        .kop-text h1 { font-size: 14px; margin: 0; padding: 0; text-transform: uppercase; color: #1e293b; letter-spacing: 0.5px; }
        .kop-text h2 { font-size: 18px; margin: 2px 0; padding: 0; text-transform: uppercase; font-weight: 800; color: #1e293b; }
        .kop-text p { font-size: 10px; margin: 2px 0 0 0; padding: 0; font-style: italic; color: #64748b; }

        .doc-header { margin-top: 30px; margin-bottom: 25px; text-align: center; }
        .doc-title { font-size: 16px; font-weight: 800; text-transform: uppercase; margin-bottom: 5px; color: #1e293b; border-bottom: 1.5pt solid #3b82f6; display: inline-block; padding-bottom: 3px; }
        
        .doc-metadata { width: 100%; margin-bottom: 15px; font-size: 10px; color: #64748b; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; border-radius: 8px; overflow: hidden; }
        th { background-color: #1e293b; color: #ffffff; font-weight: 700; text-transform: uppercase; font-size: 9px; padding: 12px 0 12px 8px; text-align: left; border: none; }
        td { padding: 10px 8px; border-bottom: 0.5pt solid #e2e8f0; vertical-align: middle; color: #334155; }
        tr:nth-child(even) { background-color: #f8fafc; }
        
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: 700; }
        .highlight { color: #3b82f6; font-weight: 700; }
        
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 8px; color: #94a3b8; text-align: center; padding-top: 10px; border-top: 0.5pt solid #e2e8f0; }
        
        .progress-bar-container { width: 100px; height: 8px; background-color: #e2e8f0; border-radius: 4px; overflow: hidden; display: inline-block; vertical-align: middle; margin-right: 5px; }
        .progress-bar-fill { height: 100%; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="kop-container">
        <img src="data:image/webp;base64,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" class="logo-bpkp">
    <div class="kop-text">
        <h1>BADAN PENGAWASAN KEUANGAN DAN PEMBANGUNAN</h1>
        <h2>PERWAKILAN PROVINSI JAWA BARAT</h2>
        <p>Jl. Raya Cibeureum No.50, Campaka, Kec. Andir, Kota Bandung, Jawa Barat 40184</p>
    </div>
</div>
<div class="kop-border-thin"></div>

<div class="doc-header">
    <div class="doc-title">REKAPITULASI CAPAIAN KINERJA PER BIDANG</div>
</div>

<div class="doc-metadata">
    <table style="width: auto; margin-top: 0; background: transparent;">
        @if($startDate && $endDate)
        <tr>
            <td style="padding: 2px 0; border: none; font-weight: 700;">Periode Laporan</td>
            <td style="padding: 2px 10px; border: none;">:</td>
            <td style="padding: 2px 0; border: none;" class="highlight">{{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 2px 0; border: none; font-weight: 700;">Waktu Cetak</td>
            <td style="padding: 2px 10px; border: none;">:</td>
            <td style="padding: 2px 0; border: none;">{{ date('d-m-Y H:i') }} WIB</td>
        </tr>
    </table>
</div>

<table>
    <thead>
        <tr>
            <th width="30" class="center">No.</th>
            <th>Nama Bidang</th>
            <th width="80" class="center">Jml Pegawai</th>
            <th width="80" class="center">Total ST</th>
            <th width="80" class="center">Total LHP</th>
            <th width="150" class="center">Capaian (%)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapCapaianBidang as $index => $bidang)
        @php
            $percentage = $bidang->total_st > 0 ? ($bidang->total_lhp / $bidang->total_st) * 100 : 0;
            $color = $percentage >= 100 ? '#10b981' : ($percentage >= 50 ? '#3b82f6' : '#f59e0b');
        @endphp
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td class="bold">{{ $bidang->nm_bidwas }}</td>
            <td class="center">{{ $bidang->total_pegawai }}</td>
            <td class="center">{{ $bidang->total_st }}</td>
            <td class="center">{{ $bidang->total_lhp }}</td>
            <td>
                <div style="display: flex; align-items: center; justify-content: flex-end;">
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" style="width: {{ min(100, $percentage) }}%; background-color: {{ $color }};"></div>
                    </div>
                    <span class="bold" style="color: {{ $color }};">{{ number_format($percentage, 1) }}%</span>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Dicetak secara otomatis melalui Sistem Monitoring IPMS BPKP Jabar &bull; {{ date('Y') }}
</div>
</body>
</html>
