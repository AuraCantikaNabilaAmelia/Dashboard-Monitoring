@extends('layouts.app')

@section('title', 'Monitoring Penugasan Bulanan')

@section('content')
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl lg:text-3xl font-black text-slate-800 dark:text-white tracking-tight">Monitoring Penugasan Bulanan</h1>
            <p class="text-slate-500 text-[10px] md:text-sm font-medium">Statistik akumulasi beban kerja per pegawai</p>
        </div>
    </div>

    <div class="glass dark:bg-white/10 bg-white p-5 rounded-[1.5rem] border border-blue-500/10 shadow-xl shadow-blue-500/5 relative z-50">
        <form action="/dashboard/monthly" method="GET" id="filterForm" class="space-y-4">
            <div class="glass dark:bg-white/5 bg-slate-50 px-5 py-2.5 rounded-xl flex items-center space-x-3 border border-slate-200 dark:border-white/10 focus-within:border-blue-500/50 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all h-[48px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari Nama Pegawai atau NIP..." class="bg-transparent border-none outline-none text-xs w-full text-slate-900 dark:text-white font-bold placeholder:text-slate-400 placeholder:font-medium" autocomplete="off">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-x-4 gap-y-6 items-end">
                <div class="lg:col-span-3">
                    <div class="custom-dropdown-container w-full">
                        <div class="status-filter-dropdown dark:bg-white/5 bg-slate-50 h-[48px] border border-slate-200 dark:border-white/10 rounded-xl">
                            <input hidden="" class="sr-only" name="bidwas-dropdown" id="bidwas-dropdown" type="checkbox" />
                            <label for="bidwas-dropdown" class="status-filter-trigger h-full rounded-xl">
                                <span class="text-[9px] uppercase tracking-widest text-slate-500 mr-2 font-black">Bidang:</span>
                                <span class="text-blue-600 dark:text-blue-400 font-black text-xs truncate">
                                    {{ $selectedBidwas ? $selectedBidwas->short_name : 'Semua' }}
                                </span>
                            </label>
                            <ul class="status-filter-list webkit-scrollbar">
                                <li class="status-filter-listitem">
                                    <div onclick="applyFilter('bidwas', '')" class="status-filter-article {{ !$idBidwas ? 'active' : '' }}">Semua Bidang</div>
                                </li>
                                @foreach($bidwas as $b)
                                <li class="status-filter-listitem">
                                    <div onclick="applyFilter('bidwas', '{{ $b->id_bidwas }}')" class="status-filter-article {{ $idBidwas == $b->id_bidwas ? 'active' : '' }}">
                                        {{ $b->short_name }}
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" name="bidwas" id="bidwas_input" value="{{ $idBidwas }}">
                </div>

                <div class="lg:col-span-4 grid grid-cols-2 gap-3 {{ $startDate ? 'opacity-30 grayscale pointer-events-none' : '' }}">
                    <div class="custom-dropdown-container w-full">
                        <div class="status-filter-dropdown dark:bg-white/5 bg-slate-50 h-[48px] border border-slate-200 dark:border-white/10 rounded-xl">
                            <input hidden="" class="sr-only" name="month-dropdown" id="month-dropdown" type="checkbox" />
                            <label for="month-dropdown" class="status-filter-trigger h-full rounded-xl">
                                <span class="text-[9px] uppercase tracking-widest text-slate-500 mr-2 font-black">Bulan:</span>
                                <span class="text-slate-800 dark:text-slate-200 font-black text-xs">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</span>
                            </label>
                            <ul class="status-filter-list webkit-scrollbar">
                                @foreach(range(1, 12) as $m)
                                <li class="status-filter-listitem">
                                    <div onclick="applyFilter('month', '{{ $m }}')" class="status-filter-article {{ $month == $m ? 'active' : '' }}">
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="custom-dropdown-container w-full">
                        <div class="status-filter-dropdown dark:bg-white/5 bg-slate-50 h-[48px] border border-slate-200 dark:border-white/10 rounded-xl">
                            <input hidden="" class="sr-only" name="year-dropdown" id="year-dropdown" type="checkbox" />
                            <label for="year-dropdown" class="status-filter-trigger h-full rounded-xl">
                                <span class="text-[9px] uppercase tracking-widest text-slate-500 mr-2 font-black">Tahun:</span>
                                <span class="text-slate-800 dark:text-slate-200 font-black text-xs">{{ $year }}</span>
                            </label>
                            <ul class="status-filter-list webkit-scrollbar">
                                @foreach($years as $y)
                                <li class="status-filter-listitem">
                                    <div onclick="applyFilter('year', '{{ $y }}')" class="status-filter-article {{ $year == $y ? 'active' : '' }}">
                                        {{ $y }}
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" name="month" id="month_input" value="{{ $month }}">
                    <input type="hidden" name="year" id="year_input" value="{{ $year }}">
                </div>

                <div class="lg:col-span-3">
                    <div class="flex items-center space-x-1.5 h-[48px]">
                        <div class="glass dark:bg-white/5 bg-slate-50 rounded-xl px-2 flex items-center border border-slate-200 dark:border-white/10 focus-within:border-blue-500/50 transition-all h-full flex-1">
                            <span class="text-[8px] uppercase font-black text-slate-400 ml-1 mr-2 flex-shrink-0">DARI</span>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="bg-transparent border-none outline-none text-[10px] text-slate-900 dark:text-white font-bold w-full" onchange="toggleCustomMode()">
                        </div>
                        <div class="glass dark:bg-white/5 bg-slate-50 rounded-xl px-2 flex items-center border border-slate-200 dark:border-white/10 focus-within:border-blue-500/50 transition-all h-full flex-1">
                            <span class="text-[8px] uppercase font-black text-slate-400 ml-1 mr-2 flex-shrink-0">SD</span>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="bg-transparent border-none outline-none text-[10px] text-slate-900 dark:text-white font-bold w-full" onchange="toggleCustomMode()">
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 flex items-center gap-2">
                    <button type="submit" class="flex-1 bg-slate-900 dark:bg-blue-600 hover:bg-blue-600 dark:hover:bg-blue-700 text-white h-[48px] rounded-xl transition-all font-black shadow-lg shadow-blue-500/20 flex items-center justify-center space-x-2 group">
                        <i data-lucide="filter" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i>
                        <span class="text-[10px] uppercase tracking-widest">Filter</span>
                    </button>
                    @if($startDate || $idBidwas || $month != date('n') || $year != date('Y') || $search)
                        <a href="/dashboard/monthly" class="w-[48px] h-[48px] bg-red-500/10 hover:bg-red-500/20 text-red-500 rounded-xl transition-all font-black border border-red-500/20 flex items-center justify-center group" title="Reset Filter">
                            <i data-lucide="x" class="w-4 h-4 group-hover:rotate-90 transition-transform"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
    @forelse($stats as $stat)
    <a href="{{ route('employee.detail', ['nip' => $stat->nip, 'month' => $month, 'year' => $year, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="glass dark:bg-white/10 bg-white p-8 rounded-[2rem] flex items-center justify-between group hover:bg-white/5 hover:scale-[1.02] transition-all border border-blue-500/5 hover:border-blue-500/30 relative overflow-hidden group shadow-sm">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 blur-3xl group-hover:bg-blue-500/20 transition-all duration-500"></div>
        
        <div class="flex items-center space-x-6 relative z-10">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500/20 to-indigo-500/10 rounded-2xl flex items-center justify-center font-black text-2xl text-blue-500 border border-blue-500/20 shadow-inner group-hover:rotate-3 transition-transform">
                {{ substr($stat->nama, 0, 1) }}
            </div>
            <div>
                <h4 class="text-lg font-black text-slate-800 dark:text-white group-hover:text-blue-500 transition-colors line-clamp-1 max-w-[200px]" title="{{ $stat->nama }}">{{ $stat->nama }}</h4>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="text-[10px] text-slate-500 uppercase tracking-widest font-black bg-slate-100 dark:bg-white/5 px-2 py-0.5 rounded">{{ $stat->total }} Penugasan</span>
                </div>
            </div>
        </div>
        
        <div class="relative w-16 h-16 relative z-10 flex items-center justify-center">
            <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="16" fill="none" class="stroke-slate-100 dark:stroke-white/5" stroke-width="4"></circle>
                <circle cx="18" cy="18" r="16" fill="none" class="stroke-blue-500" stroke-width="4" 
                        stroke-dasharray="{{ min(($stat->total / 10) * 100, 100) }} 100" 
                        stroke-linecap="round"
                        style="transition: stroke-dasharray 1.5s cubic-bezier(0.4, 0, 0.2, 1);"></circle>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-xs font-black text-slate-800 dark:text-white leading-none">{{ $stat->total }}</span>
            </div>
        </div>
    </a>
    @empty
    <div class="col-span-full py-24 text-center glass rounded-[3rem] border border-dashed border-slate-300 dark:border-white/10">
        <div class="w-20 h-20 bg-slate-500/5 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="search-x" class="w-10 h-10 text-slate-500 opacity-20"></i>
        </div>
        <p class="text-slate-500 font-black uppercase tracking-[0.2em] text-sm">Data Tidak Ditemukan</p>
        <p class="text-slate-400 text-xs mt-2 font-medium">Coba gunakan kata kunci lain atau sesuaikan filter Bapak.</p>
    </div>
    @endforelse
</div>

@include('components.custom-dropdown-css')

@push('scripts')
<script>
    function applyFilter(id, value) {
        document.getElementById(id + '_input').value = value;
        if ((id === 'month' || id === 'year') && !document.getElementById('start_date').value) {
            document.getElementById('filterForm').submit();
        } else if (id === 'bidwas') {
            document.getElementById('filterForm').submit();
        }
    }

    function toggleCustomMode() {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;
        const monthCont = document.getElementById('month_container');
        const yearCont = document.getElementById('year_container');
        
        if (start || end) {
            monthCont.classList.add('opacity-40', 'grayscale', 'pointer-events-none');
            yearCont.classList.add('opacity-40', 'grayscale', 'pointer-events-none');
        } else {
            monthCont.classList.remove('opacity-40', 'grayscale', 'pointer-events-none');
            yearCont.classList.remove('opacity-40', 'grayscale', 'pointer-events-none');
        }
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
@endsection
