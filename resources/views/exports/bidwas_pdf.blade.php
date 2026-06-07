@extends('exports.layout')

@section('title', 'Rekapitulasi Capaian Kinerja')
@section('orientation', 'landscape')
@section('judul', 'Rekapitulasi Capaian Kinerja Per Bidang')

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
    <td>Nama Bidang</td>
    <td width="90" class="c">Jml Pegawai</td>
    <td width="80" class="c">Total ST</td>
    <td width="80" class="c">Total LHP</td>
    <td width="90" class="c">Capaian</td>
@endsection

@section('tbody')
    @foreach($rekapCapaianBidang as $index => $bidang)
    @php $persen = $bidang->total_st > 0 ? ($bidang->total_lhp / $bidang->total_st) * 100 : 0; @endphp
    <tr class="{{ $index % 2 === 0 ? 'baris-ganjil' : 'baris-genap' }}">
        <td class="col-no">{{ $index + 1 }}</td>
        <td class="col-nama">{{ $bidang->nm_bidwas }}</td>
        <td class="c">{{ $bidang->total_pegawai }}</td>
        <td class="c">{{ $bidang->total_st }}</td>
        <td class="c">{{ $bidang->total_lhp }}</td>
        <td class="col-num">{{ number_format($persen, 1) }}%</td>
    </tr>
    @endforeach
@endsection
