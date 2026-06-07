@extends('exports.layout')

@section('title', 'Monitoring Penugasan Harian')
@section('orientation', 'landscape')
@section('judul', 'Monitoring Penugasan Harian')

@section('meta')
    <tr>
        <td class="lbl">Tanggal Laporan</td>
        <td class="sep">:</td>
        <td class="val">{{ \Carbon\Carbon::parse($tanggalLaporan)->locale('id')->isoFormat('DD MMMM YYYY') }}</td>
    </tr>
    <tr>
        <td class="lbl">Jumlah Penugasan</td>
        <td class="sep">:</td>
        <td class="val">{{ $daftarAktivitasHarian->count() }} penugasan aktif</td>
    </tr>
@endsection

@section('thead')
    <td width="26" class="c">No.</td>
    <td width="150">Nama Pegawai</td>
    <td width="110" class="c">NIP</td>
    <td width="120">Nomor ST</td>
    <td>Nama Penugasan</td>
    <td width="110">Peran</td>
    <td width="80" class="c">Status</td>
@endsection

@section('tbody')
    @forelse($daftarAktivitasHarian as $index => $aktivitas)
    <tr class="{{ $index % 2 === 0 ? 'baris-ganjil' : 'baris-genap' }}">
        <td class="col-no">{{ $index + 1 }}</td>
        <td class="col-nama">{{ $aktivitas->nama }}</td>
        <td class="col-mono c">{{ $aktivitas->nip }}</td>
        <td class="col-mono">{{ $aktivitas->no_surat_tugas ?? '-' }}</td>
        <td class="col-sub">{{ $aktivitas->st_nama }}</td>
        <td class="col-sub">{{ $aktivitas->peran ?: '-' }}</td>
        <td class="col-status">{{ strtoupper($aktivitas->status_st ?? '-') }}</td>
    </tr>
    @empty
    <tr><td colspan="7" class="c" style="padding:18px; color:#94a3b8;">Tidak ada penugasan aktif pada tanggal ini</td></tr>
    @endforelse
@endsection
