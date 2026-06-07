@extends('exports.layout')

@section('title', 'Daftar Laporan Hasil Pengawasan')
@section('orientation', 'landscape')
@section('judul', 'Daftar Laporan Hasil Pengawasan (LHP)')

@section('thead')
    <td width="26" class="c">No.</td>
    <td width="130">Nomor LHP</td>
    <td width="130">Nomor ST</td>
    <td>Nama Penugasan</td>
    <td width="150">Bidang</td>
    <td width="72" class="c">Tgl. LHP</td>
    <td width="78" class="c">Status</td>
@endsection

@section('tbody')
    @foreach($daftarLhp as $index => $lhp)
    <tr class="{{ $index % 2 === 0 ? 'baris-ganjil' : 'baris-genap' }}">
        <td class="col-no">{{ $index + 1 }}</td>
        <td class="col-mono">{{ $lhp->no_lhp ?? '-' }}</td>
        <td class="col-sub">{{ $lhp->no_st ?? '-' }}</td>
        <td class="col-nama">{{ $lhp->nama }}</td>
        <td class="col-sub">{{ $lhp->nm_bidwas ? str_replace('Koordinator Pengawasan Kelompok JFA ', '', $lhp->nm_bidwas) : '-' }}</td>
        <td class="col-tgl">{{ $lhp->tgl_lhp ? \Carbon\Carbon::parse($lhp->tgl_lhp)->format('d-m-Y') : '-' }}</td>
        <td class="col-status">{{ strtoupper($lhp->status ?: 'TERBIT') }}</td>
    </tr>
    @endforeach
@endsection
