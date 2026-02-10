@extends('layouts.app')

@section('title', 'Detail Bidang')

@php
    $bidwasLabelMap = [
        'Bagian Tata Usaha' => 'Tata Usaha',
        'Koordinator Pengawasan Kelompok JFA Bidang Pengawasan Instansi Pemerintah Pusat' => 'Bidang IPP',
        'Koordinator Pengawasan Kelompok JFA Bidang Akuntabilitas Pemerintah Daerah' => 'Bidang APD',
        'Koordinator Pengawasan Kelompok JFA Bidang Akuntan Negara' => 'Bidang AN',
        'Koordinator Pengawasan Kelompok JFA Bidang Investigasi' => 'Bidang Investigasi',
        'Koordinator Pengawasan Kelompok JFA Bidang Program dan Pelaporan serta Pembinaan APIP' => 'Bidang P3A',
        'Sub Bagian Kepegawaian' => 'Kepegawaian',
        'Sub Bagian Keuangan' => 'Keuangan',
        'Sub Bagian Umum' => 'Umum'
    ];
    $bidwasDisplayName = $bidwasLabelMap[$informasiBidang->nm_bidwas] ?? $informasiBidang->nm_bidwas;

    $roleDisplayNameMap = [
        'staff' => 'Staff',
        'korwas_apd_1' => 'Korwas APD 1',
        'korwas_apd_2' => 'Korwas APD 2',
        'korwas_an_1' => 'Korwas AN 1',
        'korwas_an_2' => 'Korwas AN 2',
        'korwas_ipp_1' => 'Korwas IPP 1',
        'korwas_ipp_2' => 'Korwas IPP 2',
        'korwas_investigasi_1' => 'Korwas Investigasi 1',
        'korwas_investigasi_2' => 'Korwas Investigasi 2',
        'korwas_p3a' => 'Korwas P3A',
        'kepala_perwakilan' => 'Kepala Perwakilan',
        'kepala_bagian_umum' => 'Kepala Bagian Umum',
        'subkoor_keuangan' => 'Subkoordinator Keuangan',
        'subkoor_bmn_rt_kearsipan' => 'Subkoordinator BMN & RT',
    ];
@endphp

@section('content')
<div class="mb-6">
    <a href="/dashboard/bidwas" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-blue-400 transition-colors group">
        <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
        Kembali ke Monitoring Per Bidang
    </a>
</div>

<div class="glass p-8 rounded-3xl mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
            <h2 class="text-2xl md:text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-indigo-500">{{ $bidwasDisplayName }}</h2>
            <p class="text-sm text-slate-400 mt-1 font-mono">{{ $informasiBidang->kd_bidwas }}</p>
            <p class="text-xs text-slate-500 mt-2 max-w-lg">{{ $informasiBidang->nm_bidwas }}</p>
        </div>

        <form action="{{ route('bidwas.detail', $informasiBidang->id_bidwas) }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
            <input type="hidden" name="sort" value="{{ $kolomUrut }}">
            <input type="hidden" name="direction" value="{{ $arahUrut }}">
            
            <div class="w-full sm:w-64">
                <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-black mb-2 ml-1">Rentang Tanggal</label>
                <div class="relative group">
                    <div class="status-filter-dropdown dark:bg-white/5 bg-white h-12 border border-slate-200 dark:border-white/10 rounded-xl flex items-center px-4 gap-3 cursor-pointer group-hover:border-blue-500/50 transition-all">
                        <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                        <span id="display_range" class="text-xs font-bold text-slate-600 dark:text-slate-300">
                            {{ $tanggalMulai && $tanggalSelesai ? date('d M Y', strtotime($tanggalMulai)) . ' - ' . date('d M Y', strtotime($tanggalSelesai)) : 'Filter Tanggal' }}
                        </span>
                        <input type="text" id="date_range_picker" class="absolute inset-0 opacity-0 cursor-pointer" readonly>
                        <input type="hidden" name="start_date" id="start_date" value="{{ $tanggalMulai }}">
                        <input type="hidden" name="end_date" id="end_date" value="{{ $tanggalSelesai }}">
                    </div>
                </div>
            </div>

            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="h-12 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all font-bold flex items-center justify-center gap-2 group shadow-lg shadow-blue-500/20">
                    <i data-lucide="filter" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm">Terapkan</span>
                </button>
                <div class="flex gap-1">
                    <a href="{{ route('export.bidwas_detail.excel', array_merge(['id' => $informasiBidang->id_bidwas], request()->query())) }}" class="h-12 w-12 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-500 rounded-xl flex items-center justify-center border border-emerald-500/20 transition-all" title="Export Excel">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                    </a>
                    <a href="{{ route('export.bidwas_detail.pdf', array_merge(['id' => $informasiBidang->id_bidwas], request()->query())) }}" class="h-12 w-12 bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 rounded-xl flex items-center justify-center border border-rose-500/20 transition-all" title="Export PDF">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </a>
                </div>
                @if($tanggalMulai)
                    <a href="{{ route('bidwas.detail', $informasiBidang->id_bidwas) }}" class="h-12 w-12 bg-slate-500/10 hover:bg-slate-500/20 text-slate-500 rounded-xl flex items-center justify-center border border-slate-500/20 transition-all" title="Reset">
                        <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
    <div class="glass p-6 rounded-3xl text-center group hover:border-violet-500/30 transition-all">
        <div class="w-12 h-12 bg-violet-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
            <i data-lucide="users" class="w-6 h-6 text-violet-400"></i>
        </div>
        <p class="text-3xl font-black text-violet-400">{{ $daftarPegawai->count() }}</p>
        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-2">Pegawai</p>
    </div>
    <div class="glass p-6 rounded-3xl text-center group hover:border-blue-500/30 transition-all">
        <div class="w-12 h-12 bg-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
            <i data-lucide="zap" class="w-6 h-6 text-blue-400"></i>
        </div>
        <p class="text-3xl font-black text-blue-400">{{ $totalSuratTugasAktif }}</p>
        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-2">ST Aktif</p>
    </div>
    <div class="glass p-6 rounded-3xl text-center group hover:border-slate-500/30 transition-all">
        <div class="w-12 h-12 bg-slate-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
            <i data-lucide="file-text" class="w-6 h-6 text-slate-400"></i>
        </div>
        <p class="text-3xl font-black">{{ $totalSuratTugas }}</p>
        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-2">Total ST</p>
    </div>
    <div class="glass p-6 rounded-3xl text-center group hover:border-emerald-500/30 transition-all">
        <div class="w-12 h-12 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
            <i data-lucide="clipboard-check" class="w-6 h-6 text-emerald-400"></i>
        </div>
        <p class="text-3xl font-black text-emerald-400">{{ $totalLaporanHasil }}</p>
        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-2">Total LHP</p>
    </div>
</div>

<div class="glass p-8 rounded-3xl overflow-hidden shadow-2xl border border-white/5">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-600 dark:from-white dark:to-slate-300">Daftar Pegawai</h3>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1 italic opacity-70">
                Sorted by {{ $kolomUrut === 'active_tasks' ? 'ST Aktif' : 'Total ST' }} ({{ strtoupper($arahUrut) }})
            </p>
        </div>
        <div class="p-2 bg-violet-500/10 rounded-xl">
            <i data-lucide="users" class="w-5 h-5 text-violet-500"></i>
        </div>
    </div>

    <div class="overflow-x-auto overflow-y-hidden">
        <table class="w-full text-left border-separate border-spacing-y-2">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400">
                    <th class="px-6 py-4 text-[10px] uppercase tracking-[0.2em] font-black first:rounded-l-xl w-8">#</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-[0.2em] font-black">NAMA</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-[0.2em] font-black">NIP</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-[0.2em] font-black">JABATAN</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-[0.2em] font-black text-center cursor-pointer hover:text-blue-500 transition-colors" onclick="sortData('active_tasks')">
                        <div class="flex items-center justify-center gap-1">
                            ST AKTIF
                            @if($kolomUrut === 'active_tasks')
                                <i data-lucide="{{ $arahUrut === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3 h-3 text-blue-500"></i>
                            @else
                                <i data-lucide="chevrons-up-down" class="w-3 h-3 opacity-30"></i>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-[0.2em] font-black text-center last:rounded-r-xl cursor-pointer hover:text-blue-500 transition-colors" onclick="sortData('total_assignments')">
                        <div class="flex items-center justify-center gap-1">
                            TOTAL ST
                            @if($kolomUrut === 'total_assignments')
                                <i data-lucide="{{ $arahUrut === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3 h-3 text-blue-500"></i>
                            @else
                                <i data-lucide="chevrons-up-down" class="w-3 h-3 opacity-30"></i>
                            @endif
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="space-y-4">
                @forelse($daftarPegawai as $index => $pegawai)
                <tr class="group transition-all duration-300 cursor-pointer" onclick="window.location='{{ route('employee.detail', ['nip' => $pegawai->nip, 'start_date' => $tanggalMulai, 'end_date' => $tanggalSelesai]) }}'">
                    <td class="px-6 py-5 bg-slate-50 dark:bg-white/5 group-hover:bg-blue-500/5 first:rounded-l-2xl border-l border-t border-b border-slate-200/50 dark:border-white/5 transition-colors">
                        <span class="text-sm text-slate-400 font-mono">{{ $index + 1 }}</span>
                    </td>
                    <td class="px-6 py-5 bg-slate-50 dark:bg-white/5 group-hover:bg-blue-500/5 border-t border-b border-slate-200/50 dark:border-white/5 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-blue-500/20 rounded-full flex items-center justify-center text-blue-400 font-bold text-sm border border-blue-500/30 flex-shrink-0">
                                {{ substr($pegawai->nama, 0, 1) }}
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 dark:text-white group-hover:text-blue-500 transition-colors block">{{ $pegawai->nama }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 bg-slate-50 dark:bg-white/5 group-hover:bg-blue-500/5 border-t border-b border-slate-200/50 dark:border-white/5 transition-colors">
                        <span class="text-sm font-mono text-slate-500">{{ $pegawai->nip }}</span>
                    </td>
                    <td class="px-6 py-5 bg-slate-50 dark:bg-white/5 group-hover:bg-blue-500/5 border-t border-b border-slate-200/50 dark:border-white/5 transition-colors">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $pegawai->user_role !== 'staff' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20' }}">
                            {{ $roleDisplayNameMap[$pegawai->user_role] ?? $pegawai->user_role }}
                        </span>
                    </td>
                    <td class="px-6 py-5 bg-slate-50 dark:bg-white/5 group-hover:bg-blue-500/5 border-t border-b border-slate-200/50 dark:border-white/5 transition-colors text-center">
                        @if($pegawai->active_tasks > 0)
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-blue-500/20 text-blue-400 font-black text-lg">{{ $pegawai->active_tasks }}</span>
                        @else
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 font-bold text-sm">
                                <i data-lucide="check" class="w-5 h-5"></i>
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-5 bg-slate-50 dark:bg-white/5 group-hover:bg-blue-500/5 last:rounded-r-2xl border-r border-t border-b border-slate-200/50 dark:border-white/5 transition-colors text-center">
                        <span class="font-mono font-black text-lg text-slate-700 dark:text-slate-300">{{ $pegawai->total_assignments }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-16 h-16 bg-slate-500/10 rounded-full flex items-center justify-center">
                                <i data-lucide="users-x" class="w-8 h-8 text-slate-500"></i>
                            </div>
                            <p class="text-slate-500 font-bold">Belum ada pegawai di bidang ini</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    function sortData(column) {
        let currentSort = @json($kolomUrut);
        let currentDirection = @json($arahUrut);
        let newDirection = 'desc';

        if (currentSort === column) {
            newDirection = currentDirection === 'desc' ? 'asc' : 'desc';
        }

        const url = new URL(window.location.href);
        url.searchParams.set('sort', column);
        url.searchParams.set('direction', newDirection);
        window.location.href = url.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        flatpickr("#date_range_picker", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: @json($tanggalMulai) && @json($tanggalSelesai) ? [@json($tanggalMulai), @json($tanggalSelesai)] : null,
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const start = instance.formatDate(selectedDates[0], "Y-m-d");
                    const end = instance.formatDate(selectedDates[1], "Y-m-d");
                    const displayStart = instance.formatDate(selectedDates[0], "d M Y");
                    const displayEnd = instance.formatDate(selectedDates[1], "d M Y");
                    
                    document.getElementById('start_date').value = start;
                    document.getElementById('end_date').value = end;
                    document.getElementById('display_range').innerText = displayStart + ' - ' + displayEnd;
                }
            }
        });
    });
</script>
@endpush
@endsection
