@extends('layouts.app')

@section('title', 'Detail Surat Tugas')

@section('content')
<div class="mb-6">
    <a href="{{ url()->previous() }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 flex items-center space-x-2 text-sm font-bold transition-colors">
        <i data-lucide="chevron-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
    <div class="lg:col-span-2 space-y-8">
        <div class="glass p-8 rounded-3xl shadow-xl">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="px-3 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-black rounded-lg uppercase tracking-widest mb-2 inline-block border border-blue-500/10">Surat Tugas</span>
                    <h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $st->nama_penugasan }}</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-1 font-mono text-xs font-bold">ID: #{{ $st->id_st }}</p>
                </div>
                <div class="text-right">
                    @php
                        $statusClass = match(strtolower($st->status_st)) {
                            'batal' => 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20',
                            'final', 'selesai' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                            'tidak aktif' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
                            default => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20'
                        };
                        $statusIcon = match(strtolower($st->status_st)) {
                            'batal' => 'x-circle',
                            'final', 'selesai' => 'check-circle',
                            'tidak aktif' => 'minus-circle',
                            default => 'clock'
                        };
                    @endphp
                    <span class="px-4 py-2 {{ $statusClass }} text-sm font-black rounded-xl border shadow-sm inline-flex items-center gap-2">
                        <i data-lucide="{{ $statusIcon }}" class="w-4 h-4"></i>
                        {{ $st->status_st }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 dark:border-white/5 pt-8">
                <div class="space-y-6">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">Tanggal Mulai</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $st->start_date ? date('d F Y', strtotime($st->start_date)) : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">Nomor Surat Tugas</p>
                        <p class="text-slate-700 dark:text-slate-300 font-bold font-mono">{{ $st->no_surat_tugas ?? '-' }}</p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">Tanggal Selesai</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $st->end_date ? date('d F Y', strtotime($st->end_date)) : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">ID Penugasan</p>
                        <p class="text-slate-700 dark:text-slate-300 font-bold font-mono">ST-{{ $st->id_st }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($st->nomor_lhp)
        <div class="glass p-8 rounded-3xl border-emerald-500/30 shadow-lg shadow-emerald-500/5">
            <h3 class="text-xl font-black mb-6 text-emerald-600 dark:text-emerald-400 flex items-center gap-3">
                <div class="p-2 bg-emerald-500/20 rounded-lg"><i data-lucide="file-check" class="w-6 h-6"></i></div>
                Laporan Hasil Pengawasan (LHP)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-5 bg-slate-100/50 dark:bg-white/5 rounded-2xl border border-slate-200/50 dark:border-white/5 shadow-inner">
                    <p class="text-slate-600 dark:text-slate-400 text-[10px] uppercase font-black mb-1 tracking-wider">Nomor LHP</p>
                    <p class="text-lg font-black text-slate-800 dark:text-white font-mono">{{ $st->nomor_lhp }}</p>
                </div>
                <div class="p-5 bg-slate-100/50 dark:bg-white/5 rounded-2xl border border-slate-200/50 dark:border-white/5 shadow-inner">
                    <p class="text-slate-600 dark:text-slate-400 text-[10px] uppercase font-black mb-1 tracking-wider">Tanggal LHP</p>
                    <p class="text-lg font-black text-slate-800 dark:text-white">{{ date('d F Y', strtotime($st->tanggal_lhp)) }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="glass p-8 rounded-3xl shadow-xl">
            <h3 class="text-xl font-black mb-6 flex items-center gap-3">
                <i data-lucide="users" class="w-6 h-6 text-blue-500"></i>
                Tim Penugasan
            </h3>
            <div class="grid grid-cols-1 gap-4">
                @foreach($tim as $anggota)
                <div class="flex flex-col md:flex-row md:items-center justify-between p-6 rounded-2xl bg-slate-50/50 dark:bg-white/5 border border-slate-100 dark:border-white/5 group hover:bg-slate-100 dark:hover:bg-white/10 transition-all gap-4">
                    <div class="flex items-center space-x-5 min-w-0">
                        <div class="w-12 h-12 bg-blue-600/10 dark:bg-slate-800 rounded-2xl flex items-center justify-center font-black text-blue-700 dark:text-slate-400 border border-blue-500/10 flex-shrink-0">
                            {{ substr($anggota->nama, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-black text-slate-800 dark:text-white text-lg group-hover:text-blue-600 transition-colors truncate" title="{{ $anggota->nama }}">{{ $anggota->nama }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-500 font-bold font-mono">{{ $anggota->nip }}</p>
                        </div>
                    </div>
                    <div class="flex-1 md:text-right">
                        <span class="inline-block text-[10px] font-black text-blue-700 dark:text-blue-400 bg-blue-500/10 dark:bg-blue-500/20 px-4 py-2 rounded-xl uppercase tracking-tighter border border-blue-500/10 leading-relaxed md:max-w-[400px]">
                            {{ $anggota->peran }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="space-y-8">
        <div class="glass p-8 rounded-3xl bg-blue-600/5 dark:bg-blue-600/10 border border-blue-500/20 dark:border-blue-500/20 shadow-lg">
            <h3 class="font-black mb-4 flex items-center gap-2">
                <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
                Meta Data
            </h3>
            <div class="space-y-4">
                <div class="space-y-1">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-500">Sektor:</p>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Keuangan Negara</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-500">Prioritas:</p>
                    <p class="text-sm font-bold text-amber-600 dark:text-amber-400">High Priority</p>
                </div>
                <div class="mt-6 pt-6 border-t border-slate-300 dark:border-white/10">
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-bold">Aktivitas ini terpantau dalam dashboard monitoring untuk analisis beban kerja secara komprehensif.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
