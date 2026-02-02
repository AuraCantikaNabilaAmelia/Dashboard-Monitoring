@extends('layouts.app')

@section('title', 'Daftar Surat Tugas')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 mb-8 items-end">
        <div class="lg:col-span-4">
            <div class="glass dark:bg-white/5 bg-white px-5 py-2.5 rounded-xl flex items-center space-x-3 border border-slate-200 dark:border-white/10 focus-within:border-blue-500/50 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all h-[48px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                <form action="/dashboard/st" method="GET" class="flex-1" id="filterForm">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari No ST atau Nama Penugasan..." class="bg-transparent border-none outline-none text-xs w-full text-slate-900 dark:text-white font-bold" autocomplete="off">
                    <input type="hidden" name="status" id="status_input" value="{{ $status }}">
                    <input type="hidden" name="start_date" id="start_date_hidden" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" id="end_date_hidden" value="{{ $endDate }}">
                </form>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="flex items-center space-x-1.5 h-[48px]">
                <div class="glass dark:bg-white/5 bg-white rounded-xl px-2 flex items-center border border-slate-200 dark:border-white/10 focus-within:border-blue-500/50 transition-all h-full flex-1">
                    <span class="text-[8px] uppercase font-black text-slate-400 ml-1 mr-2 flex-shrink-0">DARI</span>
                    <input type="date" id="start_date" value="{{ $startDate }}" class="bg-transparent border-none outline-none text-[10px] text-slate-900 dark:text-white font-bold w-full" onchange="syncDateAndSubmit()">
                </div>
                <div class="glass dark:bg-white/5 bg-white rounded-xl px-2 flex items-center border border-slate-200 dark:border-white/10 focus-within:border-blue-500/50 transition-all h-full flex-1">
                    <span class="text-[8px] uppercase font-black text-slate-400 ml-1 mr-2 flex-shrink-0">SD</span>
                    <input type="date" id="end_date" value="{{ $endDate }}" class="bg-transparent border-none outline-none text-[10px] text-slate-900 dark:text-white font-bold w-full" onchange="syncDateAndSubmit()">
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="custom-dropdown-container w-full">
                <div class="status-filter-dropdown dark:bg-white/5 bg-white h-[48px] border border-slate-200 dark:border-white/10 rounded-xl">
                    <input hidden="" class="sr-only" name="state-dropdown" id="state-dropdown" type="checkbox" />
                    <label aria-label="dropdown scrollbar" for="state-dropdown" class="status-filter-trigger h-full rounded-xl">
                        <span class="text-[9px] uppercase tracking-widest text-slate-500 mr-2 font-black">Status:</span>
                        <span class="text-blue-600 dark:text-blue-400 font-black text-xs truncate">{{ $status ?: 'Semua' }}</span>
                    </label>

                    <ul class="status-filter-list" role="list">
                        <li class="status-filter-listitem">
                            <div onclick="applyStatus('')" class="status-filter-article {{ !$status ? 'active' : '' }}">Semua Status</div>
                        </li>
                        @foreach($statuses as $s)
                        <li class="status-filter-listitem">
                            <div onclick="applyStatus('{{ $s }}')" class="status-filter-article {{ $status == $s ? 'active' : '' }}">{{ $s }}</div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 flex items-center gap-2">
            <button type="submit" form="filterForm" class="flex-1 bg-slate-900 dark:bg-blue-600 hover:bg-blue-600 dark:hover:bg-blue-700 text-white h-[48px] rounded-xl transition-all font-black shadow-lg shadow-blue-500/20 flex items-center justify-center group">
                <i data-lucide="filter" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i>
            </button>
            @if($search || $status || $startDate || $endDate)
                <a href="/dashboard/st" class="w-[48px] h-[48px] bg-red-500/10 hover:bg-red-500/20 text-red-500 rounded-xl transition-all font-black border border-red-500/20 flex items-center justify-center group" title="Reset Filter">
                    <i data-lucide="x" class="w-4 h-4 group-hover:rotate-90 transition-transform"></i>
                </a>
            @endif
        </div>
    </div>

<div class="glass rounded-3xl overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-200 dark:border-white/10 bg-slate-100/50 dark:bg-white/5">
                    <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-600 dark:text-slate-300">No. Surat Tugas</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-600 dark:text-slate-300">Nama Penugasan</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-600 dark:text-slate-300">Periode</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-600 dark:text-slate-300 text-center">Status</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-widest font-black text-slate-600 dark:text-slate-300 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse($stList as $st)
                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-all group">
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-blue-700 dark:text-blue-400 font-mono">{{ $st->no_surat_tugas ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-black text-slate-800 dark:text-slate-200 line-clamp-1 max-w-xs group-hover:text-blue-600 transition-colors" title="{{ $st->nama_penugasan }}">
                            {{ $st->nama_penugasan }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-[10px] font-black text-slate-500 uppercase tracking-tight whitespace-nowrap bg-slate-50 dark:bg-white/2 px-3 py-1.5 rounded-lg w-fit">
                            {{ \Carbon\Carbon::parse($st->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($st->end_date)->format('d M Y') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusClass = match($st->status_st) {
                                'Realisasi' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                'Perpanjangan ST' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                'Batal' => 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20',
                                'Konsep', 'Final', 'Selesai' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                default => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20'
                            };
                        @endphp
                        <span class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest border {{ $statusClass }} shadow-sm">
                            {{ $st->status_st }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('st.detail', $st->id_st) }}" class="p-2.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl hover:bg-blue-600 hover:text-white transition-all inline-flex items-center shadow-sm border border-blue-500/10">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 italic">
                        Tidak ada data surat tugas ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($stList->hasPages())
    <div class="px-6 py-4 border-t border-white/10 bg-white/5">
        {{ $stList->appends(['search' => $search])->links() }}
    </div>
    @endif
</div>
@endsection

@include('components.custom-dropdown-css')

@push('scripts')
<style>
    .pagination { @apply flex space-x-2; }
    .page-item { @apply rounded-lg overflow-hidden; }
    .page-link { @apply block px-4 py-2 text-sm glass border-none hover:bg-blue-500 transition-all; }
    .page-item.active .page-link { @apply bg-blue-600 text-white; }
    .page-item.disabled .page-link { @apply opacity-50 cursor-not-allowed; }
</style>

<script>
    function applyStatus(status) {
        document.getElementById('status_input').value = status;
        syncDateAndSubmit();
    }

    function syncDateAndSubmit() {
        document.getElementById('start_date_hidden').value = document.getElementById('start_date').value;
        document.getElementById('end_date_hidden').value = document.getElementById('end_date').value;
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
