@extends('layouts.app')

@section('title', 'Monitoring Penugasan Bulanan')

@section('content')

    <div class="glass dark:bg-slate-900/50 bg-white rounded-2xl p-6 mb-8 border border-slate-200/50 dark:border-white/5 shadow-xl shadow-slate-200/20 dark:shadow-black/20 relative z-[40] overflow-visible">
        <form action="/dashboard/monthly" method="GET" id="filterForm" class="space-y-6">
            <input type="hidden" name="bidwas" id="bidwas_input" value="{{ $idBidwas }}">
            <input type="hidden" name="month" id="month_input" value="{{ $month }}">
            <input type="hidden" name="year" id="year_input" value="{{ $year }}">
            <input type="hidden" name="start_date" id="start_date_hidden" value="{{ $startDate }}">
            <input type="hidden" name="end_date" id="end_date_hidden" value="{{ $endDate }}">

<div class="flex flex-col lg:flex-row gap-4 items-end">

                <div class="flex-1 min-w-0 w-full">
                    <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-black mb-2 ml-1">Pencarian Pegawai</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search }}"
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
                                <span id="display_start_date" class="text-sm font-medium {{ $startDate ? 'text-slate-800 dark:text-white' : 'text-slate-400' }}">
                                    {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Mulai' }}
                                </span>
                            </div>

<div class="px-2 text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </div>

<div class="flex-1 flex items-center gap-3 pl-2 pr-3 h-8">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                <span id="display_end_date" class="text-sm font-medium {{ $endDate ? 'text-slate-800 dark:text-white' : 'text-slate-400' }}">
                                    {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'Selesai' }}
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
                    <div class="custom-dropdown-container w-full">
                        <div class="status-filter-dropdown dark:bg-white/5 bg-white h-12 border border-slate-200 dark:border-white/10 rounded-xl">
                            <input hidden="" class="sr-only" name="bidwas-dropdown" id="bidwas-dropdown" type="checkbox" />
                            <label for="bidwas-dropdown" class="status-filter-trigger h-full rounded-xl">
                                <span class="text-blue-600 dark:text-blue-400 font-bold text-sm truncate">{{ $selectedBidwas ? $selectedBidwas->short_name : 'Semua Bidang' }}</span>
                            </label>
                            <ul class="status-filter-list rounded-xl border border-white/10 shadow-2xl backdrop-blur-xl" role="list">
                                <li class="status-filter-listitem">
                                    <div onclick="applyFilter('bidwas', '')" class="status-filter-article {{ !$idBidwas ? 'active' : '' }}">Semua Bidang</div>
                                </li>
                                @foreach($bidwas as $b)
                                <li class="status-filter-listitem">
                                    <div onclick="applyFilter('bidwas', '{{ $b->id_bidwas }}')" class="status-filter-article {{ $idBidwas == $b->id_bidwas ? 'active' : '' }}">{{ $b->short_name }}</div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

<div class="flex items-center gap-2 w-full lg:w-auto">
                    <button type="submit" class="flex-1 lg:flex-none h-12 px-6 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-xl transition-all font-bold shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2 group">
                        <i data-lucide="filter" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm">Filter</span>
                    </button>
                    @if($search || $idBidwas || $startDate || $month != date('n') || $year != date('Y'))
                        <a href="/dashboard/monthly" class="h-12 w-12 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-500 rounded-xl transition-all flex items-center justify-center border border-red-200 dark:border-red-500/20 group" title="Reset Filter">
                            <i data-lucide="x" class="w-4 h-4 group-hover:rotate-90 transition-transform"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($stats as $stat)
        <a href="{{ route('employee.detail', ['nip' => $stat->nip, 'month' => $month, 'year' => $year, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="glass dark:bg-slate-900/40 bg-white p-6 rounded-[2rem] border border-slate-200/50 dark:border-white/5 hover:border-blue-500/30 hover:shadow-2xl hover:shadow-blue-500/10 transition-all group relative overflow-hidden flex items-center justify-between">

<div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/5 blur-3xl group-hover:bg-blue-500/20 transition-all duration-700"></div>

            <div class="flex items-center gap-5 relative z-10">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500/20 to-indigo-500/10 dark:from-blue-500/10 dark:to-indigo-500/5 rounded-[1.25rem] border border-blue-500/20 shadow-inner flex items-center justify-center text-2xl font-black text-blue-600 dark:text-blue-400 group-hover:rotate-6 transition-transform">
                    {{ substr($stat->nama, 0, 1) }}
                </div>
                <div>
                    <h4 class="text-base font-black text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-1 pr-4" title="{{ $stat->nama }}">
                        {{ $stat->nama }}
                    </h4>
                    <div class="flex items-center space-x-2 mt-1.5">
                        <span class="text-[9px] uppercase tracking-widest font-black bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 px-2.5 py-1 rounded-lg border border-blue-100 dark:border-blue-500/10">
                            {{ $stat->total }} Penugasan
                        </span>
                    </div>
                </div>
            </div>

            <div class="relative w-16 h-16 flex items-center justify-center relative z-10 shrink-0">
                <svg class="w-full h-full -rotate-90 transform" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-slate-100 dark:stroke-white/5" stroke-width="4"></circle>
                    <circle cx="18" cy="18" r="16" fill="none" class="stroke-blue-500" stroke-width="4"
                            stroke-dasharray="{{ min(($stat->total / 10) * 100, 100) }} 100"
                            stroke-linecap="round"
                            style="transition: stroke-dasharray 2s cubic-bezier(0.4, 0, 0.2, 1);"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-sm font-black text-slate-800 dark:text-slate-200">{{ $stat->total }}</span>
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
            defaultDate: [ "{{ $startDate }}" || null, "{{ $endDate }}" || null ],
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
