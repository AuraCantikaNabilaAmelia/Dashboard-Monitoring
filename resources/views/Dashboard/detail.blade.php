@extends('layouts.app')

@section('title', 'Detail Surat Tugas')

@section('content')
@if(session('success'))
<div id="toast-sukses" class="fixed bottom-6 right-6 z-[9999] flex items-center gap-3 bg-emerald-600 text-white px-5 py-3.5 rounded-2xl shadow-2xl shadow-emerald-500/30 font-bold text-sm">
    <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
    {{ session('success') }}
</div>
<script>setTimeout(() => { const t = document.getElementById('toast-sukses'); if(t) t.remove(); }, 3500);</script>
@endif

<div class="mb-6">
    <a href="{{ url()->previous() }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 flex items-center gap-2 text-sm font-bold transition-colors w-fit">
        <i data-lucide="chevron-left" class="w-4 h-4"></i>
        <span>Kembali</span>
    </a>
</div>

<div class="space-y-8">
        <div class="glass p-8 rounded-3xl shadow-xl">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="px-3 py-1 bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-black rounded-lg uppercase tracking-widest mb-2 inline-block border border-blue-500/10">Surat Tugas</span>
                    <h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $suratTugas->nama_penugasan }}</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-1 font-mono text-xs font-bold">ID: #{{ $suratTugas->id_st }}</p>
                </div>
                <div class="text-right">
                    @php
                        $statusClass = match(strtolower($suratTugas->status_st)) {
                            'batal' => 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20',
                            'final', 'selesai' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                            'Tidak Aktif' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
                            default => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20'
                        };
                        $statusIcon = match(strtolower($suratTugas->status_st)) {
                            'batal' => 'x-circle',
                            'final', 'selesai' => 'check-circle',
                            'Tidak Aktif' => 'minus-circle',
                            default => 'clock'
                        };
                    @endphp
                    <span class="px-4 py-2 {{ $statusClass }} text-sm font-black rounded-xl border shadow-sm inline-flex items-center gap-2">
                        <i data-lucide="{{ $statusIcon }}" class="w-4 h-4"></i>
                        {{ $suratTugas->status_st }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 dark:border-white/5 pt-8">
                <div class="space-y-6">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">Tanggal Mulai</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $suratTugas->start_date ? date('d F Y', strtotime($suratTugas->start_date)) : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">Nomor Surat Tugas</p>
                        <p class="text-slate-700 dark:text-slate-300 font-bold font-mono">{{ $suratTugas->no_surat_tugas ?? '-' }}</p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">Tanggal Selesai</p>
                        <p class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ $suratTugas->end_date ? date('d F Y', strtotime($suratTugas->end_date)) : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-[0.2em] font-black mb-1">ID Penugasan</p>
                        <p class="text-slate-700 dark:text-slate-300 font-bold font-mono">ST-{{ $suratTugas->id_st }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass p-8 rounded-3xl border-emerald-500/30 shadow-lg shadow-emerald-500/5">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-3">
                    <div class="p-2 bg-emerald-500/20 rounded-lg"><i data-lucide="file-check" class="w-6 h-6"></i></div>
                    Laporan Hasil Pengawasan (LHP)
                </h3>
                @if($bisaTambahLHP)
                <button onclick="document.getElementById('modal-buat-lhp').classList.remove('hidden')" class="h-8 px-3 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-bold border border-emerald-500/20 flex items-center gap-1.5 transition-all">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah LHP
                </button>
                @endif
            </div>
            @forelse($lhpList as $lhp)
            <div class="flex items-start justify-between p-5 bg-slate-100/50 dark:bg-white/5 rounded-2xl border border-slate-200/50 dark:border-white/5 mb-3 last:mb-0">
                <div class="space-y-1">
                    <p class="text-sm font-black text-slate-800 dark:text-white font-mono">{{ $lhp->nomor_lhp ?? '(No. belum ada)' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $lhp->judul_lhp ?? '-' }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-[10px] font-bold text-slate-500">{{ \Carbon\Carbon::parse($lhp->tanggal_lhp)->format('d M Y') }}</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400">{{ $lhp->status_lhp }}</span>
                    </div>
                </div>
                @if($bisaEditST || auth()->user()->role === 'pimpinan')
                <div class="flex gap-2 shrink-0 ml-4">
                    @if($bisaEditST)
                    <button onclick="openModalEditLHP({{ $lhp->id_lhp }}, '{{ addslashes($lhp->nomor_lhp) }}', '{{ addslashes($lhp->judul_lhp ?? '') }}', '{{ $lhp->tanggal_lhp }}', '{{ $lhp->status_lhp }}')"
                        class="w-7 h-7 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center border border-amber-200 dark:border-amber-500/20 hover:bg-amber-100 transition-all" title="Edit LHP">
                        <i data-lucide="pencil" class="w-3 h-3"></i>
                    </button>
                    @endif
                    @if(auth()->user()->role === 'pimpinan')
                    <form method="POST" action="{{ route('lhp.destroy', $lhp->id_lhp) }}" onsubmit="return confirm('Yakin hapus LHP ini?')">
                        @csrf @method('DELETE')
                        <input type="hidden" name="redirect_to" value="detail">
                        <button type="submit" class="w-7 h-7 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center border border-red-200 dark:border-red-500/20 hover:bg-red-100 transition-all" title="Hapus LHP">
                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                        </button>
                    </form>
                    @endif
                </div>
                @endif
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <i data-lucide="file-x" class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-3"></i>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Belum ada LHP untuk penugasan ini</p>
                @if($bisaTambahLHP)
                <button onclick="document.getElementById('modal-buat-lhp').classList.remove('hidden')" class="mt-3 h-8 px-4 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-bold border border-emerald-500/20 transition-all">
                    + Buat LHP Sekarang
                </button>
                @endif
            </div>
            @endforelse
        </div>

        <div class="glass p-8 rounded-3xl shadow-xl">
            <h3 class="text-xl font-black mb-6 flex items-center gap-3">
                <i data-lucide="users" class="w-6 h-6 text-blue-500"></i>
                Tim Penugasan
            </h3>
            <div class="grid grid-cols-1 gap-4">
                @foreach($timPenugasan as $anggota)
                <div class="flex flex-col md:flex-row md:items-center justify-between p-6 rounded-2xl bg-slate-50/50 dark:bg-white/5 border border-slate-100 dark:border-white/5 group hover:bg-slate-100 dark:hover:bg-white/10 transition-all gap-4">
                    <div class="flex items-center space-x-5 min-w-0">
                        <div class="w-12 h-12 bg-blue-600/10 dark:bg-slate-800 rounded-2xl flex items-center justify-center font-black text-blue-700 dark:text-slate-400 border border-blue-500/10 flex-shrink-0">
                            {{ substr($anggota->nama, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-black text-slate-800 dark:text-white text-lg group-hover:text-blue-600 transition-colors truncate" title="{{ $anggota->nama }}">{{ $anggota->nama }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-500 font-bold font-mono">{{ $anggota->nip }}</p>
                        </div>
                    </div>
                    <div class="flex-1 md:text-right">
                        <span class="inline-block text-[10px] font-black text-blue-700 dark:text-blue-400 bg-blue-500/10 dark:bg-blue-500/20 px-4 py-2 rounded-xl uppercase tracking-tighter border border-blue-500/10 leading-relaxed md:max-w-[400px]">
                            {{ $anggota->peran }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
</div>
{{-- Modal Buat LHP --}}
@if($bisaTambahLHP)
<div id="modal-buat-lhp" class="fixed inset-0 z-[9990] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-white/10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-white/10">
                <h2 id="modal-lhp-title" class="text-base font-black text-slate-800 dark:text-white">Buat LHP</h2>
                <button onclick="document.getElementById('modal-buat-lhp').classList.add('hidden')" class="w-8 h-8 bg-slate-100 dark:bg-white/10 rounded-lg flex items-center justify-center text-slate-500 hover:text-red-500 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="form-lhp-detail" method="POST" action="{{ route('lhp.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="_method" id="lhp-detail-method" value="POST">
                <input type="hidden" name="id_st" value="{{ $suratTugas->id_st }}">
                <input type="hidden" name="redirect_to" value="detail">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Nomor LHP <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_lhp" id="lhp-d-nomor" required
                        class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Judul LHP</label>
                    <input type="text" name="judul_lhp" id="lhp-d-judul"
                        class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Tanggal LHP <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_lhp" id="lhp-d-tanggal" required
                            class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Status LHP <span class="text-red-500">*</span></label>
                        <select name="status_lhp" id="lhp-d-status" required
                            class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-medium">
                            <option value="Konsep">Konsep</option>
                            <option value="Review Dalnis">Review Dalnis</option>
                            <option value="Final">Final</option>
                            <option value="FINAL">FINAL</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-buat-lhp').classList.add('hidden')" class="h-10 px-5 bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold hover:bg-slate-200 dark:hover:bg-white/20 transition-all">Batal</button>
                    <button type="submit" class="h-10 px-6 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/25 hover:from-emerald-700 hover:to-emerald-600 transition-all">Simpan LHP</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Edit LHP (dari halaman detail) --}}
@if($bisaEditST)
<div id="modal-edit-lhp-detail" class="fixed inset-0 z-[9990] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-white/10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-white/10">
                <h2 class="text-base font-black text-slate-800 dark:text-white">Edit LHP</h2>
                <button onclick="document.getElementById('modal-edit-lhp-detail').classList.add('hidden')" class="w-8 h-8 bg-slate-100 dark:bg-white/10 rounded-lg flex items-center justify-center text-slate-500 hover:text-red-500 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="form-edit-lhp-detail" method="POST" action="" class="px-6 py-5 space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="id_st" value="{{ $suratTugas->id_st }}">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Nomor LHP <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_lhp" id="edit-lhp-d-nomor" required
                        class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Judul LHP</label>
                    <input type="text" name="judul_lhp" id="edit-lhp-d-judul"
                        class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Tanggal LHP <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_lhp" id="edit-lhp-d-tanggal" required
                            class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Status LHP <span class="text-red-500">*</span></label>
                        <select name="status_lhp" id="edit-lhp-d-status" required
                            class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                            <option value="Konsep">Konsep</option>
                            <option value="Review Dalnis">Review Dalnis</option>
                            <option value="Final">Final</option>
                            <option value="FINAL">FINAL</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-lhp-detail').classList.add('hidden')" class="h-10 px-5 bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold hover:bg-slate-200 dark:hover:bg-white/20 transition-all">Batal</button>
                    <button type="submit" class="h-10 px-6 bg-gradient-to-r from-amber-600 to-amber-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-amber-500/25 hover:from-amber-700 hover:to-amber-600 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit ST dari halaman detail --}}
<div id="modal-edit-st-detail" class="fixed inset-0 z-[9990] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModalEditDetail()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-white/10 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-white/10 sticky top-0 bg-white dark:bg-slate-900 z-10">
                <h2 class="text-base font-black text-slate-800 dark:text-white">Edit Surat Tugas</h2>
                <button onclick="closeModalEditDetail()" class="w-8 h-8 bg-slate-100 dark:bg-white/10 rounded-lg flex items-center justify-center text-slate-500 hover:text-red-500 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('st.update', $suratTugas->id_st) }}" class="px-6 py-5 space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="redirect_to" value="detail">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Nama Penugasan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_penugasan" value="{{ $suratTugas->nama_penugasan }}" required
                        class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">No. Surat Tugas</label>
                    <input type="text" name="no_surat_tugas" value="{{ $suratTugas->no_surat_tugas }}"
                        class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                </div>
                @if(auth()->user()->role === 'pimpinan')
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Bidang <span class="text-red-500">*</span></label>
                    <select name="id_bidwas" required
                        class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                        @foreach($daftarBidwas as $bw)
                        <option value="{{ $bw->id_bidwas }}" {{ $bw->id_bidwas == $suratTugas->id_bidwas ? 'selected' : '' }}>{{ $bw->nm_bidwas }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="id_bidwas" value="{{ $suratTugas->id_bidwas }}">
                @endif
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status_st" required
                        class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                        @foreach(['Konsep','Realisasi','Perpanjangan ST','Final','Batal'] as $s)
                        <option value="{{ $s }}" {{ $suratTugas->status_st === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ $suratTugas->start_date }}" required
                            class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" value="{{ $suratTugas->end_date }}" required
                            class="w-full h-11 px-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-medium">
                    </div>
                </div>
                {{-- Anggota Tim --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Anggota Tim</label>
                        <button type="button" onclick="tambahBarisTimDetail()" class="flex items-center gap-1.5 text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-700 transition-colors">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Anggota
                        </button>
                    </div>
                    <div id="container-tim-detail" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        @foreach($timPenugasan as $anggota)
                        <div class="flex gap-2 items-center">
                            <select name="nip[]" class="flex-1 h-9 px-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-medium">
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($daftarPegawai as $p)
                                <option value="{{ $p->nip }}" {{ $p->nip === $anggota->nip ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->nip }})</option>
                                @endforeach
                            </select>
                            <input type="text" name="peran[]" value="{{ $anggota->peran }}" placeholder="Peran"
                                class="w-36 h-9 px-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-medium">
                            <button type="button" onclick="this.parentElement.remove()" class="w-8 h-8 bg-red-50 dark:bg-red-500/10 text-red-500 rounded-lg flex items-center justify-center border border-red-200 dark:border-red-500/20 hover:bg-red-100 transition-all shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModalEditDetail()" class="h-10 px-5 bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold hover:bg-slate-200 dark:hover:bg-white/20 transition-all">Batal</button>
                    <button type="submit" class="h-10 px-6 bg-gradient-to-r from-amber-600 to-amber-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-amber-500/25 hover:from-amber-700 hover:to-amber-600 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    const pegawaiDetailOptions = @json($daftarPegawai);

    function tambahBarisTimDetail(nipVal = '', peranVal = '') {
        const container = document.getElementById('container-tim-detail');
        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center';
        let opts = '<option value="">-- Pilih Pegawai --</option>';
        pegawaiDetailOptions.forEach(p => {
            opts += `<option value="${p.nip}" ${p.nip === nipVal ? 'selected' : ''}>${p.nama} (${p.nip})</option>`;
        });
        div.innerHTML = `
            <select name="nip[]" class="flex-1 h-9 px-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-medium">${opts}</select>
            <input type="text" name="peran[]" value="${peranVal}" placeholder="Peran (cth: Ketua Tim)"
                class="w-36 h-9 px-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg text-xs text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-medium">
            <button type="button" onclick="this.parentElement.remove()" class="w-8 h-8 bg-red-50 dark:bg-red-500/10 text-red-500 rounded-lg flex items-center justify-center border border-red-200 dark:border-red-500/20 hover:bg-red-100 transition-all shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>`;
        container.appendChild(div);
    }

    function openModalEditDetail() {
        document.getElementById('modal-edit-st-detail').classList.remove('hidden');
    }

    function closeModalEditDetail() {
        document.getElementById('modal-edit-st-detail').classList.add('hidden');
    }

    function openModalEditLHP(id, nomor, judul, tanggal, status) {
        document.getElementById('form-edit-lhp-detail').action = '/dashboard/lhp/' + id;
        document.getElementById('edit-lhp-d-nomor').value = nomor;
        document.getElementById('edit-lhp-d-judul').value = judul;
        document.getElementById('edit-lhp-d-tanggal').value = tanggal;
        document.getElementById('edit-lhp-d-status').value = status;
        document.getElementById('modal-edit-lhp-detail').classList.remove('hidden');
    }

    // Auto-fill Nomor & Judul LHP saat modal Buat LHP dibuka
    const stIdLhp   = '{{ $suratTugas->id_st }}';
    const stNamaLhp = @json($suratTugas->nama_penugasan);
    const tahunLhp  = new Date().getFullYear();

    document.querySelectorAll('[onclick*="modal-buat-lhp"]').forEach(btn => {
        btn.addEventListener('click', () => {
            setTimeout(() => {
                const nomorEl = document.getElementById('lhp-d-nomor');
                const judulEl = document.getElementById('lhp-d-judul');
                if (nomorEl && !nomorEl.value) nomorEl.value = `LHP/${stIdLhp}/PW10/${tahunLhp}`;
                if (judulEl && !judulEl.value) judulEl.value = `Laporan Hasil Pengawasan — ${stNamaLhp}`;
            }, 50);
        });
    });

    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>
@endpush

@endsection
