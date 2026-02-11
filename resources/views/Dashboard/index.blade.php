@extends('layouts.app')

@section('title', 'Overview Monitoring')

@section('content')

@if(auth()->check() && in_array(auth()->user()->role, ['pimpinan', 'kabid', 'admin']))
<div class="mb-8">
    <div class="flex items-center space-x-3 mb-6">
        <div class="p-2 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-lg shadow-blue-500/30">
            <i data-lucide="crown" class="w-6 h-6 text-white"></i>
        </div>
        <div>
        <h2 class="text-lg md:text-xl lg:text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-600 dark:from-white dark:to-slate-300">
            Leadership Insights
        </h2>
        <p class="text-slate-500 dark:text-slate-400 text-[10px] md:text-xs">Ringkasan eksekutif untuk pengambilan keputusan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="glass p-6 rounded-3xl border-l-4 border-blue-500">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-blue-500"></i>
                Top 5 Pegawai Tersibuk
            </h3>
            <div class="space-y-3">
                @forelse($pegawaiPalingSibuk as $indeks => $dataPegawai)
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-sm">
                            {{ $indeks + 1 }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $dataPegawai->nama }}</p>
                            <p class="text-xs text-slate-500">{{ $dataPegawai->nip }}</p>
                        </div>
                    </div>
                    <div class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded-full">
                        {{ $dataPegawai->active_tasks }} ST
                    </div>
                </div>
                @empty
                <p class="text-slate-500 italic text-center py-4">Tidak ada penugasan aktif hari ini.</p>
                @endforelse
            </div>
        </div>

        <div class="glass p-6 rounded-3xl border-l-4 border-emerald-500">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <i data-lucide="user-check" class="w-5 h-5 text-emerald-500"></i>
                    Pegawai Tersedia ({{ $totalPegawai - $pegawaiPalingSibuk->sum('active_tasks') > 0 ? $totalPegawai - $pegawaiPalingSibuk->sum('active_tasks') : count($pegawaiTersedia) }})
                </h3>
                <button onclick="openPaginationModal('available')" class="group flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-500/10 hover:bg-blue-500 text-blue-500 hover:text-white transition-all duration-300 border border-blue-500/20 hover:border-blue-500 shadow-lg shadow-blue-500/10">
                    <span class="text-[10px] font-black uppercase tracking-widest">Selengkapnya</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 group-hover:translate-x-0.5 transition-transform"></i>
                </button>
            </div>
            <div class="space-y-3 max-h-[280px] overflow-y-auto pr-2">
                @forelse($pegawaiTersedia as $dataPegawai)
                <div class="flex items-center justify-between p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl border border-emerald-100 dark:border-emerald-500/20 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold text-sm">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $dataPegawai->nama }}</p>
                            <p class="text-xs text-slate-500">{{ $dataPegawai->nip }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-500/20 rounded-full flex items-center justify-center mb-3">
                        <i data-lucide="users" class="w-6 h-6 text-amber-500"></i>
                    </div>
                    <p class="text-slate-500 font-medium">Semua pegawai sedang bertugas.</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="glass p-6 rounded-3xl border-l-4 border-red-500">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
                    Perhatian Khusus ({{ $totalBelumSelesai }})
                </h3>
                <button onclick="openPaginationModal('overdue')" class="group flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-500/10 hover:bg-blue-500 text-blue-500 hover:text-white transition-all duration-300 border border-blue-500/20 hover:border-blue-500 shadow-lg shadow-blue-500/10">
                    <span class="text-[10px] font-black uppercase tracking-widest">Selengkapnya</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 group-hover:translate-x-0.5 transition-transform"></i>
                </button>
            </div>
             <div class="space-y-3 max-h-[280px] overflow-y-auto pr-2">
                @forelse($daftarSuratTugasBelumSelesai as $suratTugas)
                <div class="p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20 hover:border-red-500/40 transition-all">
                    <div class="flex justify-between items-start mb-1">
                        <p class="font-bold text-slate-800 dark:text-slate-200 text-sm line-clamp-1" title="{{ $suratTugas->nama_penugasan }}">
                            {{ $suratTugas->nama_penugasan }}
                        </p>
                        <span class="text-xs font-bold text-red-500 bg-red-100 dark:bg-red-500/20 px-2 py-0.5 rounded flex-shrink-0 ml-2">
                            Telat {{ \Carbon\Carbon::parse($suratTugas->end_date)->diffInDays(now()) }} H
                        </span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            {{ \Carbon\Carbon::parse($suratTugas->end_date)->format('d M Y') }}
                        </span>
                        <a href="{{ route('st.detail', $suratTugas->id_st) }}" class="text-xs text-blue-500 hover:underline">Detail &rarr;</a>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-500/20 rounded-full flex items-center justify-center mb-3">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-500"></i>
                    </div>
                    <p class="text-slate-500 font-medium">Tidak ada penugasan terlambat!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="glass p-6 rounded-2xl relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-blue-500/20 rounded-xl text-blue-400">
                <i data-lucide="clipboard-list" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-slate-600 dark:text-slate-400 text-sm font-bold mb-1">ST Aktif Hari Ini</h3>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $totalSuratTugasAktif }}</p>
            </div>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-emerald-500/20 rounded-xl text-emerald-400">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-slate-600 dark:text-slate-400 text-sm font-bold mb-1">Total Pegawai</h3>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $totalPegawai }}</p>
            </div>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-all"></div>
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-purple-500/20 rounded-xl text-purple-400">
                <i data-lucide="file-check" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-slate-600 dark:text-slate-400 text-sm font-bold mb-1">Total LHP Terbit</h3>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $totalLHP }}</p>
            </div>
        </div>
    </div>

    <div class="glass p-6 rounded-2xl relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-amber-500/20 rounded-xl text-amber-400">
                <i data-lucide="target" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-slate-600 dark:text-slate-400 text-sm font-bold mb-1">Target PKPT</h3>
                <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $targetPKPT }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <div class="glass p-6 rounded-3xl relative">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold">Distribusi Penugasan per Bidang</h3>
            <button onclick="expandChart('bidwasChart', 'Distribusi Penugasan per Bidang')" class="p-2 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-lg transition-all text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/10 shadow-sm">
                <i data-lucide="maximize-2" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="h-[300px] w-full relative">
            <canvas id="bidwasChart"></canvas>
        </div>
    </div>

    <div class="glass p-6 rounded-3xl relative">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold">Tren Penugasan Bulanan ({{ date('Y') }})</h3>
            <button onclick="expandChart('trendChart', 'Tren Penugasan Bulanan')" class="p-2 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-lg transition-all text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/10 shadow-sm">
                <i data-lucide="maximize-2" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="h-[300px] w-full relative">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="glass p-6 rounded-3xl lg:col-span-1">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold">Status Penugasan</h3>
            <button onclick="expandChart('statusChart', 'Status Penugasan')" class="p-2 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-lg transition-all text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/10 shadow-sm">
                <i data-lucide="maximize-2" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="h-[300px] flex items-center justify-center relative">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <div class="glass p-6 rounded-3xl lg:col-span-2">
        <h3 class="text-lg font-bold mb-6">Informasi Kinerja Terkini</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-white/5 group hover:border-blue-500/50 transition-all shadow-sm">
                <div class="flex items-center space-x-3 mb-2 text-blue-400">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                    <span class="font-bold">Efisiensi Audit</span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Rasio rata-rata penyelesaian ST tepat waktu mencapai 82% pada kuartal ini.</p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-white/5 group hover:border-emerald-500/50 transition-all shadow-sm">
                <div class="flex items-center space-x-3 mb-2 text-emerald-500 dark:text-emerald-400">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                    <span class="font-bold">Kualitas Output</span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Total 124 LHP terakreditasi 'A' dalam sistem monitoring kualitas internal.</p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-white/5 group hover:border-amber-500/50 transition-all shadow-sm">
                <div class="flex items-center space-x-3 mb-2 text-amber-500 dark:text-amber-400">
                    <i data-lucide="zap" class="w-5 h-5"></i>
                    <span class="font-bold">Respon Cepat</span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Rata-rata waktu disposisi surat masuk menjadi surat tugas adalah 2 hari kerja.</p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-white/5 group hover:border-purple-500/50 transition-all shadow-sm">
                <div class="flex items-center space-x-3 mb-2 text-purple-500 dark:text-purple-400">
                    <i data-lucide="award" class="w-5 h-5"></i>
                    <span class="font-bold">Inovasi Monitoring</span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Visualisasi data interaktif membantu pimpinan memantau beban kerja secara real-time.</p>
            </div>
        </div>
    </div>
</div>

@push('modals')
<div id="chartModal" class="fixed inset-0 dark:bg-slate-950/95 bg-white/98 backdrop-blur-3xl z-[100] hidden flex flex-col p-4 md:p-8 modal-root">
    <div class="glass w-full max-w-7xl mx-auto rounded-[40px] p-6 md:p-10 flex flex-col h-full overflow-hidden shadow-2xl border border-white/10 dark:border-white/5">
        <div class="flex justify-between items-center mb-6 flex-shrink-0">
            <div>
                <h2 id="modalChartTitle" class="text-2xl md:text-3xl font-semibold tracking-tight transition-colors" style="color: var(--bpkp-text-light)">Chart Detail</h2>
                <p id="modalChartSubtitle" class="mt-1 font-medium text-sm" style="color: var(--bpkp-text-muted)">Analisis mendalam data operasional BPKP Jawa Barat</p>
            </div>
            <button onclick="closeChartModal()" class="p-3 bg-slate-200/50 dark:bg-white/5 hover:bg-slate-300 dark:hover:bg-white/10 rounded-2xl transition-all border border-slate-300 dark:border-white/10 shadow-lg" style="color: var(--bpkp-text-light)">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <div class="flex-1 min-h-0 w-full mb-6 relative bg-slate-50/50 dark:bg-white/5 rounded-3xl p-4 md:p-8 border border-slate-100 dark:border-white/5 shadow-inner">
            <canvas id="expandedChart"></canvas>
        </div>

        <div id="chartDescription" class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 dark:from-blue-500/20 dark:to-purple-500/20 p-6 rounded-3xl border border-blue-500/10 dark:border-white/10 shadow-lg flex-shrink-0">
        </div>
    </div>
</div>

<div id="detailsModal" class="fixed inset-0 dark:bg-slate-950/95 bg-white/90 backdrop-blur-2xl z-[100] hidden flex items-center justify-center p-4 modal-root">
    <div class="glass w-full max-w-2xl rounded-[40px] overflow-hidden shadow-2xl border border-slate-200 dark:border-white/10 flex flex-col h-[80vh]">
        <div class="p-8 border-b border-slate-200 dark:border-white/10 flex justify-between items-center flex-shrink-0">
            <div>
                <h3 id="modalDetailsTitle" class="text-2xl font-bold text-slate-800 dark:text-white">Detail Data</h3>
                <p id="modalDetailsSubtitle" class="text-sm text-slate-500 dark:text-slate-400 mt-1">Menampilkan daftar lengkap</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 rounded-2xl p-1.5 px-4 border border-slate-200 dark:border-white/10 shadow-inner">
                    <button onclick="changePage(currentModalType, -1)" id="modal-prev" class="p-1 hover:bg-slate-200 dark:hover:bg-white/10 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed group" disabled>
                        <i data-lucide="chevron-left" class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:text-blue-500 transition-colors"></i>
                    </button>
                    <span class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mx-2"><span id="modal-current" class="text-blue-500">1</span> / <span id="modal-last">1</span></span>
                    <button onclick="changePage(currentModalType, 1)" id="modal-next" class="p-1 hover:bg-slate-200 dark:hover:bg-white/10 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed group" disabled>
                        <i data-lucide="chevron-right" class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:text-blue-500 transition-colors"></i>
                    </button>
                </div>
                <button onclick="closeDetailsModal()" class="p-3 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 rounded-2xl transition-all border border-slate-200 dark:border-white/10 shadow-lg text-slate-600 dark:text-white">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
        <div id="modal-container" class="p-8 overflow-y-auto space-y-4 custom-scrollbar flex-1 relative">
            <!-- Content loaded via AJAX -->
        </div>
        <div class="p-6 bg-slate-50/50 dark:bg-white/5 border-t border-slate-200 dark:border-white/10 text-center flex-shrink-0">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest">Total: <span id="modal-total" class="text-blue-500 text-lg mx-1">0</span> Item Terdata</p>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    let charts = {};
    let expandedChartInstance = null;

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

    const bidwasLabelsFull = {!! json_encode($statistikBidwas->pluck('nama')) !!}.map(shortenBidwasLabel);
    const bidwasLabelsShort = {!! json_encode($statistikBidwas->pluck('kode')) !!};
    const bidwasData = {!! json_encode($statistikBidwas->pluck('total')) !!};

    const trendData = {!! json_encode(collect(range(1, 12))->map(function($m) use ($trendBulanan) {
        return $trendBulanan->where('bulan', $m)->first()->total ?? 0;
    })) !!};

    const statusLabels = {!! json_encode($distribusiStatus->pluck('status_st')) !!};
    const statusData = {!! json_encode($distribusiStatus->pluck('total')) !!};

    document.addEventListener('DOMContentLoaded', function() {
        Chart.register(ChartDataLabels);

        const getChartColors = () => {
            const isLight = document.documentElement.getAttribute('data-theme') === 'light';
            return {
                text: isLight ? '#1e293b' : '#cbd5e1',
                grid: isLight ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.05)',
                shadow: isLight ? 'rgba(0, 0, 0, 0.05)' : 'rgba(255, 255, 255, 0.02)'
            };
        };

        const canvasBidwas = document.getElementById('bidwasChart');
        if (canvasBidwas) {
            const ctxBidwas = canvasBidwas.getContext('2d');
            const colors = getChartColors();

            const createGrad = (c1, c2) => {
                const g = ctxBidwas.createLinearGradient(0, 0, 0, 300);
                g.addColorStop(0, c1);
                g.addColorStop(1, c2);
                return g;
            };

            const gradients = [
                createGrad('#6366f1', 'rgba(99, 102, 241, 0.1)'),
                createGrad('#10b981', 'rgba(16, 185, 129, 0.1)'),
                createGrad('#f59e0b', 'rgba(245, 158, 11, 0.1)'),
                createGrad('#ef4444', 'rgba(239, 68, 68, 0.1)'),
                createGrad('#8b5cf6', 'rgba(139, 92, 246, 0.1)'),
                createGrad('#06b6d4', 'rgba(6, 182, 212, 0.1)'),
                createGrad('#ec4899', 'rgba(236, 72, 153, 0.1)'),
                createGrad('#60a5fa', 'rgba(96, 165, 250, 0.1)'),
            ];

            const borderColors = [
                '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#60a5fa'
            ];

            charts['bidwasChart'] = new Chart(ctxBidwas, {
                type: 'bar',
                data: {
                    labels: bidwasLabelsFull,
                    datasets: [{
                        label: 'Penugasan',
                        data: bidwasData,
                        backgroundColor: gradients,
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 12,
                        hoverBorderWidth: 4,
                        hoverBorderColor: (ctx) => borderColors[ctx.dataIndex]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        datalabels: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: colors.grid },
                            ticks: { color: colors.text, font: { weight: '500' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: colors.text, autoSkip: false, maxRotation: 45, minRotation: 45, font: { weight: '500' } }
                        }
                    }
                }
            });
        }

        const canvasTrend = document.getElementById('trendChart');
        if (canvasTrend) {
            const ctxTrend = canvasTrend.getContext('2d');
            const colors = getChartColors();
            const gradTrend = ctxTrend.createLinearGradient(0, 0, 0, 300);
            gradTrend.addColorStop(0, 'rgba(139, 92, 246, 0.4)');
            gradTrend.addColorStop(1, 'rgba(139, 92, 246, 0)');

            charts['trendChart'] = new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Trend ST',
                        data: trendData,
                        borderColor: '#8b5cf6',
                        borderWidth: 4,
                        fill: true,
                        backgroundColor: gradTrend,
                        tension: 0.4,
                        pointBackgroundColor: '#8b5cf6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        datalabels: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: colors.grid },
                            ticks: { color: colors.text, font: { weight: '500' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: colors.text, font: { weight: '500' } }
                        }
                    }
                }
            });
        }

        const canvasStatus = document.getElementById('statusChart');
        if (canvasStatus) {
            const ctxStatus = canvasStatus.getContext('2d');
            const colors = getChartColors();
            const statusColorMap = {
                'Realisasi': '#10b981',
                'Batal': '#ef4444',
                'Konsep': '#f59e0b',
                'Final': '#3b82f6',
                'Selesai': '#6366f1',
                'tidak aktif': '#94a3b8',
                'Review Dalnis': '#8b5cf6',
                'Review Daltu': '#06b6d4'
            };

            charts['statusChart'] = new Chart(ctxStatus, {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: statusLabels.map(label => statusColorMap[label] || '#ec4899'),
                        borderWidth: 1,
                        borderColor: document.documentElement.getAttribute('data-theme') === 'light' ? '#fff' : 'rgba(15, 23, 42, 0.5)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: colors.text,
                                padding: 20,
                                font: { size: 11, weight: '500' }
                            }
                        },
                        datalabels: {
                            display: false
                        }
                    }
                }
            });
        }

        window.addEventListener('themeChanged', function(e) {
            const newColors = getChartColors();
            Object.values(charts).forEach(chart => {
                if (chart.options.scales) {
                    if (chart.options.scales.x) chart.options.scales.x.ticks.color = newColors.text;
                    if (chart.options.scales.y) {
                        chart.options.scales.y.ticks.color = newColors.text;
                        chart.options.scales.y.grid.color = newColors.grid;
                    }
                }
                if (chart.options.plugins && chart.options.plugins.legend) {
                    chart.options.plugins.legend.labels.color = newColors.text;
                }
                chart.update();
            });
        });

        lucide.createIcons();
    });

    function expandChart(chartId, title) {
        const modal = document.getElementById('chartModal');
        const modalTitle = document.getElementById('modalChartTitle');
        const canvas = document.getElementById('expandedChart');
        const ctx = canvas.getContext('2d');
        const desc = document.getElementById('chartDescription');

        const isLight = document.documentElement.getAttribute('data-theme') === 'light';
        const textColor = isLight ? '#020617' : '#ffffff';
        const mutedTextColor = isLight ? '#334155' : '#94a3b8';
        const gridColor = isLight ? 'rgba(0,0,0,0.15)' : 'rgba(255,255,255,0.1)';

        modalTitle.innerText = title;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        if (expandedChartInstance) expandedChartInstance.destroy();

        const original = charts[chartId];
        let expandedData = JSON.parse(JSON.stringify(original.data));

        let expandedOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { color: textColor, font: { size: 14, weight: '700' } }
                },
                tooltip: {
                    backgroundColor: isLight ? 'rgba(255, 255, 255, 0.98)' : 'rgba(15, 23, 42, 0.95)',
                    titleColor: isLight ? '#020617' : '#fff',
                    bodyColor: isLight ? '#020617' : '#fff',
                    borderColor: isLight ? 'rgba(0,0,0,0.2)' : 'rgba(255,255,255,0.2)',
                    borderWidth: 1,
                    padding: 12
                },
                datalabels: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { size: 13, weight: '500' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: textColor, font: { size: 13, weight: '500' } }
                }
            }
        };

        if (chartId === 'bidwasChart') {
            expandedData.labels = bidwasLabelsFull;
            expandedOptions.indexAxis = 'y';
            expandedOptions.scales.x.beginAtZero = true;
            expandedOptions.scales.y.ticks.autoSkip = false;

            const vibrantColors = [
                '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#60a5fa'
            ];

            expandedData.datasets[0].backgroundColor = vibrantColors.map(c => {
                const g = ctx.createLinearGradient(0, 0, 800, 0);
                g.addColorStop(0, c);
                g.addColorStop(1, c + '33');
                return g;
            });
            expandedData.datasets[0].borderColor = vibrantColors;
        } else if (chartId === 'trendChart') {
            const grad = ctx.createLinearGradient(0, 0, 0, 400);
            grad.addColorStop(0, 'rgba(139, 92, 246, 0.5)');
            grad.addColorStop(1, 'rgba(139, 92, 246, 0)');
            expandedData.datasets[0].backgroundColor = grad;
        } else if (chartId === 'statusChart') {
            expandedOptions.plugins.legend.position = 'right';
        }

        expandedChartInstance = new Chart(canvas, {
            type: original.config.type,
            data: expandedData,
            options: expandedOptions
        });

        const titleClass = isLight ? 'text-blue-700' : 'text-white';
        const pClass = isLight ? 'text-slate-700' : 'text-slate-200';

        if (chartId === 'bidwasChart') {
            desc.innerHTML = `<h4 class="${titleClass} font-bold mb-2 text-lg flex items-center gap-3"><i data-lucide="info" class="w-6 h-6 text-blue-500"></i>Analisis Beban Kerja</h4>
                <p class="${pClass} font-medium">Data ini menampilkan distribusi penugasan riil saat ini. Bidang dengan bar terpanjang menunjukkan volume kerja tertinggi yang perlu mendapat perhatian pimpinan terkait alokasi SDM.</p>`;
        } else if (chartId === 'trendChart') {
            desc.innerHTML = `<h4 class="${titleClass} font-bold mb-2 text-lg flex items-center gap-3"><i data-lucide="trending-up" class="w-6 h-6 text-purple-500"></i>Analisis Tren Pengawasan</h4>
                <p class="${pClass} font-medium">Grafik menunjukkan pola penugasan bulanan sepanjang tahun. Fluktuasi data dipengaruhi oleh prioritas pengawasan nasional dan daerah.</p>`;
        } else {
            desc.innerHTML = `<h4 class="${titleClass} font-bold mb-2 text-lg flex items-center gap-3"><i data-lucide="activity" class="w-6 h-6 text-emerald-500"></i>Status Real-time</h4>
                <p class="${pClass} font-medium">Persentase status memberikan gambaran progres penyelesaian tugas secara kolektif di perwakilan.</p>`;
        }

        lucide.createIcons();
    }

    function closeChartModal() {
        document.getElementById('chartModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    let currentModalType = null;
    let dashboardState = {
        available: { current: 1, last: 1 },
        overdue: { current: 1, last: 1 }
    };

    function openPaginationModal(type) {
        currentModalType = type;
        const modal = document.getElementById('detailsModal');
        const title = document.getElementById('modalDetailsTitle');
        const subtitle = document.getElementById('modalDetailsSubtitle');
        
        if (type === 'available') {
            title.innerText = 'Pegawai Tersedia';
            subtitle.innerText = 'Daftar lengkap pegawai yang tidak memiliki penugasan aktif hari ini';
        } else {
            title.innerText = 'Perhatian Khusus';
            subtitle.innerText = 'Daftar penugasan yang telah melewati tenggat waktu namun belum selesai';
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        dashboardState[type].current = 1;
        loadModalData(type, 1);
    }

    function closeDetailsModal() {
        document.getElementById('detailsModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    async function loadModalData(type, page) {
        const container = document.getElementById('modal-container');
        const prevBtn = document.getElementById('modal-prev');
        const nextBtn = document.getElementById('modal-next');
        
        container.classList.add('opacity-40');
        prevBtn.disabled = true;
        nextBtn.disabled = true;

        const url = type === 'available' 
            ? `/dashboard/ajax/available-employees?available_page=${page}`
            : `/dashboard/ajax/overdue-tasks?overdue_page=${page}`;

        try {
            const response = await fetch(url);
            const html = await response.text();
            
            container.innerHTML = html;
            
            const paginationData = JSON.parse(container.querySelector(`.${type}-pagination-data`).textContent);
            
            dashboardState[type].current = paginationData.current_page;
            dashboardState[type].last = paginationData.last_page;
            
            document.getElementById('modal-current').textContent = dashboardState[type].current;
            document.getElementById('modal-last').textContent = dashboardState[type].last;
            document.getElementById('modal-total').textContent = paginationData.total;

            prevBtn.disabled = dashboardState[type].current === 1;
            nextBtn.disabled = dashboardState[type].current === dashboardState[type].last;
            
            lucide.createIcons();
            container.scrollTop = 0;
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            container.classList.remove('opacity-40');
        }
    }

    function changePage(type, delta) {
        const nextPage = dashboardState[type].current + delta;
        if (nextPage < 1 || nextPage > dashboardState[type].last) return;
        loadModalData(type, nextPage);
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeChartModal();
            closeDetailsModal();
        }
    });
</script>
@endpush
@endsection
