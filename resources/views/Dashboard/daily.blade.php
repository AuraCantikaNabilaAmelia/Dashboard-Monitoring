@extends('layouts.app')

@section('title', 'Monitoring Penugasan Harian')

@section('content')
<div class="glass p-8 rounded-3xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-xl font-bold">Daftar Penugasan Aktif</h2>
            <p class="text-slate-400 text-sm">Menampilkan penugasan pada tanggal {{ date('d F Y', strtotime($date)) }}</p>
        </div>
        <form action="/dashboard/daily" method="GET" class="flex items-center gap-3">
            <div class="glass dark:bg-white/5 bg-slate-50 rounded-xl px-4 py-2.5 flex items-center border border-slate-200 dark:border-white/10 focus-within:border-blue-500/50 transition-all h-[48px]">
                <span class="text-[9px] uppercase font-black text-slate-500 mr-3">TANGGAL:</span>
                <input type="date" name="date" value="{{ $date }}" class="bg-transparent border-none outline-none text-xs text-slate-900 dark:text-white font-bold" onchange="this.form.submit()">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 h-[48px] px-6 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest text-white shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Update
            </button>
        </form>
    </div>

    @if($penugasan->isEmpty())
        <i data-lucide="clipboard-x" class="w-16 h-16 mb-4 opacity-10"></i>
        <p class="font-bold">Tidak ada penugasan aktif pada tanggal ini.</p>
    @else
    <div class="space-y-4">
        {{-- Desktop Table View --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/10 bg-white/5">
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-600 dark:text-slate-300">Pegawai</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-600 dark:text-slate-300">Surat Tugas</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-600 dark:text-slate-300">Periode</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-600 dark:text-slate-300">Peran</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-600 dark:text-slate-300 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach($penugasan as $item)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group">
                        <td class="px-6 py-4 min-w-[200px]">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-500 font-bold text-sm uppercase border border-blue-500/10 flex-shrink-0">
                                    {{ substr($item->nama, 0, 2) }}
                                </div>
                                <div class="truncate">
                                    <p class="font-extrabold text-slate-800 dark:text-white group-hover:text-blue-600 transition-colors truncate" title="{{ $item->nama }}">{{ $item->nama }}</p>
                                    <p class="text-[10px] text-slate-500 font-bold font-mono tracking-tight">{{ $item->nip }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 min-w-[200px] max-w-xs">
                            <a href="{{ route('st.detail', $item->id_st) }}" class="text-sm font-medium hover:text-blue-500 transition-colors block leading-snug">
                                {{ $item->st_nama }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2 text-[10px] font-semibold text-slate-500">
                                <span>{{ date('d M Y', strtotime($item->start_date)) }}</span>
                                <i data-lucide="arrow-right" class="w-3 h-3 flex-shrink-0"></i>
                                <span>{{ date('d M Y', strtotime($item->end_date)) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="inline-block text-[10px] font-extrabold text-blue-600 dark:text-blue-400 bg-blue-500/10 dark:bg-blue-500/20 px-3 py-1.5 rounded-lg uppercase tracking-wider border border-blue-500/10 leading-normal max-w-[250px]">
                                {{ $item->peran }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-3 py-1.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black rounded-lg uppercase tracking-widest border border-emerald-500/20 shadow-sm whitespace-nowrap">
                                On Duty
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="lg:hidden space-y-4">
            @foreach($penugasan as $item)
            <div class="p-6 rounded-2xl bg-slate-50/50 dark:bg-white/5 border border-slate-100 dark:border-white/5 flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-500 font-bold text-sm uppercase">
                            {{ substr($item->nama, 0, 2) }}
                        </div>
                        <div>
                            <p class="font-extrabold text-slate-800 dark:text-white">{{ $item->nama }}</p>
                            <p class="text-[10px] text-slate-500 font-bold font-mono">{{ $item->nip }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[9px] font-black rounded-lg uppercase border border-emerald-500/20">
                        On Duty
                    </span>
                </div>
                
                <div class="py-3 border-y border-slate-100 dark:border-white/5">
                    <p class="text-[9px] uppercase tracking-widest text-slate-400 font-black mb-1">Penugasan:</p>
                    <a href="{{ route('st.detail', $item->id_st) }}" class="text-sm font-bold text-slate-700 dark:text-slate-200 hover:text-blue-500 transition-colors">
                        {{ $item->st_nama }}
                    </a>
                </div>

                <div class="flex flex-col gap-3">
                    <div>
                        <p class="text-[9px] uppercase tracking-widest text-slate-400 font-black mb-1">Peran:</p>
                        <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400 px-3 py-1.5 bg-blue-500/5 dark:bg-blue-500/10 rounded-lg inline-block uppercase">
                            {{ $item->peran }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-widest text-slate-400 font-black mb-1">Periode:</p>
                        <div class="flex items-center space-x-2 text-[10px] font-bold text-slate-500">
                             <span>{{ date('d M Y', strtotime($item->start_date)) }}</span>
                             <i data-lucide="arrow-right" class="w-3 h-3"></i>
                             <span>{{ date('d M Y', strtotime($item->end_date)) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
