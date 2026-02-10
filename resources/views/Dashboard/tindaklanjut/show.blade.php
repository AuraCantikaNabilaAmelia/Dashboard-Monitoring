@extends('layouts.app')

@section('title', 'Detail Tindak Lanjut')

@section('content')
<div class="mb-6">
    <a href="{{ route('tindaklanjut.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 flex items-center space-x-2 text-sm font-bold transition-colors group">
        <i data-lucide="chevron-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
        <span>Kembali ke Monitoring</span>
    </a>
</div>

{{-- Standard Header from detail.blade.php --}}
<div class="glass p-8 rounded-3xl shadow-xl border border-slate-200/50 dark:border-white/5 mb-8">
    <div class="flex flex-col md:flex-row justify-between items-start mb-6 gap-4">
        <div>
            <span class="px-3 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-black rounded-lg uppercase tracking-widest mb-2 inline-block border border-blue-500/10">Surat Tugas</span>
            <h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $st->nama_penugasan }}</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-1 font-mono text-xs font-bold">ID: #{{ $st->id_st }}</p>
        </div>
        <div class="text-right">
            @php
                $statusClass = match(strtolower($st->status_st)) {
                    'batal' => 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20',
                    'final', 'selesai' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                    'tidak aktif' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
                    default => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20'
                };
                $statusIcon = match(strtolower($st->status_st)) {
                    'batal' => 'x-circle',
                    'final', 'selesai' => 'check-circle',
                    'tidak aktif' => 'minus-circle',
                    default => 'clock'
                };
            @endphp
            <span class="px-4 py-2 {{ $statusClass }} text-sm font-black rounded-xl border shadow-sm inline-flex items-center gap-2">
                <i data-lucide="{{ $statusIcon }}" class="w-4 h-4"></i>
                {{ $st->status_st }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 dark:border-white/5 pt-8">
        <div class="space-y-6">
            <div>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">Tanggal Mulai</p>
                <p class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $st->start_date ? date('d F Y', strtotime($st->start_date)) : '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">Nomor Surat Tugas</p>
                <p class="text-slate-700 dark:text-slate-300 font-bold font-mono">{{ $st->no_surat_tugas ?? '-' }}</p>
            </div>
        </div>
        <div class="space-y-6">
            <div>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">Tanggal Selesai</p>
                <p class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $st->end_date ? date('d F Y', strtotime($st->end_date)) : '-' }}</p>
            </div>
            <div>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">Bidang / Koordinator</p>
                <p class="text-slate-700 dark:text-slate-300 font-bold">{{ $st->nm_bidwas }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Timeline Tindak Lanjut Section --}}
<div class="glass p-8 rounded-3xl min-h-[400px]">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h3 class="text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-slate-400">Catatan Tindak Lanjut</h3>
            <p class="text-xs text-slate-500 mt-1 uppercase tracking-widest font-black opacity-60 italic">Riwayat naratif perkembangan penugasan</p>
        </div>
        <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
            <i data-lucide="history" class="w-6 h-6"></i>
        </div>
    </div>

    @if(auth()->user()->role === 'pimpinan' || auth()->user()->bidang_id == $st->id_bidwas)
    <div class="mb-12">
        <div class="glass bg-blue-500/5 border-blue-500/20 p-6 rounded-2xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center text-white">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                </div>
                <h4 class="font-black text-slate-800 dark:text-white uppercase tracking-wider text-xs">Tambah Catatan Tindak Lanjut</h4>
            </div>
            <form action="{{ route('tindaklanjut.addEntry', $st->id_st) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="space-y-1">
                    <textarea name="catatan" rows="3" class="w-full glass bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl px-5 py-4 text-sm font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/50 transition-all outline-none" placeholder="Tuliskan perkembangan tindak lanjut atau penugasan di sini..."></textarea>
                    @error('catatan') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ $message }}</p> @enderror
                </div>
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="relative group">
                        <input type="file" name="file" id="file" class="hidden" onchange="updateFileName(this)">
                        <label for="file" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold text-slate-500 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-white/5 transition-all cursor-pointer">
                            <i data-lucide="paperclip" class="w-4 h-4"></i>
                            <span id="file-label">Lampirkan File (Opsional)</span>
                        </label>
                        <p class="text-[9px] text-slate-400 mt-1 ml-1 italic">*Max 20MB: PDF, Word, Excel, JPG, PNG</p>
                    </div>
                    
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2">
                        Simpan Catatan
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="mb-12 p-6 bg-slate-50 dark:bg-white/5 border-2 border-dashed border-slate-200 dark:border-white/10 rounded-2xl text-center">
        <p class="text-slate-500 italic text-sm font-medium">Hanya anggota bidang terkait yang dapat menambahkan catatan tindak lanjut.</p>
    </div>
    @endif

    <div class="relative space-y-10">
        <div class="absolute left-6 top-0 bottom-0 w-[2px] bg-slate-100 dark:bg-white/5"></div>
        
        @forelse($entries as $entry)
        <div class="relative pl-16 group">
            <div class="absolute left-4 top-1 w-4 h-4 rounded-full border-4 border-white dark:border-slate-900 bg-blue-500 z-10 group-hover:scale-125 transition-transform"></div>
            
            <div class="glass p-6 rounded-2xl group-hover:border-blue-500/30 transition-all border border-transparent shadow-sm hover:shadow-xl hover:shadow-blue-500/5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-black text-slate-900 dark:text-white">{{ $entry->created_by_nama }}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-100 dark:bg-white/10 text-slate-500 rounded-lg uppercase tracking-wider italic">{{ $entry->created_by_nip }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest font-mono">{{ $entry->created_at->format('d M Y - H:i') }}</span>
                        
                        @if($entry->created_by_nip === auth()->user()->nip)
                        <form id="delete-entry-{{ $entry->id }}" action="{{ route('tindaklanjut.deleteEntry', $entry->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                    @click="$store.confirm.ask('Hapus catatan ini?', () => document.getElementById('delete-entry-{{ $entry->id }}').submit(), 'Hapus Catatan', 'Ya, Hapus')"
                                    class="text-red-400 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                
                <div class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed font-bold whitespace-pre-line mb-6 italic opacity-90">
                    "{{ $entry->catatan }}"
                </div>
                
                @if($entry->file_path)
                <div class="pt-4 border-t border-slate-100 dark:border-white/5">
                    <a href="{{ asset('storage/' . $entry->file_path) }}" target="_blank" class="inline-flex items-center gap-3 px-4 py-2 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl text-xs font-bold hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-all group/file">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white group-hover/file:scale-110 transition-transform">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="line-clamp-1 truncate max-w-[200px]">{{ $entry->file_name }}</span>
                            <span class="text-[9px] opacity-60 uppercase font-black uppercase tracking-widest mt-0.5">Lihat Lampiran</span>
                        </div>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="py-20 text-center">
            <div class="w-20 h-20 bg-slate-500/5 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="clipboard-list" class="w-10 h-10 text-slate-400 opacity-20"></i>
            </div>
            <p class="text-slate-400 font-black uppercase tracking-[0.2em] text-sm italic opacity-50">Belum ada riwayat tindak lanjut</p>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    function updateFileName(input) {
        const label = document.getElementById('file-label');
        if (input.files && input.files[0]) {
            label.textContent = input.files[0].name;
            label.classList.add('text-blue-600');
        } else {
            label.textContent = 'Lampirkan File (Opsional)';
            label.classList.remove('text-blue-600');
        }
    }

    lucide.createIcons();
</script>
@endpush
@endsection
