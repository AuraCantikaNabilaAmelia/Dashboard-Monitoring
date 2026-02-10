@extends('layouts.app')

@section('title', 'Monitoring Penugasan Harian')

@section('content')

    <div class="glass dark:bg-slate-900/50 bg-white rounded-2xl p-6 mb-8 border border-slate-200/50 dark:border-white/5 shadow-xl shadow-slate-200/20 dark:shadow-black/20 relative z-[40] overflow-visible text-slate-800 dark:text-white">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <h2 class="text-xl md:text-2xl font-black tracking-tight mb-1">Monitoring Penugasan</h2>
                <div class="flex items-center gap-2 text-slate-400">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                    <p class="text-sm font-medium">Berdasarkan tanggal <span class="text-blue-600 dark:text-blue-400 font-bold underline underline-offset-4 decoration-blue-500/30">{{ date('d F Y', strtotime($tanggalTerpilih)) }}</span></p>
                </div>
            </div>

            <form action="/dashboard/daily" method="GET" class="w-full lg:w-auto flex flex-col sm:flex-row items-stretch sm:items-end gap-3" id="filterForm">
                <div class="flex-1 sm:w-64">
                    <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-black mb-2 ml-1">Pilih Tanggal</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-20">
                            <i data-lucide="calendar-search" class="w-4 h-4 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>

                        <input type="hidden" name="date" id="date_hidden" value="{{ $tanggalTerpilih }}">

                        <div class="relative h-12 flex items-center bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-11 cursor-pointer hover:border-blue-500/50 focus-within:ring-2 focus-within:ring-blue-500/20 transition-all">
                            <span id="display_date" class="text-sm font-bold text-slate-800 dark:text-white">
                                {{ date('d M Y', strtotime($tanggalTerpilih)) }}
                            </span>
                            <input type="text" id="daily_datepicker" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-30" readonly>
                        </div>
                    </div>
                </div>

                <button type="submit" class="h-12 px-6 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-xl transition-all font-bold shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2 group">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500"></i>
                    <span class="text-sm">Update Data</span>
                </button>

                <div class="flex gap-2">
                    <a href="{{ route('export.daily.excel', request()->query()) }}" class="h-12 px-5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl transition-all font-bold flex items-center gap-2 border border-emerald-500/20 group" title="Export Excel">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    </a>
                    <a href="{{ route('export.daily.pdf', request()->query()) }}" class="h-12 px-5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl transition-all font-bold flex items-center gap-2 border border-rose-500/20 group" title="Export PDF">
                        <i data-lucide="file-text" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 mb-6">
        <div class="glass dark:bg-emerald-500/10 bg-emerald-50 border border-emerald-200/50 dark:border-emerald-500/20 rounded-xl px-4 py-2.5 flex items-center gap-3">
            <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                <i data-lucide="zap" class="w-4 h-4 text-emerald-500"></i>
            </div>
            <div>
                <div class="text-[10px] uppercase tracking-widest text-emerald-500 font-black">Penugasan Aktif</div>
                <div class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ $daftarPenugasan->total() }}</div>
            </div>
        </div>
    </div>

    @if($daftarPenugasan->isEmpty())
        <div class="glass dark:bg-slate-900/30 bg-white rounded-3xl p-16 text-center shadow-xl border border-slate-200/50 dark:border-white/5">
            <div class="w-20 h-20 bg-slate-100 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="clipboard-x" class="w-10 h-10 text-slate-300 dark:text-slate-600"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Tidak Ada Penugasan Aktif</h3>
            <p class="text-slate-500 dark:text-slate-400">Tidak ditemukan penugasan pegawai pada tanggal yang dipilih.</p>
        </div>
    @else

        <div class="hidden lg:block glass dark:bg-slate-900/30 bg-white rounded-2xl overflow-hidden shadow-xl shadow-slate-200/20 dark:shadow-black/20 border border-slate-200/50 dark:border-white/5">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-white/5 dark:to-white/[0.02]">
                            <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-400">Pegawai</th>
                            <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-400">Nama Penugasan</th>
                            <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-400">Periode</th>
                            <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-400">Peran</th>
                            <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-400 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach($daftarPenugasan as $itemPenugasan)
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 font-black text-sm uppercase border border-blue-500/10 transition-transform group-hover:scale-110">
                                        {{ substr($itemPenugasan->nama, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-800 dark:text-white leading-tight group-hover:text-blue-600 transition-colors">{{ $itemPenugasan->nama }}</p>
                                        <p class="text-[10px] text-slate-500 font-bold font-mono tracking-tight">{{ $itemPenugasan->nip }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 min-w-[300px] max-w-sm">
                                <a href="{{ route('st.detail', $itemPenugasan->id_st) }}" class="group/link block">
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200 line-clamp-1 group-hover/link:text-blue-500 transition-colors" title="{{ $itemPenugasan->st_nama }}">
                                        {{ $itemPenugasan->st_nama }}
                                    </p>
                                    <span class="text-[9px] font-mono font-bold text-slate-400 group-hover/link:text-blue-400/70">Lihat Detail ST &rarr;</span>
                                </a>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="inline-flex items-center gap-2 bg-slate-100 dark:bg-white/5 px-3 py-1.5 rounded-lg text-[10px] font-black text-slate-500">
                                    <span>{{ date('d M Y', strtotime($itemPenugasan->start_date)) }}</span>
                                    <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400"></i>
                                    <span>{{ date('d M Y', strtotime($itemPenugasan->end_date)) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-block px-3 py-1.5 rounded-lg bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 text-[10px] font-black uppercase tracking-wider border border-blue-500/10 leading-relaxed max-w-[200px]">
                                    {{ $itemPenugasan->peran }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 shadow-sm">
                                    <span class="relative flex h-2 w-2 mr-1">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                    On Duty
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:hidden space-y-4">
            @foreach($daftarPenugasan as $itemPenugasan)
            <div class="p-6 rounded-2xl glass dark:bg-slate-900/40 bg-white border border-slate-200/50 dark:border-white/10 shadow-lg flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 font-black text-sm uppercase">
                            {{ substr($itemPenugasan->nama, 0, 2) }}
                        </div>
                        <div>
                            <p class="font-extrabold text-slate-800 dark:text-white">{{ $itemPenugasan->nama }}</p>
                            <p class="text-[10px] text-slate-500 font-bold font-mono tracking-tight">{{ $itemPenugasan->nip }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-[9px] font-black rounded-lg uppercase border border-emerald-500/20">
                        On Duty
                    </span>
                </div>

                <div class="py-4 border-y border-slate-100 dark:border-white/5">
                    <p class="text-[9px] uppercase tracking-widest text-slate-400 font-black mb-1">Penugasan:</p>
                    <a href="{{ route('st.detail', $itemPenugasan->id_st) }}" class="text-sm font-bold text-slate-800 dark:text-white hover:text-blue-500 transition-colors leading-tight block">
                        {{ $itemPenugasan->st_nama }}
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[9px] uppercase tracking-widest text-slate-400 font-black mb-1">Peran:</p>
                        <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 px-3 py-1.5 bg-blue-500/10 rounded-lg inline-block uppercase tracking-wider">
                            {{ $itemPenugasan->peran }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-widest text-slate-400 font-black mb-1">Periode:</p>
                        <div class="flex items-center flex-wrap gap-1 text-[10px] font-extrabold text-slate-500">
                             <span>{{ date('d M Y', strtotime($itemPenugasan->start_date)) }}</span>
                             <i data-lucide="minus" class="w-3 h-3"></i>
                             <span>{{ date('d M Y', strtotime($itemPenugasan->end_date)) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($daftarPenugasan->hasPages())
        <div class="mt-6 glass dark:bg-slate-900/30 bg-white rounded-2xl px-6 py-4 border border-slate-200/50 dark:border-white/5 shadow-lg">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    Menampilkan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftarPenugasan->firstItem() }}</span> - <span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftarPenugasan->lastItem() }}</span> dari <span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftarPenugasan->total() }}</span> data
                </div>
                <div class="flex gap-1">
                    @if($daftarPenugasan->onFirstPage())
                        <span class="px-4 py-2 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-400 cursor-not-allowed text-sm font-medium">Prev</span>
                    @else
                        <a href="{{ $daftarPenugasan->appends(request()->query())->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-white/10 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:border-blue-300 dark:hover:border-blue-500/30 hover:text-blue-600 transition-all text-sm font-medium">Prev</a>
                    @endif

                    @foreach ($daftarPenugasan->appends(request()->query())->getUrlRange(max(1, $daftarPenugasan->currentPage() - 2), min($daftarPenugasan->lastPage(), $daftarPenugasan->currentPage() + 2)) as $page => $url)
                        @if ($page == $daftarPenugasan->currentPage())
                            <span class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-bold shadow-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 rounded-lg bg-white dark:bg-white/10 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:border-blue-300 dark:hover:border-blue-500/30 hover:text-blue-600 transition-all text-sm font-medium">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($penugasan->hasMorePages())
                        <a href="{{ $penugasan->appends(request()->query())->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-white/10 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:border-blue-300 dark:hover:border-blue-500/30 hover:text-blue-600 transition-all text-sm font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-400 cursor-not-allowed text-sm font-medium">Next</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    @endif
@endsection

@push('scripts')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>

    .flatpickr-calendar {
        background: rgba(15, 23, 42, 0.98) !important;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow:
            0 25px 50px -12px rgba(0, 0, 0, 0.5),
            0 0 0 1px rgba(255, 255, 255, 0.05) inset !important;
        border-radius: 20px !important;
        padding: 20px !important;
        font-family: inherit !important;
        z-index: 99999 !important;
        width: auto !important;
        min-width: 300px !important;
        animation: calendarSlideIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    @keyframes calendarSlideIn {
        from {
            opacity: 0;
            transform: translateY(-8px) scale(0.96);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    [data-theme="light"] .flatpickr-calendar {
        background: rgba(255, 255, 255, 0.98) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        box-shadow:
            0 25px 50px -12px rgba(0, 0, 0, 0.12),
            0 0 0 1px rgba(0, 0, 0, 0.03) inset !important;
    }

    .flatpickr-months {
        margin-bottom: 16px !important;
        padding: 0 !important;
        background: transparent !important;
        height: auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    .flatpickr-months .flatpickr-month {
        background: transparent !important;
        height: auto !important;
        padding: 0 !important;
        flex: 1 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .flatpickr-prev-month,
    .flatpickr-next-month {
        position: static !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 36px !important;
        height: 36px !important;
        padding: 0 !important;
        border-radius: 50% !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        color: #94a3b8 !important;
        background: rgba(255, 255, 255, 0.05) !important;
        fill: currentColor !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    .flatpickr-prev-month:hover,
    .flatpickr-next-month:hover {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        transform: scale(1.08) !important;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4) !important;
    }

    .flatpickr-prev-month:active,
    .flatpickr-next-month:active {
        transform: scale(0.95) !important;
    }

    [data-theme="light"] .flatpickr-prev-month,
    [data-theme="light"] .flatpickr-next-month {
        background: rgba(0, 0, 0, 0.03) !important;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        color: #64748b !important;
    }

    [data-theme="light"] .flatpickr-prev-month:hover,
    [data-theme="light"] .flatpickr-next-month:hover {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #ffffff !important;
        border-color: transparent !important;
    }

    .flatpickr-prev-month svg,
    .flatpickr-next-month svg {
        width: 16px !important;
        height: 16px !important;
        fill: none !important;
        stroke: currentColor !important;
        stroke-width: 2.5 !important;
    }

    .flatpickr-current-month {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        padding: 0 !important;
        height: auto !important;
        position: static !important;
        width: auto !important;
        left: auto !important;
        overflow: visible !important;
    }

    .flatpickr-monthDropdown-months {
        appearance: none !important;
        -webkit-appearance: none !important;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        color: #f1f5f9 !important;
        font-weight: 600 !important;
        font-size: 15px !important;
        padding: 4px 2px !important;
        margin: 0 !important;
        cursor: pointer !important;
        transition: color 0.2s ease !important;
        text-transform: capitalize !important;
    }

    .flatpickr-monthDropdown-months:hover,
    .flatpickr-monthDropdown-months:focus {
        color: #60a5fa !important;
    }

    [data-theme="light"] .flatpickr-monthDropdown-months {
        color: #1e293b !important;
    }

    [data-theme="light"] .flatpickr-monthDropdown-months:hover,
    [data-theme="light"] .flatpickr-monthDropdown-months:focus {
        color: #3b82f6 !important;
    }

    .flatpickr-monthDropdown-months option {
        background: #1e293b !important;
        color: #e2e8f0 !important;
        padding: 8px 12px !important;
        font-weight: 500 !important;
    }

    [data-theme="light"] .flatpickr-monthDropdown-months option {
        background: #ffffff !important;
        color: #334155 !important;
    }

    .numInputWrapper {
        width: auto !important;
        height: auto !important;
        position: relative !important;
    }

    .numInputWrapper input.cur-year {
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        color: #f1f5f9 !important;
        font-weight: 600 !important;
        font-size: 15px !important;
        padding: 4px 2px !important;
        margin: 0 !important;
        width: 52px !important;
        text-align: center !important;
        cursor: pointer !important;
        transition: color 0.2s ease !important;
    }

    .numInputWrapper input.cur-year:hover,
    .numInputWrapper input.cur-year:focus {
        color: #60a5fa !important;
    }

    [data-theme="light"] .numInputWrapper input.cur-year {
        color: #1e293b !important;
    }

    [data-theme="light"] .numInputWrapper input.cur-year:hover,
    [data-theme="light"] .numInputWrapper input.cur-year:focus {
        color: #3b82f6 !important;
    }

    .numInputWrapper span {
        display: none !important;
    }

    .flatpickr-weekdays {
        background: transparent !important;
        margin: 12px 0 8px !important;
        padding: 0 !important;
    }

    .flatpickr-weekday {
        background: transparent !important;
        color: #64748b !important;
        font-weight: 700 !important;
        font-size: 10px !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
    }

    [data-theme="light"] .flatpickr-weekday {
        color: #94a3b8 !important;
    }

    .flatpickr-day {
        color: #e2e8f0 !important;
        border-radius: 10px !important;
        height: 38px !important;
        line-height: 38px !important;
        font-weight: 500 !important;
        font-size: 13px !important;
        border: none !important;
        margin: 2px !important;
        transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    [data-theme="light"] .flatpickr-day {
        color: #334155 !important;
    }

    .flatpickr-day:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        transform: scale(1.05) !important;
    }

    [data-theme="light"] .flatpickr-day:hover {
        background: rgba(59, 130, 246, 0.1) !important;
        color: #3b82f6 !important;
    }

    .flatpickr-day.selected {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4) !important;
        transform: scale(1.05) !important;
        border-color: transparent !important;
    }

    .flatpickr-day.selected:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
    }

    .flatpickr-day.today:not(.selected) {
        background: rgba(59, 130, 246, 0.1) !important;
        border: 2px solid rgba(59, 130, 246, 0.4) !important;
        color: #60a5fa !important;
        font-weight: 700 !important;
    }

    [data-theme="light"] .flatpickr-day.today:not(.selected) {
        background: rgba(59, 130, 246, 0.08) !important;
        border: 2px solid rgba(59, 130, 246, 0.3) !important;
        color: #2563eb !important;
    }

    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
        color: #94a3b8 !important;
        opacity: 0.1 !important;
        pointer-events: none !important;
    }

    [data-theme="light"] .flatpickr-day.flatpickr-disabled,
    [data-theme="light"] .flatpickr-day.prevMonthDay,
    [data-theme="light"] .flatpickr-day.nextMonthDay {
        color: #94a3b8 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#daily_datepicker", {
            dateFormat: "Y-m-d",
            prevArrow: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>',
            nextArrow: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>',
            defaultDate: "{{ $tanggalTerpilih }}",
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length > 0) {
                    const selected = selectedDates[0];
                    document.getElementById('date_hidden').value = dateStr;

                    const formatDate = (date) => {
                        return date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                    };
                    document.getElementById('display_date').textContent = formatDate(selected);

                    setTimeout(() => document.getElementById('filterForm').submit(), 100);
                }
            }
        });
        lucide.createIcons();
    });
</script>
@endpush
