@extends('exports.layout')

@section('title', 'Daftar Surat Tugas')
@section('orientation', 'landscape')
@section('judul', 'Daftar Surat Tugas (ST)')

@section('thead')
    <td width="26" class="c">No.</td>
    <td width="135">Nomor ST</td>
    <td>Nama Penugasan</td>
    <td width="150">Bidang</td>
    <td width="72" class="c">Tgl. Mulai</td>
    <td width="72" class="c">Tgl. Selesai</td>
    <td width="88" class="c">Status</td>
@endsection

@section('tbody')
    @foreach($daftarSuratTugas as $index => $suratTugas)
    <tr class="{{ $index % 2 === 0 ? 'baris-ganjil' : 'baris-genap' }}">
        <td class="col-no">{{ $index + 1 }}</td>
        <td class="col-mono">{{ $suratTugas->no_st ?? '-' }}</td>
        <td class="col-nama">{{ $suratTugas->nama }}</td>
        <td class="col-sub">{{ $suratTugas->nm_bidwas ? str_replace('Koordinator Pengawasan Kelompok JFA ', '', $suratTugas->nm_bidwas) : '-' }}</td>
        <td class="col-tgl">{{ $suratTugas->start_date ? \Carbon\Carbon::parse($suratTugas->start_date)->format('d-m-Y') : '-' }}</td>
        <td class="col-tgl">{{ $suratTugas->end_date ? \Carbon\Carbon::parse($suratTugas->end_date)->format('d-m-Y') : '-' }}</td>
        <td class="col-status">{{ strtoupper($suratTugas->status ?? '-') }}</td>
    </tr>
    @endforeach
@endsection
