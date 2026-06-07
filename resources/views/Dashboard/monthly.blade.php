@extends('layouts.app')

@section('title', 'Monitoring Penugasan Bulanan')

@section('content')

    <div class="glass dark:bg-slate-900/50 bg-white rounded-2xl p-6 mb-8 border border-slate-200/50 dark:border-white/5 shadow-xl shadow-slate-200/20 dark:shadow-black/20 relative z-[40] overflow-visible">
        <form action="/dashboard/monthly" method="GET" id="filterForm" class="space-y-6">
            <input type="hidden" name="bidwas" id="bidwas_input" value="{{ $idBidwas }}">
            <input type="hidden" name="start_date" id="start_date_hidden" value="{{ $tanggalMulai }}">
            <input type="hidden" name="end_date" id="end_date_hidden" value="{{ $tanggalSelesai }}">

            <div class="flex flex-col lg:flex-row gap-4 items-end">

                <div class="flex-1 min-w-0 w-full">
                    <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-black mb-2 ml-1">Pencarian Pegawai</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input type="text" name="search" value="{{ $kataKunci }}"
                               placeholder="Cari Nama Pegawai atau NIP..."
                               class="w-full h-12 pl-11 pr-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium"
                               autocomplete="off">
                    </div>
                </div>

                <div class="w-full lg:w-80">
                    <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-black mb-2 ml-1">Periode Penugasan</label>
                    <div class="relative group" id="date_range_container">
                        <div class="absolute inset-0 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl transition-all group-hover:border-blue-500/50 group-hover:bg-blue-50/5 dark:group-hover:bg-blue-500/10"></div>

                        <div class="relative flex items-center h-12 px-1 cursor-pointer">

                            <div class="flex-1 flex items-center gap-3 pl-3 pr-2 border-r border-slate-200 dark:border-white/10 h-8">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                <span id="display_start_date" class="text-sm font-medium {{ $tanggalMulai ? 'text-slate-800 dark:text-white' : 'text-slate-400' }}">
                                    {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') : 'Mulai' }}
                                </span>
                            </div>

                            <div class="px-2 text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </div>

                            <div class="flex-1 flex items-center gap-3 pl-2 pr-3 h-8">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                <span id="display_end_date" class="text-sm font-medium {{ $tanggalSelesai ? 'text-slate-800 dark:text-white' : 'text-slate-400' }}">
                                    {{ $tanggalSelesai ? \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y') : 'Selesai' }}
                                </span>
                            </div>

                            <input type="text" id="date_range_picker"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                   placeholder="Select Date Range"
                                   readonly>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-48 relative z-[45]">
                    <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-black mb-2 ml-1">Bidang</label>

                    @if($bidangTerkunci)
                    <div class="h-12 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl flex items-center justify-between gap-2 cursor-not-allowed select-none" title="{{ $bidwasTerpilih->nm_bidwas ?? '' }}">
                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400 truncate">
                            {{ $bidwasTerpilih ? $bidwasTerpilih->short_name : '-' }}
                        </span>
                        <i data-lucide="lock" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                    </div>
                    @else
                    <div class="custom-dropdown-container w-full">
                        <div class="status-filter-dropdown dark:bg-white/5 bg-white h-12 border border-slate-200 dark:border-white/10 rounded-xl">
                            <input hidden="" class="sr-only" name="bidwas-dropdown" id="bidwas-dropdown" type="checkbox" />
                            <label for="bidwas-dropdown" class="status-filter-trigger h-full rounded-xl">
                                <span class="text-blue-600 dark:text-blue-400 font-bold text-sm truncate">{{ $bidwasTerpilih ? $bidwasTerpilih->short_name : 'Semua Bidang' }}</span>
                            </label>
                            <ul class="status-filter-list rounded-xl border border-white/10 shadow-2xl backdrop-blur-xl" role="list">
                                <li class="status-filter-listitem">
                                    <div onclick="applyFilter('bidwas', '')" class="status-filter-article {{ !$idBidwas ? 'active' : '' }}">Semua Bidang</div>
                                </li>
                                @foreach($daftarBidwas as $b)
                                <li class="status-filter-listitem">
                                    <div onclick="applyFilter('bidwas', '{{ $b->id_bidwas }}')" class="status-filter-article {{ $idBidwas == $b->id_bidwas ? 'active' : '' }}">{{ $b->short_name }}</div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="flex items-center gap-2 w-full lg:w-auto">
                    <button type="submit" class="flex-1 lg:flex-none h-12 px-6 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-xl transition-all font-bold shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2 group">
                        <i data-lucide="filter" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm">Filter</span>
                    </button>
                    <div class="flex gap-2">
                        <a href="{{ route('export.monthly.excel', request()->query()) }}" class="h-12 px-4 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl transition-all font-bold flex items-center gap-2 border border-emerald-500/20 group" title="Export Excel">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                        </a>
                        <a href="{{ route('export.monthly.pdf', request()->query()) }}" class="h-12 px-4 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl transition-all font-bold flex items-center gap-2 border border-rose-500/20 group" title="Export PDF">
                            <i data-lucide="file-text" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                        </a>
                    </div>
                    @if($kataKunci || $idBidwas || $tanggalMulai)
                        <a href="/dashboard/monthly" class="h-12 w-12 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-500 rounded-xl transition-all flex items-center justify-center border border-red-200 dark:border-red-500/20 group" title="Reset Filter">
                            <i data-lucide="x" class="w-4 h-4 group-hover:rotate-90 transition-transform"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

@php
    $daftarNamaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $namaBulan = $daftarNamaBulan[(int)$bulanTerpilih] ?? '-';
    $labelPeriode = $tanggalMulai && $tanggalSelesai
        ? \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') . ' – ' . \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y')
        : $namaBulan . ' ' . $tahunTerpilih;
@endphp
<div class="flex items-center gap-3 mb-5">
    <div class="w-1 h-8 bg-blue-500 rounded-full"></div>
    <div>
        <h2 class="text-lg font-black text-slate-800 dark:text-slate-100">
            Penugasan — <span class="text-blue-600 dark:text-blue-400">{{ $labelPeriode }}</span>
        </h2>
        <p class="text-[11px] text-slate-400 font-medium mt-0.5">
            @if($tanggalMulai && $tanggalSelesai)
                Periode kustom yang dipilih
            @else
                {{ $bulanTerpilih == \Carbon\Carbon::now()->month && $tahunTerpilih == \Carbon\Carbon::now()->year ? 'Bulan berjalan' : 'Bulan terpilih' }}
            @endif
        </p>
    </div>
</div>

@if(auth()->user()->role === 'pegawai')
    <div class="glass dark:bg-slate-900/30 bg-white rounded-2xl overflow-hidden shadow-xl border border-slate-200/50 dark:border-white/5">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-white/5 flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center">
                <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-500"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-slate-700 dark:text-slate-200">Penugasan {{ $labelPeriode }}</h3>
                <p class="text-[10px] text-slate-400">{{ $daftarStStaff->count() }} surat tugas ditemukan</p>
            </div>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-white/5">
            @forelse($daftarStStaff as $st)
            <a href="{{ route('st.detail', $st->id_st) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-blue-50/50 dark:hover:bg-white/[0.02] transition-all group">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-indigo-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="briefcase" class="w-4 h-4 text-blue-500"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 transition-colors line-clamp-1">{{ $st->nama_penugasan }}</p>
                    <div class="flex items-center gap-3 mt-1 flex-wrap">
                        @if($st->no_surat_tugas)
                        <span class="text-[10px] font-mono font-bold text-slate-400">{{ $st->no_surat_tugas }}</span>
                        @endif
                        <span class="text-[10px] font-bold text-slate-400">
                            {{ \Carbon\Carbon::parse($st->start_date)->format('d M Y') }} — {{ \Carbon\Carbon::parse($st->end_date)->format('d M Y') }}
                        </span>
                        @if($st->peran)
                        <span class="text-[10px] font-bold bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 px-2 py-0.5 rounded-md">{{ $st->peran }}</span>
                        @endif
                    </div>
                </div>
                @php
                    $warna = match($st->status_st) {
                        'Konsep'          => 'bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400',
                        'Realisasi'       => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400',
                        'Perpanjangan ST' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400',
                        'Final'           => 'bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-400',
                        'Batal'           => 'bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400',
                        default           => 'bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-slate-400',
                    };
                @endphp
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg shrink-0 {{ $warna }}">{{ $st->status_st ?? 'Konsep' }}</span>
            </a>
            @empty
            <div class="py-16 text-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="inbox" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium text-sm">Tidak ada penugasan di periode ini</p>
            </div>
            @endforelse
        </div>
    </div>

@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($rekapPenugasanBulanan as $dataStatistik)
        <a href="{{ route('employee.detail', ['nip' => $dataStatistik->nip, 'start_date' => $tanggalMulai, 'end_date' => $tanggalSelesai, 'bidwas' => $idBidwas]) }}"
           class="glass dark:bg-slate-900/40 bg-white p-6 rounded-[2rem] border border-slate-200/50 dark:border-white/5 hover:border-blue-500/30 hover:shadow-2xl hover:shadow-blue-500/10 transition-all group relative overflow-hidden flex items-center justify-between">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/5 blur-3xl group-hover:bg-blue-500/20 transition-all duration-700"></div>
            <div class="flex items-center gap-5 relative z-10">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500/20 to-indigo-500/10 dark:from-blue-500/10 dark:to-indigo-500/5 rounded-[1.25rem] border border-blue-500/20 shadow-inner flex items-center justify-center text-2xl font-black text-blue-600 dark:text-blue-400 group-hover:rotate-6 transition-transform">
                    {{ substr($dataStatistik->nama, 0, 1) }}
                </div>
                <div>
                    <h4 class="text-base font-black text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-1 pr-4" title="{{ $dataStatistik->nama }}">
                        {{ $dataStatistik->nama }}
                    </h4>
                    <div class="flex items-center space-x-2 mt-1.5">
                        <span class="text-[9px] uppercase tracking-widest font-black bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 px-2.5 py-1 rounded-lg border border-blue-100 dark:border-blue-500/10">
                            {{ $dataStatistik->total }} Penugasan
                        </span>
                    </div>
                </div>
            </div>
            <div class="relative w-16 h-16 flex items-center justify-center relative z-10 shrink-0">
                <svg class="w-full h-full -rotate-90 transform" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-slate-100 dark:stroke-white/5" stroke-width="4"></circle>
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-blue-500" stroke-width="4"
                            stroke-dasharray="{{ min(($dataStatistik->total / 10) * 100, 100) }} 100"
                            stroke-linecap="round"
                            style="transition: stroke-dasharray 2s cubic-bezier(0.4, 0, 0.2, 1);"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-sm font-black text-slate-800 dark:text-slate-200">{{ $dataStatistik->total }}</span>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full py-20 text-center glass dark:bg-slate-900/20 rounded-[3rem] border border-dashed border-slate-300 dark:border-white/10">
            <div class="w-20 h-20 bg-slate-500/5 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="search-x" class="w-10 h-10 text-slate-400 opacity-30"></i>
            </div>
            <p class="text-slate-500 dark:text-slate-400 font-black uppercase tracking-[0.2em] text-sm">Data Tidak Ditemukan</p>
            <p class="text-slate-400 dark:text-slate-500 text-xs mt-2 font-medium">Gunakan filter lain untuk menemukan data yang Bapak cari.</p>
        </div>
        @endforelse
    </div>
@endif
@endsection

@include('components.custom-dropdown-css')

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
        from { opacity: 0; transform: translateY(-8px) scale(0.96); }
        to { opacity: 1; transform: translateY(0) scale(1); }
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
        display: flex !important; align-items: center !important; justify-content: space-between !important;
    }

    .flatpickr-prev-month, .flatpickr-next-month {
        position: static !important;
        display: flex !important; align-items: center !important; justify-content: center !important;
        width: 36px !important; height: 36px !important;
        border-radius: 50% !important;
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #94a3b8 !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .flatpickr-prev-month:hover, .flatpickr-next-month:hover {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #ffffff !important; transform: scale(1.08) !important;
    }

    .flatpickr-current-month {
        display: flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important;
        position: static !important; width: auto !important; padding: 0 !important;
        flex: 1 !important; text-align: center !important;
    }

    .flatpickr-monthDropdown-months {
        appearance: none !important; -webkit-appearance: none !important;
        background: transparent !important; border: none !important;
        color: #f1f5f9 !important; font-weight: 600 !important; font-size: 15px !important;
    }

    [data-theme="light"] .flatpickr-monthDropdown-months { color: #1e293b !important; }

    .numInputWrapper input.cur-year {
        background: transparent !important; color: #f1f5f9 !important;
        font-weight: 600 !important; font-size: 15px !important; width: 52px !important;
    }

    [data-theme="light"] .numInputWrapper input.cur-year { color: #1e293b !important; }
    .numInputWrapper span { display: none !important; }

    .flatpickr-day {
        color: #e2e8f0 !important; border-radius: 10px !important; height: 38px !important; line-height: 38px !important;
        font-weight: 500 !important; margin: 2px !important; transition: all 0.15s ease !important;
    }

    [data-theme="light"] .flatpickr-day { color: #334155 !important; }

    .flatpickr-day:hover { background: rgba(59, 130, 246, 0.1) !important; transform: scale(1.05) !important; }

    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #ffffff !important; font-weight: 700 !important; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4) !important;
        border-color: transparent !important;
    }

    .flatpickr-day.inRange {
        background: rgba(59, 130, 246, 0.2) !important;
        color: #60a5fa !important;
        border-color: transparent !important;
        box-shadow: none !important;
    }

    [data-theme="light"] .flatpickr-day.inRange {
        background: rgba(59, 130, 246, 0.15) !important;
        color: #2563eb !important;
        border-color: transparent !important;
        box-shadow: none !important;
    }

    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
        color: #94a3b8 !important;
        opacity: 0.1 !important;
        pointer-events: none !important;
    }

    .flatpickr-weekday {
        color: #94a3b8 !important;
        font-weight: 700 !important;
    }

    .dark .flatpickr-weekday {
        color: rgba(255, 255, 255, 0.9) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateRangePicker = flatpickr("#date_range_picker", {
            mode: "range",
            dateFormat: "Y-m-d",
            prevArrow: '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>',
            nextArrow: '<svg class="shrink-0 size-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>',
            locale: { rangeSeparator: " → " },
            defaultDate: [ "{{ $tanggalMulai }}" || null, "{{ $tanggalSelesai }}" || null ],
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length === 2) {
                    const startDate = selectedDates[0];
                    const endDate = selectedDates[1];

                    document.getElementById('start_date_hidden').value = startDate.toISOString().split('T')[0];
                    document.getElementById('end_date_hidden').value = endDate.toISOString().split('T')[0];

                    const formatDate = (date) => date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

                    document.getElementById('display_start_date').textContent = formatDate(startDate);
                    document.getElementById('display_start_date').classList.remove('text-slate-400');
                    document.getElementById('display_start_date').classList.add('text-slate-800', 'dark:text-white');

                    document.getElementById('display_end_date').textContent = formatDate(endDate);
                    document.getElementById('display_end_date').classList.remove('text-slate-400');
                    document.getElementById('display_end_date').classList.add('text-slate-800', 'dark:text-white');

                } else if (selectedDates.length === 0) {
                    document.getElementById('start_date_hidden').value = '';
                    document.getElementById('display_start_date').textContent = 'Mulai';
                    document.getElementById('display_start_date').classList.add('text-slate-400');
                    document.getElementById('display_start_date').classList.remove('text-slate-800', 'dark:text-white');

                    document.getElementById('end_date_hidden').value = '';
                    document.getElementById('display_end_date').textContent = 'Selesai';
                    document.getElementById('display_end_date').classList.add('text-slate-400');
                    document.getElementById('display_end_date').classList.remove('text-slate-800', 'dark:text-white');
                }
            }
        });

        lucide.createIcons();
    });

    function applyFilter(key, value) {
        document.getElementById(key + '_input').value = value;
        if (key === 'bidwas' || !document.getElementById('start_date_hidden').value) {
            document.getElementById('filterForm').submit();
        }
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown-container')) {
            document.querySelectorAll('.status-filter-dropdown input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    });
</script>
@endpush
