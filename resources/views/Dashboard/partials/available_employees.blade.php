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
    <span class="px-2 py-1 bg-emerald-500 text-white text-xs font-bold rounded-full">
        Tersedia
    </span>
</div>
@empty
<div class="flex flex-col items-center justify-center py-8 text-center">
    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-500/20 rounded-full flex items-center justify-center mb-3">
        <i data-lucide="users" class="w-6 h-6 text-amber-500"></i>
    </div>
    <p class="text-slate-500 font-medium">Semua pegawai sedang bertugas.</p>
</div>
@endforelse

<div class="hidden available-pagination-data">
    {!! json_encode([
        'current_page' => $pegawaiTersedia->currentPage(),
        'last_page' => $pegawaiTersedia->lastPage(),
        'total' => $pegawaiTersedia->total()
    ]) !!}
</div>
