@extends('exports.layout')

@section('title', 'Rekap Pegawai Per Bidang')
@section('orientation', 'landscape')
@section('judul', 'Rekap Pegawai Per Bidang')
@section('subjudul', $informasiBidang->nm_bidwas)

@section('meta')
    @if(!empty($startDate) && !empty($endDate))
    <tr>
        <td class="lbl">Periode Laporan</td>
        <td class="sep">:</td>
        <td class="val">{{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('DD MMM YYYY') }} s/d {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
    </tr>
    @endif
@endsection

@section('thead')
    <td width="30" class="c">No.</td>
    <td>Nama Pegawai</td>
    <td width="110" class="c">NIP</td>
    <td width="180">Jabatan</td>
    <td width="80" class="c">ST Aktif</td>
    <td width="80" class="c">Total ST</td>
@endsection

@section('tbody')
    @php
        $roleLabels = [
            'staff' => 'Staff',
            'korwas_apd_1' => 'Korwas APD 1', 'korwas_apd_2' => 'Korwas APD 2',
            'korwas_an_1' => 'Korwas AN 1', 'korwas_an_2' => 'Korwas AN 2',
            'korwas_ipp_1' => 'Korwas IPP 1', 'korwas_ipp_2' => 'Korwas IPP 2',
            'korwas_investigasi_1' => 'Korwas Investigasi 1', 'korwas_investigasi_2' => 'Korwas Investigasi 2',
            'korwas_p3a' => 'Korwas P3A',
            'kepala_perwakilan' => 'Kepala Perwakilan', 'kepala_bagian_umum' => 'Kepala Bagian Umum',
            'subkoor_keuangan' => 'Subkoordinator Keuangan', 'subkoor_bmn_rt_kearsipan' => 'Subkoordinator BMN & RT',
        ];
    @endphp
    @foreach($daftarPegawai as $index => $pegawai)
    <tr class="{{ $index % 2 === 0 ? 'baris-ganjil' : 'baris-genap' }}">
        <td class="col-no">{{ $index + 1 }}</td>
        <td class="col-nama">{{ $pegawai->nama }}</td>
        <td class="col-mono c">{{ $pegawai->nip }}</td>
        <td class="col-sub">{{ $roleLabels[$pegawai->user_role] ?? $pegawai->user_role }}</td>
        <td class="col-num">{{ $pegawai->active_tasks }}</td>
        <td class="c b">{{ $pegawai->total_assignments }}</td>
    </tr>
    @endforeach
@endsection
