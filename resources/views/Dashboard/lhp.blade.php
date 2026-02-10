@extends('layouts.app')

@section('title', 'Daftar LHP')

@section('content')

    <div class="glass dark:bg-slate-900/50 bg-white rounded-2xl p-6 mb-8 border border-slate-200/50 dark:border-white/5 shadow-xl shadow-slate-200/20 dark:shadow-black/20 relative z-[40] overflow-visible">
        <form action="/dashboard/lhp" method="GET" id="filterForm">
            <input type="hidden" name="status" id="status_input" value="{{ $statusTerpilih }}">
            <input type="hidden" name="start_date" id="start_date_hidden" value="{{ $tanggalMulai }}">
            <input type="hidden" name="end_date" id="end_date_hidden" value="{{ $tanggalSelesai }}">

            <div class="flex flex-col lg:flex-row gap-4 items-end">

                <div class="flex-1 min-w-0">
                    <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-black mb-2 ml-1">Pencarian</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input type="text" name="search" value="{{ $kataKunci }}"
                               placeholder="Cari nomor LHP atau nama penugasan..."
                               class="w-full h-12 pl-11 pr-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium"
                               autocomplete="off">
                    </div>
                </div>

                <div class="w-full lg:w-80">
                    <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-black mb-2 ml-1">Periode LHP</label>
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

                <div class="w-full lg:w-52 relative z-[45]">
                    <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-black mb-2 ml-1">Status</label>
                    <div class="custom-dropdown-container w-full">
                        <div class="status-filter-dropdown dark:bg-white/5 bg-white h-12 border border-slate-200 dark:border-white/10 rounded-xl">
                            <input hidden="" class="sr-only" name="state-dropdown" id="state-dropdown" type="checkbox" />
                            <label aria-label="dropdown scrollbar" for="state-dropdown" class="status-filter-trigger h-full rounded-xl">
                                <span class="text-blue-600 dark:text-blue-400 font-bold text-sm truncate">{{ $statusTerpilih ?: 'Semua Status' }}</span>
                            </label>

                            <ul class="status-filter-list rounded-xl border border-white/10 shadow-2xl backdrop-blur-xl" role="list">
                                <li class="status-filter-listitem">
                                    <div onclick="applyStatus('')" class="status-filter-article {{ !$statusTerpilih ? 'active' : '' }}">Semua Status</div>
                                </li>
                                @foreach($daftarStatus as $s)
                                <li class="status-filter-listitem">
                                    <div onclick="applyStatus('{{ $s }}')" class="status-filter-article {{ $statusTerpilih == $s ? 'active' : '' }}">{{ $s }}</div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex items-end gap-2 flex-wrap">
                    <button type="submit" class="h-12 px-6 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white rounded-xl transition-all font-bold shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2 group">
                        <i data-lucide="filter" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm">Filter</span>
                    </button>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('export.lhp.excel', request()->query()) }}" class="h-12 px-5 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl transition-all flex items-center justify-center border border-emerald-200 dark:border-emerald-500/20 group gap-2" title="Export Excel">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                            <span class="text-xs font-bold">Excel</span>
                        </a>
                        <a href="{{ route('export.lhp.pdf', request()->query()) }}" class="h-12 px-5 bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl transition-all flex items-center justify-center border border-rose-200 dark:border-rose-500/20 group gap-2" title="Export PDF">
                            <i data-lucide="file-text" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                            <span class="text-xs font-bold">PDF</span>
                        </a>
                    </div>

                    @if($kataKunci || $statusTerpilih || $tanggalMulai || $tanggalSelesai)
                        <a href="/dashboard/lhp" class="h-12 w-12 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-500 rounded-xl transition-all flex items-center justify-center border border-red-200 dark:border-red-500/20 group" title="Reset Filter">
                            <i data-lucide="x" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="flex flex-wrap gap-3 mb-6">
        <div class="glass dark:bg-blue-500/10 bg-blue-50 border border-blue-200/50 dark:border-blue-500/20 rounded-xl px-4 py-2.5 flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                <i data-lucide="clipboard-check" class="w-4 h-4 text-blue-500"></i>
            </div>
            <div>
                <div class="text-[10px] uppercase tracking-widest text-blue-500 font-black">Total LHP</div>
                <div class="text-lg font-black text-blue-600 dark:text-blue-400">{{ $daftarLhp->total() }}</div>
            </div>
        </div>
        @if($kataKunci || $statusTerpilih || $tanggalMulai || $tanggalSelesai)
        <div class="glass dark:bg-amber-500/10 bg-amber-50 border border-amber-200/50 dark:border-amber-500/20 rounded-xl px-4 py-2.5 flex items-center gap-3">
            <div class="w-8 h-8 bg-amber-500/20 rounded-lg flex items-center justify-center">
                <i data-lucide="filter" class="w-4 h-4 text-amber-500"></i>
            </div>
            <div>
                <div class="text-[10px] uppercase tracking-widest text-amber-500 font-black">Filter Aktif</div>
                <div class="text-sm font-bold text-amber-600 dark:text-amber-400">
                    {{ collect([$kataKunci ? 'Pencarian' : null, $statusTerpilih ?: null, ($tanggalMulai && $tanggalSelesai) ? 'Periode' : null])->filter()->implode(', ') }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="glass dark:bg-slate-900/30 bg-white rounded-2xl overflow-hidden shadow-xl shadow-slate-200/20 dark:shadow-black/20 border border-slate-200/50 dark:border-white/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-white/5 dark:to-white/[0.02]">
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-400">
                            <div class="flex items-center gap-2">
                                <i data-lucide="hash" class="w-3 h-3"></i>
                                No. LHP
                            </div>
                        </th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-400">
                            <div class="flex items-center gap-2">
                                <i data-lucide="briefcase" class="w-3 h-3"></i>
                                Nama Penugasan
                            </div>
                        </th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-400">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                Tanggal LHP
                            </div>
                        </th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 dark:text-slate-400 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($daftarLhp as $indeks => $dataLhp)
                    <tr class="hover:bg-blue-50/50 dark:hover:bg-white/[0.02] transition-all group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                    {{ $daftarLhp->firstItem() + $indeks }}
                                </div>
                                <span class="text-sm font-bold text-blue-600 dark:text-blue-400 font-mono tracking-tight">{{ $dataLhp->nomor_lhp ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="max-w-md">
                                <a href="{{ route('st.detail', $dataLhp->st_id) }}" class="group/link">
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 line-clamp-1 group-hover/link:text-blue-600 transition-colors" title="{{ $dataLhp->nama_penugasan }}">
                                        {{ $dataLhp->nama_penugasan }}
                                    </p>
                                    <span class="text-[10px] font-mono font-bold text-slate-400 group-hover/link:text-blue-400/70 transition-colors">{{ $dataLhp->no_surat_tugas ?? '-' }}</span>
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="inline-flex items-center gap-2 bg-slate-100 dark:bg-white/5 px-3 py-1.5 rounded-lg">
                                <i data-lucide="calendar-check" class="w-3 h-3 text-slate-400"></i>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($dataLhp->tanggal_lhp)->format('d M Y') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400">
                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                {{ $dataLhp->status_lhp ?: 'Terbit' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-white/5 rounded-2xl flex items-center justify-center mb-4">
                                    <i data-lucide="inbox" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                                </div>
                                <p class="text-slate-500 dark:text-slate-400 font-medium">Tidak ada data LHP ditemukan</p>
                                <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">Coba ubah filter atau kata kunci pencarian</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($daftarLhp->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.02]">
            <div class="flex items-center justify-between">
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    Menampilkan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftarLhp->firstItem() }}</span> - <span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftarLhp->lastItem() }}</span> dari <span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftarLhp->total() }}</span> data
                </div>
                <div class="flex gap-1">
                    @if($daftarLhp->onFirstPage())
                        <span class="px-4 py-2 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-400 cursor-not-allowed text-sm font-medium">Prev</span>
                    @else
                        <a href="{{ $daftarLhp->appends(request()->query())->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-white/10 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:border-blue-300 dark:hover:border-blue-500/30 hover:text-blue-600 transition-all text-sm font-medium">Prev</a>
                    @endif

                    @foreach ($daftarLhp->appends(request()->query())->getUrlRange(max(1, $daftarLhp->currentPage() - 2), min($daftarLhp->lastPage(), $daftarLhp->currentPage() + 2)) as $page => $url)
                        @if ($page == $daftarLhp->currentPage())
                            <span class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-bold shadow-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 rounded-lg bg-white dark:bg-white/10 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:border-blue-300 dark:hover:border-blue-500/30 hover:text-blue-600 transition-all text-sm font-medium">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($daftarLhp->hasMorePages())
                        <a href="{{ $daftarLhp->appends(request()->query())->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-white/10 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:border-blue-300 dark:hover:border-blue-500/30 hover:text-blue-600 transition-all text-sm font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-400 cursor-not-allowed text-sm font-medium">Next</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
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

    function applyStatus(status) {
        document.getElementById('status_input').value = status;
        document.getElementById('filterForm').submit();
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown-container')) {
            document.querySelectorAll('.status-filter-dropdown input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    });

    document.querySelectorAll('.status-filter-dropdown input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                document.querySelectorAll('.status-filter-dropdown input[type="checkbox"]').forEach(others => {
                    if (others !== this) others.checked = false;
                });
            }
        });
    });
</script>
@endpush

