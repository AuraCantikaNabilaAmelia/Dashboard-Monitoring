@extends('exports.layout')

@section('title', 'Rekap Penugasan Pegawai')
@section('orientation', 'landscape')
@section('judul', $modeRekap ? 'Rekap Penugasan Pegawai (Bulanan)' : 'Rincian Penugasan Pegawai (Bulanan)')

@section('meta')
    @if($modeRekap)
    <tr>
        <td class="lbl">Bidang</td>
        <td class="sep">:</td>
        <td class="val">{{ $bidangLabel }}</td>
    </tr>
    @endif
    @if(!empty($startDate) && !empty($endDate))
    <tr>
        <td class="lbl">Periode Laporan</td>
        <td class="sep">:</td>
        <td class="val">{{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('DD MMM YYYY') }} s/d {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
    </tr>
    @endif
    <tr>
        <td class="lbl">{{ $modeRekap ? 'Jumlah Pegawai' : 'Jumlah Penugasan' }}</td>
        <td class="sep">:</td>
        <td class="val">{{ $modeRekap ? $rekapPenugasan->count() . ' pegawai' : $rincianPenugasan->count() . ' surat tugas' }}</td>
    </tr>
@endsection

@section('thead')
    @if($modeRekap)
        <td width="40" class="c">No.</td>
        <td>Nama Pegawai</td>
        <td width="170" class="c">NIP</td>
        <td width="170" class="c">Total Penugasan</td>
    @else
        <td width="26" class="c">No.</td>
        <td width="150">Nama Pegawai</td>
        <td width="120">Nomor ST</td>
        <td>Nama Penugasan</td>
        <td width="110">Peran</td>
        <td width="72" class="c">Tgl. Mulai</td>
        <td width="72" class="c">Tgl. Selesai</td>
        <td width="80" class="c">Status</td>
    @endif
@endsection

@section('tbody')
    @if($modeRekap)
        @forelse($rekapPenugasan as $index => $pegawai)
        <tr class="{{ $index % 2 === 0 ? 'baris-ganjil' : 'baris-genap' }}">
            <td class="col-no">{{ $index + 1 }}</td>
            <td class="col-nama">{{ $pegawai->nama }}</td>
            <td class="col-mono c">{{ $pegawai->nip }}</td>
            <td class="col-num">{{ $pegawai->total }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="c" style="padding:18px; color:#94a3b8;">Tidak ada data pada periode ini</td></tr>
        @endforelse
    @else
        @forelse($rincianPenugasan as $index => $tugas)
        <tr class="{{ $index % 2 === 0 ? 'baris-ganjil' : 'baris-genap' }}">
            <td class="col-no">{{ $index + 1 }}</td>
            <td class="col-nama">{{ $tugas->nama }}</td>
            <td class="col-mono">{{ $tugas->no_surat_tugas ?? '-' }}</td>
            <td class="col-sub">{{ $tugas->nama_penugasan }}</td>
            <td class="col-sub">{{ $tugas->peran ?: '-' }}</td>
            <td class="col-tgl">{{ $tugas->start_date ? \Carbon\Carbon::parse($tugas->start_date)->format('d-m-Y') : '-' }}</td>
            <td class="col-tgl">{{ $tugas->end_date ? \Carbon\Carbon::parse($tugas->end_date)->format('d-m-Y') : '-' }}</td>
            <td class="col-status">{{ strtoupper($tugas->status_st ?? '-') }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:18px; color:#94a3b8;">Tidak ada penugasan pada periode ini</td></tr>
        @endforelse
    @endif
@endsection
