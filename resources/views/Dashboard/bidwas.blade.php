@extends('layouts.app')

@section('title', 'Monitoring Per Bidang')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <div class="glass p-8 rounded-3xl flex flex-col items-center justify-center min-h-[400px]">
        <h3 class="text-xl font-bold mb-8 self-start">Distribusi Workload Antar Bidang</h3>
        <div class="w-full max-w-[300px]">
            <canvas id="bidwasDonut"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <div class="glass p-8 rounded-3xl">
            <h3 class="text-xl font-bold mb-6">Ringkasan Managerial</h3>
            <div class="space-y-4">
                @php
                    $highest = $bidwasData->sortByDesc('total_st')->first();
                    $lowest = $bidwasData->sortBy('total_st')->first();

                    $labelMapping = [
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

                    function getShortLabel($full, $map) {
                        return $map[$full] ?? $full;
                    }
                @endphp

                <div class="p-4 bg-white/5 rounded-2xl border border-white/5 group hover:border-blue-500/50 transition-all">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Bidang Teraktif</p>
                    <div class="flex justify-between items-end">
                        <p class="text-xl font-black text-blue-500 dark:text-blue-400">
                            {{ getShortLabel($highest->nama_bidwas ?? '-', $labelMapping) }}
                        </p>
                        <p class="text-sm font-black text-slate-700 dark:text-slate-300">{{ $highest->total_st ?? 0 }} ST</p>
                    </div>
                </div>

                <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                    <p class="text-slate-400 text-sm mb-1">Total LHP Terbit per Bidang</p>
                    <div class="flex justify-between items-end">
                        <p class="text-xl font-bold text-emerald-400">Total {{ $bidwasData->sum('total_lhp') }} LHP</p>
                        <p class="text-sm font-medium">Monitoring Akhir</p>
                    </div>
                </div>

                <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                    <p class="text-slate-400 text-sm mb-1">Total Pegawai Terdistribusi</p>
                    <div class="flex justify-between items-end">
                        <p class="text-xl font-bold text-violet-400">{{ $bidwasData->sum('total_pegawai') }} Orang</p>
                        <p class="text-sm font-medium">Di {{ $bidwasData->where('total_pegawai', '>', 0)->count() }} Bidang</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass p-8 rounded-3xl bg-blue-600/10 border border-blue-500/20">
            <div class="flex items-start space-x-4">
                <div class="p-3 bg-blue-500/20 rounded-xl text-blue-400">
                    <i data-lucide="info" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="font-bold mb-1">Catatan Pimpinan</h4>
                    <p class="text-sm text-slate-300">Gunakan data ini untuk memantau efektivitas setiap bidang dalam menyelesaikan penugasan menjadi Laporan Hasil Pengawasan (LHP).</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="glass p-8 rounded-3xl overflow-hidden shadow-2xl border border-white/5">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-600 dark:from-white dark:to-slate-300">Tabel Capaian per Bidang</h3>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1 italic opacity-70">Data real-time penyelesaian penugasan</p>
        </div>
        <div class="p-2 bg-blue-500/10 rounded-xl">
            <i data-lucide="table" class="w-5 h-5 text-blue-500"></i>
        </div>
    </div>

    <div class="overflow-x-auto overflow-y-hidden">
        <table class="w-full text-left border-separate border-spacing-y-2">
            <thead>
                <tr class="text-slate-500 dark:text-slate-400">
                    <th class="px-6 py-4 text-[10px] uppercase tracking-[0.2em] font-black first:rounded-l-xl">BIDANG/UNIT</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-[0.2em] font-black text-center">PEGAWAI</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-[0.2em] font-black text-center">TOTAL ST</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-[0.2em] font-black text-center">TOTAL LHP</th>
                    <th class="px-6 py-4 text-[10px] uppercase tracking-[0.2em] font-black last:rounded-r-xl">PENYELESAIAN</th>
                </tr>
            </thead>
            <tbody class="space-y-4">
                @foreach($bidwasData as $item)
                <tr class="group transition-all duration-300 cursor-pointer" onclick="window.location='{{ route('bidwas.detail', $item->id_bidwas) }}'">
                    <td class="px-6 py-5 bg-slate-50 dark:bg-white/5 group-hover:bg-blue-500/5 first:rounded-l-2xl border-l border-t border-b border-slate-200/50 dark:border-white/5 transition-colors">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-black text-slate-800 dark:text-white group-hover:text-blue-500 transition-colors leading-tight">
                                    {{ getShortLabel($item->nama_bidwas, $labelMapping) }}
                                </span>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </div>
                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 font-mono tracking-widest mt-1">{{ $item->kode }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 bg-slate-50 dark:bg-white/5 group-hover:bg-blue-500/5 border-t border-b border-slate-200/50 dark:border-white/5 transition-colors text-center">
                        <span class="font-mono font-black text-xl text-violet-500">{{ $item->total_pegawai }}</span>
                    </td>
                    <td class="px-6 py-5 bg-slate-50 dark:bg-white/5 group-hover:bg-blue-500/5 border-t border-b border-slate-200/50 dark:border-white/5 transition-colors text-center">
                        <span class="font-mono font-black text-xl text-slate-700 dark:text-slate-300">{{ $item->total_st }}</span>
                    </td>
                    <td class="px-6 py-5 bg-slate-50 dark:bg-white/5 group-hover:bg-blue-500/5 border-t border-b border-slate-200/50 dark:border-white/5 transition-colors text-center">
                        <span class="font-mono font-black text-xl text-emerald-500">{{ $item->total_lhp }}</span>
                    </td>
                    <td class="px-6 py-5 bg-slate-50 dark:bg-white/5 group-hover:bg-blue-500/5 last:rounded-r-2xl border-r border-t border-b border-slate-200/50 dark:border-white/5 transition-colors min-w-[200px]">
                        @php
                            $rate = $item->total_st > 0 ? round(($item->total_lhp / $item->total_st) * 100) : 0;

                            $colorClass = 'bg-blue-500';
                            if($rate >= 80) $colorClass = 'bg-gradient-to-r from-emerald-500 to-teal-400 shadow-emerald-500/30';
                            elseif($rate >= 50) $colorClass = 'bg-gradient-to-r from-blue-500 to-indigo-500 shadow-blue-500/30';
                            else $colorClass = 'bg-gradient-to-r from-amber-500 to-orange-400 shadow-amber-500/30';
                        @endphp
                        <div class="flex items-center space-x-4">
                            <div class="flex-1 h-3 bg-slate-200 dark:bg-white/5 rounded-full overflow-hidden p-[2px] shadow-inner">
                                <div class="h-full {{ $colorClass }} rounded-full transition-all duration-1000 ease-out shadow-lg" style="width: {{ $rate }}%"></div>
                            </div>
                            <span class="text-sm font-black text-slate-800 dark:text-slate-200 w-10 text-right">{{ $rate }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    const getChartColors = () => {
        const isLight = document.documentElement.getAttribute('data-theme') === 'light';
        return isLight ? '#1e293b' : '#cbd5e1';
    };

    function shortenBidwasLabel(name) {
        const mapping = {
            'Bagian Tata Usaha': 'Tata Usaha',
            'Koordinator Pengawasan Kelompok JFA Bidang Pengawasan Instansi Pemerintah Pusat': 'Bidang IPP',
            'Koordinator Pengawasan Kelompok JFA Bidang Akuntabilitas Pemerintah Daerah': 'Bidang APD',
            'Koordinator Pengawasan Kelompok JFA Bidang Akuntan Negara': 'Bidang AN',
            'Koordinator Pengawasan Kelompok JFA Bidang Investigasi': 'Bidang Investigasi',
            'Koordinator Pengawasan Kelompok JFA Bidang Program dan Pelaporan serta Pembinaan APIP': 'Bidang P3A',
            'Sub Bagian Kepegawaian': 'Kepegawaian',
            'Sub Bagian Keuangan': 'Keuangan',
            'Sub Bagian Umum': 'Umum'
        };
        return mapping[name] || name;
    }

    document.addEventListener('DOMContentLoaded', function() {
        Chart.register(ChartDataLabels);

        const bCtx = document.getElementById('bidwasDonut').getContext('2d');
        const bidwasChartInstance = new Chart(bCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($bidwasData->pluck('nama_bidwas')) !!}.map(shortenBidwasLabel),
                datasets: [{
                    data: {!! json_encode($bidwasData->pluck('total_st')) !!},
                    backgroundColor: [
                        '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#64748b'
                    ],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: getChartColors(),
                            padding: 20,
                            usePointStyle: true,
                            font: { size: 12, weight: '500' }
                        }
                    },
                    datalabels: {
                        display: false
                    }
                }
            }
        });

        window.addEventListener('themeChanged', function(e) {
            if (bidwasChartInstance.options.plugins && bidwasChartInstance.options.plugins.legend) {
                bidwasChartInstance.options.plugins.legend.labels.color = e.detail.theme === 'light' ? '#1e293b' : '#cbd5e1';
                bidwasChartInstance.update();
            }
        });
    });
</script>
@endpush
@endsection
