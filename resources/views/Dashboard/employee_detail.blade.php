@extends('layouts.app')

@section('title', 'Detail Penugasan Pegawai')

@section('content')
<div class="mb-8">
    <a href="{{ route('dashboard.monthly', ['month' => $month, 'year' => $year, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center space-x-2 text-slate-500 hover:text-blue-600 transition-colors font-bold text-sm mb-4 group">
        <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
        <span>Kembali ke Monitoring Bulanan</span>
    </a>

<div class="glass dark:bg-white/10 bg-white p-8 rounded-3xl flex flex-col md:flex-row md:items-center justify-between gap-6 border border-blue-500/10 shadow-xl shadow-blue-500/5">
        <div class="flex items-center space-x-6">
            <div class="w-20 h-20 bg-blue-500/20 rounded-2xl flex items-center justify-center text-3xl font-black text-blue-500 border border-blue-500/30">
                {{ substr($employee->nama, 0, 1) }}
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-1 tracking-tight">{{ $employee->nama }}</h1>
                <p class="text-slate-500 font-mono font-bold tracking-tight bg-slate-100 dark:bg-white/5 px-2 py-1 rounded inline-block text-xs">NIP: {{ $employee->nip }}</p>
            </div>
        </div>

        <div class="flex flex-col items-end">
            <span class="text-[10px] uppercase tracking-widest text-slate-400 font-black mb-1">Periode Terview</span>
            <div class="bg-blue-600/10 px-4 py-2 rounded-xl border border-blue-500/20">
                <span class="text-blue-500 font-black">
                    @if($startDate && $endDate)
                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    @else
                        {{ \Carbon\Carbon::create(null, $month)->format('F') }} {{ $year }}
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

<div class="glass dark:bg-white/10 bg-white rounded-3xl overflow-hidden shadow-xl border border-white/10">
    <div class="px-8 py-6 border-b border-slate-100 dark:border-white/10 flex justify-between items-center bg-slate-50/30 dark:bg-white/5">
        <h3 class="font-bold text-lg flex items-center space-x-3 text-slate-800 dark:text-slate-200">
            <div class="p-2 bg-blue-500/20 rounded-lg text-blue-500">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
            </div>
            <span>Daftar Penugasan ({{ count($assignments) }})</span>
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left table-fixed">
            <thead>
                <tr class="border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                    <th class="w-2/5 px-8 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-300">Nama Penugasan</th>
                    <th class="w-1/5 px-8 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-300">No Surat Tugas</th>
                    <th class="w-1/5 px-8 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-300">Peran</th>
                    <th class="w-1/4 px-8 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-300">Waktu</th>
                    <th class="w-[120px] px-8 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse($assignments as $st)
                <tr class="group hover:bg-slate-50 dark:hover:bg-white/5 transition-all">
                    <td class="px-8 py-5">
                        <p class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 transition-colors truncate" title="{{ $st->nama_penugasan }}">{{ $st->nama_penugasan }}</p>
                    </td>
                    <td class="px-8 py-5">
                        <span class="font-mono text-[10px] font-bold px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-white/5 truncate block">{{ $st->no_surat_tugas }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-[10px] font-black uppercase px-3 py-1.5 rounded-full shadow-sm truncate block
                            {{ $st->peran == 'Ketua Tim' ? 'bg-blue-500/10 text-blue-500 border border-blue-500/20' : 'bg-slate-500/10 text-slate-600 dark:text-slate-300 border border-slate-500/20' }}" title="{{ $st->peran }}">
                            {{ $st->peran }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center space-x-2 text-[10px] font-bold text-slate-600 dark:text-slate-300 bg-slate-100/50 dark:bg-slate-800 px-3 py-1.5 rounded-lg w-fit whitespace-nowrap">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-500"></i>
                            <span>{{ \Carbon\Carbon::parse($st->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($st->end_date)->format('d M Y') }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <a href="{{ route('st.detail', $st->id_st) }}" class="inline-flex items-center justify-center bg-slate-900 dark:bg-blue-600 hover:bg-blue-600 dark:hover:bg-blue-700 text-white w-10 h-10 rounded-xl transition-all font-black shadow-lg shadow-blue-500/10 group" title="Lihat Detail ST">
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-24 text-center">
                        <div class="w-20 h-20 bg-slate-500/5 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="inbox" class="w-10 h-10 text-slate-500 opacity-20"></i>
                        </div>
                        <p class="text-slate-500 font-black uppercase tracking-widest text-xs">Tidak ada penugasan di periode ini</p>
                        <p class="text-slate-400 text-[10px] mt-2">Coba sesuaikan filter bulan atau tanggal Bapak.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
