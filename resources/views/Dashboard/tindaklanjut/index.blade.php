@extends('layouts.app')

@section('title', 'Tindak Lanjut Surat Masuk')

@section('content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-xl md:text-2xl font-bold">Monitoring Tindak Lanjut</h2>
        <p class="text-slate-500 text-[10px] md:text-sm">Tracking surat masuk hingga selesai</p>
    </div>
    <a href="{{ route('tindaklanjut.create') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Tambah Surat Masuk
    </a>
</div>

<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    @php
        $labels = \App\Models\SuratMasuk::statusLabels();
        $colors = \App\Models\SuratMasuk::statusColors();
    @endphp
    <div class="glass p-4 rounded-2xl text-center border-l-4 border-slate-400">
        <p class="text-2xl font-black text-slate-500">{{ $statusSummary['masuk'] ?? 0 }}</p>
        <p class="text-xs font-bold text-slate-500 uppercase">Baru/Masuk</p>
    </div>
    <div class="glass p-4 rounded-2xl text-center border-l-4 border-amber-500">
        <p class="text-2xl font-black text-amber-500">{{ $statusSummary['disposisi'] ?? 0 }}</p>
        <p class="text-xs font-bold text-slate-500 uppercase">Disposisi</p>
    </div>
    <div class="glass p-4 rounded-2xl text-center border-l-4 border-blue-500">
        <p class="text-2xl font-black text-blue-500">{{ $statusSummary['proses'] ?? 0 }}</p>
        <p class="text-xs font-bold text-slate-500 uppercase">Proses</p>
    </div>
    <div class="glass p-4 rounded-2xl text-center border-l-4 border-green-500">
        <p class="text-2xl font-black text-green-500">{{ $statusSummary['selesai'] ?? 0 }}</p>
        <p class="text-xs font-bold text-slate-500 uppercase">Selesai</p>
    </div>
    <div class="glass p-4 rounded-2xl text-center border-l-4 border-slate-800">
        <p class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ array_sum($statusSummary->toArray()) }}</p>
        <p class="text-xs font-bold text-slate-500 uppercase">Total</p>
    </div>
</div>

<div class="glass p-5 rounded-[1.5rem] border border-blue-500/10 shadow-xl shadow-blue-500/5 relative z-50 mb-8">
    <form method="GET" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            <div class="lg:col-span-3">
                <div class="glass dark:bg-white/5 bg-slate-50 px-5 py-2.5 rounded-xl flex items-center space-x-3 border border-slate-200 dark:border-white/10 focus-within:border-blue-500/50 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all h-[48px]">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor surat, perihal, atau pengirim..." class="bg-transparent border-none outline-none text-xs w-full text-slate-900 dark:text-white font-bold placeholder:text-slate-400 placeholder:font-medium" autocomplete="off">
                </div>
            </div>

            {{-- Date Range --}}
            <div class="lg:col-span-3">
                <div class="flex items-center space-x-1.5 h-[48px]">
                    <div class="glass dark:bg-white/5 bg-white rounded-xl px-2 flex items-center border border-slate-200 dark:border-white/10 focus-within:border-blue-500/50 transition-all h-full flex-1">
                        <span class="text-[8px] uppercase font-black text-slate-400 ml-1 mr-2 flex-shrink-0">DARI</span>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="bg-transparent border-none outline-none text-[10px] text-slate-900 dark:text-white font-bold w-full" onchange="this.form.submit()">
                    </div>
                    <div class="glass dark:bg-white/5 bg-white rounded-xl px-2 flex items-center border border-slate-200 dark:border-white/10 focus-within:border-blue-500/50 transition-all h-full flex-1">
                        <span class="text-[8px] uppercase font-black text-slate-400 ml-1 mr-2 flex-shrink-0">SD</span>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="bg-transparent border-none outline-none text-[10px] text-slate-900 dark:text-white font-bold w-full" onchange="this.form.submit()">
                    </div>
                </div>
            </div>

            {{-- Status Dropdown --}}
            <div class="lg:col-span-2">
                <div class="custom-dropdown-container w-full">
                    <div class="status-filter-dropdown dark:bg-white/5 bg-slate-50 h-[48px] border border-slate-200 dark:border-white/10 rounded-xl">
                        <input hidden="" class="sr-only" name="status-dropdown" id="status-dropdown" type="checkbox" />
                        <label for="status-dropdown" class="status-filter-trigger h-full rounded-xl w-full">
                            <span class="text-[9px] uppercase tracking-widest text-slate-500 mr-2 font-black">Status:</span>
                            <span class="text-blue-600 dark:text-blue-400 font-black text-xs truncate">{{ $status ? $labels[$status] : 'Semua' }}</span>
                        </label>
                        <ul class="status-filter-list">
                            <li class="status-filter-listitem">
                                <div onclick="applyFilter('status', '')" class="status-filter-article {{ !$status ? 'active' : '' }}">Semua Status</div>
                            </li>
                            @foreach($statuses as $s)
                            <li class="status-filter-listitem">
                                <div onclick="applyFilter('status', '{{ $s }}')" class="status-filter-article {{ $status == $s ? 'active' : '' }}">{{ $labels[$s] }}</div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <input type="hidden" name="status" id="status_input" value="{{ $status }}">
            </div>

            {{-- Bidang Dropdown --}}
            <div class="lg:col-span-3">
                <div class="custom-dropdown-container w-full">
                    <div class="status-filter-dropdown dark:bg-white/5 bg-slate-50 h-[48px] border border-slate-200 dark:border-white/10 rounded-xl">
                        <input hidden="" class="sr-only" name="bidang-dropdown" id="bidang-dropdown" type="checkbox" />
                        <label for="bidang-dropdown" class="status-filter-trigger h-full rounded-xl w-full">
                            <span class="text-[9px] uppercase tracking-widest text-slate-500 mr-2 font-black">Bidang:</span>
                            <span class="text-blue-600 dark:text-blue-400 font-black text-xs truncate">
                                {{ $bidang_id ? ($bidwasList->where('id_bidwas', $bidang_id)->first()->nm_bidwas ?? 'Semua') : 'Semua' }}
                            </span>
                        </label>
                        <ul class="status-filter-list webkit-scrollbar">
                            <li class="status-filter-listitem">
                                <div onclick="applyFilter('bidang_id', '')" class="status-filter-article {{ !$bidang_id ? 'active' : '' }}">Semua Bidang</div>
                            </li>
                            @foreach($bidwasList as $b)
                            <li class="status-filter-listitem">
                                <div onclick="applyFilter('bidang_id', '{{ $b->id_bidwas }}')" class="status-filter-article {{ $bidang_id == $b->id_bidwas ? 'active' : '' }}">
                                    @php
                                        $sName = $b->nm_bidwas;
                                        if (str_contains($sName, 'Instansi Pemerintah Pusat')) $sName = 'IPP';
                                        elseif (str_contains($sName, 'Pemerintah Daerah')) $sName = 'APD';
                                        elseif (str_contains($sName, 'Akuntan Negara')) $sName = 'AN';
                                        elseif (str_contains($sName, 'Investigasi')) $sName = 'Investigasi';
                                        elseif (str_contains($sName, 'Program dan Pelaporan')) $sName = 'P3A';
                                        elseif (str_contains($sName, 'Tata Usaha')) $sName = 'TU';
                                        elseif (str_contains($sName, 'Kepegawaian')) $sName = 'KEP';
                                        elseif (str_contains($sName, 'Keuangan')) $sName = 'KEU';
                                        elseif (str_contains($sName, 'Umum')) $sName = 'UMM';
                                    @endphp
                                    {{ $sName }}
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <input type="hidden" name="bidang_id" id="bidang_id_input" value="{{ $bidang_id }}">
            </div>

            <div class="lg:col-span-1 flex items-center gap-2">
                <button type="submit" class="flex-1 bg-slate-900 dark:bg-blue-600 hover:bg-blue-600 dark:hover:bg-blue-700 text-white h-[48px] rounded-xl transition-all font-black shadow-lg shadow-blue-500/20 flex items-center justify-center group">
                    <i data-lucide="filter" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i>
                </button>
                @if($search || $status || $bidang_id || $startDate || $endDate)
                <a href="{{ route('tindaklanjut.index') }}" class="w-[48px] h-[48px] bg-red-500/10 hover:bg-red-500/20 text-red-500 rounded-xl transition-all font-black border border-red-500/20 flex items-center justify-center group" title="Reset Filter">
                    <i data-lucide="x" class="w-4 h-4 group-hover:rotate-90 transition-transform"></i>
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="glass rounded-3xl overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-slate-200 dark:border-white/10 bg-slate-100/50 dark:bg-white/5">
                <th class="px-6 py-4 text-left text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">Nomor Surat</th>
                <th class="px-6 py-4 text-left text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">Tanggal</th>
                <th class="px-6 py-4 text-left text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">Perihal</th>
                <th class="px-6 py-4 text-left text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">Tujuan / Bidang</th>
                <th class="px-6 py-4 text-center text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">TL</th>
                <th class="px-6 py-4 text-left text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">Status</th>
                <th class="px-6 py-4 text-center text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
            @forelse($suratList as $surat)
            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                <td class="px-6 py-4 font-bold text-sm">{{ $surat->nomor_surat }}</td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ $surat->tanggal_surat->format('d M Y') }}</td>
                <td class="px-6 py-4 text-sm max-w-xs truncate" title="{{ $surat->perihal }}">{{ $surat->perihal }}</td>
                <td class="px-6 py-4 text-sm">
                    <div class="font-bold text-slate-600 dark:text-slate-300">
                        {{ $surat->target_bidang_id ? ($surat->targetBidang->nm_bidwas ?? 'Bidang Terhapus') : 'Belum Ditentukan' }}
                    </div>
                    <div class="text-xs text-slate-400 mt-1">
                        Dari: <span class="italic">{{ $surat->pengirim }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 text-xs font-bold rounded-full">
                        {{ $surat->tindak_lanjut_entries_count }} entri
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-{{ $surat->status_color }}-100 dark:bg-{{ $surat->status_color }}-500/20 text-{{ $surat->status_color }}-600 dark:text-{{ $surat->status_color }}-400 text-xs font-bold rounded-full">
                        {{ $surat->status_label }}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <a href="{{ route('tindaklanjut.show', $surat->id) }}" class="text-blue-500 hover:text-blue-600 font-bold text-sm">
                        Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                    <div class="flex flex-col items-center">
                        <i data-lucide="inbox" class="w-12 h-12 text-slate-300 mb-3"></i>
                        <p class="font-medium">Belum ada surat masuk.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $suratList->withQueryString()->links() }}
</div>
@endsection

@include('components.custom-dropdown-css')

@push('scripts')
<script>
    function applyFilter(id, value) {
        document.getElementById(id + '_input').value = value;
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

    lucide.createIcons();
</script>
@endpush
