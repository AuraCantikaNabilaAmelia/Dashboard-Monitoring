@forelse($daftarSuratTugasBelumSelesai as $suratTugas)
<div class="p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20 hover:border-red-500/40 transition-all">
    <div class="flex justify-between items-start mb-1">
        <p class="font-bold text-slate-800 dark:text-slate-200 text-sm line-clamp-1" title="{{ $suratTugas->nama_penugasan }}">
            {{ $suratTugas->nama_penugasan }}
        </p>
        <span class="text-xs font-bold text-red-500 bg-red-100 dark:bg-red-500/20 px-2 py-0.5 rounded flex-shrink-0 ml-2">
            Telat {{ (int) \Carbon\Carbon::parse($suratTugas->end_date)->diffInDays(now()) }} Hari
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

<div class="hidden overdue-pagination-data">
    {!! json_encode([
        'current_page' => $daftarSuratTugasBelumSelesai->currentPage(),
        'last_page' => $daftarSuratTugasBelumSelesai->lastPage(),
        'total' => $daftarSuratTugasBelumSelesai->total()
    ]) !!}
</div>
