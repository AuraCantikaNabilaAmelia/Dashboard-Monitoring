@extends('layouts.app')

@section('title', 'Detail Surat Masuk')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tindaklanjut.index') }}" class="text-blue-500 hover:text-blue-600 text-sm font-bold flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <div class="glass p-6 rounded-3xl mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start gap-4">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold mb-1">Nomor Surat</p>
                <h2 class="text-2xl font-black">{{ $surat->nomor_surat }}</h2>
            </div>
            <span class="px-4 py-2 bg-{{ $surat->status_color }}-100 dark:bg-{{ $surat->status_color }}-500/20 text-{{ $surat->status_color }}-600 dark:text-{{ $surat->status_color }}-400 text-sm font-bold rounded-full">
                {{ $surat->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold mb-1">Tanggal Surat</p>
                <p class="font-bold">{{ $surat->tanggal_surat->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold mb-1">Dari</p>
                <p class="font-bold">{{ $surat->pengirim }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500 uppercase font-bold mb-1">Kepada / Bidang Tujuan</p>
                @if($surat->target_bidang_id)
                    <p class="font-bold text-blue-600 dark:text-blue-400">{{ $surat->targetBidang->nm_bidwas }}</p>
                @else
                    <p class="font-bold text-slate-400">Belum Ditentukan (Disposisi)</p>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <p class="text-xs text-slate-500 uppercase font-bold mb-1">Perihal</p>
            <p class="text-slate-700 dark:text-slate-300">{{ $surat->perihal }}</p>
        </div>

        @if($surat->catatan)
        <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-500/10 rounded-xl border border-amber-100 dark:border-amber-500/20">
            <p class="text-xs text-amber-600 dark:text-amber-400 uppercase font-bold mb-1">Catatan Awal</p>
            <p class="text-slate-700 dark:text-slate-300">{{ $surat->catatan }}</p>
        </div>
        @endif

        @if($surat->file_path)
        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-500/10 rounded-xl border border-blue-100 dark:border-blue-500/20">
            <p class="text-xs text-blue-600 dark:text-blue-400 uppercase font-bold mb-2">File Lampiran</p>
            <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-bold text-sm transition-all">
                <i data-lucide="file-down" class="w-4 h-4"></i>
                {{ $surat->file_name }}
            </a>
        </div>
        @endif

        <div class="mt-6 flex gap-3 flex-wrap">
            @if($surat->status !== 'keputusan')
                {{-- Tombol Klaim (Jika belum ada bidang yang dituju) --}}
                @if(!$surat->target_bidang_id && auth()->user()->bidang_id)
                <form method="POST" action="{{ route('tindaklanjut.claim', $surat->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all">
                        <i data-lucide="hand" class="w-4 h-4"></i>
                        Klaim untuk Bidang Saya
                    </button>
                </form>
                @endif
            @else
                <form method="POST" action="{{ route('tindaklanjut.reopen', $surat->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Buka Kembali ke Disposisi
                    </button>
                </form>
            @endif

            <form id="delete-surat-form-{{ $surat->id }}" method="POST" action="{{ route('tindaklanjut.destroy', $surat->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" 
                        @click="$store.confirm.ask('Yakin ingin menghapus surat ini beserta semua catatan tindak lanjutnya?', () => document.getElementById('delete-surat-form-{{ $surat->id }}').submit(), 'Hapus Surat', 'Ya, Hapus')"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="glass p-6 rounded-3xl mb-6">
        <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
            <i data-lucide="list-checks" class="w-5 h-5 text-purple-500"></i>
            Catatan Tindak Lanjut ({{ $surat->tindakLanjutEntries->count() }})
        </h3>

        <div class="mb-6">
            @if($surat->status !== 'keputusan')
                @if($surat->target_bidang_id == auth()->user()->bidang_id || auth()->user()->role == \App\Models\User::ROLE_PIMPINAN)
                <a href="{{ route('tindaklanjut.addEntryForm', $surat->id) }}" class="w-full py-4 border-2 border-dashed border-slate-300 dark:border-white/10 rounded-2xl flex items-center justify-center gap-3 text-slate-500 hover:border-blue-500 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/5 transition-all font-bold group">
                    <i data-lucide="plus-circle" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
                    Tambah Catatan Tindak Lanjut
                </a>
                @else
                <div class="p-4 bg-slate-100 dark:bg-white/5 rounded-2xl text-center text-slate-500 text-sm italic">
                    Hanya staf dari bidang terkait yang dapat menambahkan catatan tindak lanjut.
                </div>
                @endif
            @endif
        </div>

        <div class="space-y-6">
            @forelse($surat->tindakLanjutEntries as $entry)
            <div class="p-6 bg-white dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/5 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-2.5 h-2.5 bg-blue-500 rounded-full"></div>
                        <span class="text-base font-bold">{{ $entry->tanggal->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-xs text-slate-400 font-medium">
                            oleh <span class="text-slate-600 dark:text-slate-300 font-bold ml-1">{{ $entry->created_by_nama ?? 'System' }}</span>
                        </span>
                        
                        @if($surat->status !== 'selesai' && $entry->created_by_nip === auth()->user()->nip)
                        <div class="flex items-center gap-2">
                            <a href="{{ route('tindaklanjut.editEntryForm', $entry->id) }}" 
                                    class="p-2 text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-xl transition-all"
                                    title="Edit">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                            
                            <form id="delete-entry-{{ $entry->id }}" action="{{ route('tindaklanjut.deleteEntry', $entry->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        @click="$store.confirm.ask('Apakah Anda yakin ingin menghapus catatan tindak lanjut ini?', () => document.getElementById('delete-entry-{{ $entry->id }}').submit(), 'Hapus Catatan', 'Ya, Hapus')"
                                        class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-all"
                                        title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @if($entry->keterangan)
                <p class="text-slate-700 dark:text-slate-300 pl-6 mb-4 whitespace-pre-line leading-relaxed">{{ $entry->keterangan }}</p>
                @endif
                @if($entry->file_path)
                <div class="pl-6">
                    <a href="{{ asset('storage/' . $entry->file_path) }}" target="_blank" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl text-xs font-bold hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-all border border-blue-100 dark:border-blue-500/20">
                        <i data-lucide="file-down" class="w-4 h-4"></i>
                        {{ $entry->file_name }}
                    </a>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-12 text-slate-500 border-2 border-dashed border-slate-100 dark:border-white/5 rounded-[2rem]">
                <i data-lucide="clipboard-list" class="w-16 h-16 mx-auto text-slate-200 mb-4"></i>
                <p class="font-bold text-lg">Belum ada catatan tindak lanjut.</p>
                <p class="text-sm">Klik tombol di atas untuk menambahkan catatan.</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($surat->status === 'keputusan')
    <div class="glass p-6 rounded-3xl text-center">
        <div class="w-16 h-16 bg-green-100 dark:bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="check-circle-2" class="w-8 h-8 text-green-500"></i>
        </div>
        <h3 class="text-lg font-bold text-green-600 dark:text-green-400">Keputusan Akhir Tercapai</h3>
        <p class="text-slate-500 text-sm mt-1">Proses tindak lanjut telah sampai pada tahap akhir pada {{ $surat->updated_at->format('d M Y H:i') }}.</p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
