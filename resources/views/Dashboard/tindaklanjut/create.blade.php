@extends('layouts.app')

@section('title', 'Tambah Surat Masuk')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tindaklanjut.index') }}" class="text-blue-500 hover:text-blue-600 text-sm font-bold flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    <div class="glass p-8 rounded-3xl">
        <h2 class="text-xl font-bold mb-6 flex items-center gap-3">
            <div class="p-2 bg-blue-500/20 rounded-lg">
                <i data-lucide="mail-plus" class="w-6 h-6 text-blue-500"></i>
            </div>
            Tambah Surat Masuk Baru
        </h2>

        <form method="POST" action="{{ route('tindaklanjut.store') }}" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-2">Nomor Surat *</label>
                    <input type="text" name="nomor_surat" value="{{ old('nomor_surat') }}" required
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: 001/SM/2026">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-2">Tanggal Surat *</label>
                    <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', date('Y-m-d')) }}" required
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-2">Dari (Pengirim) *</label>
                    <input type="text" name="pengirim" value="{{ old('pengirim') }}" required
                        class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nama instansi/pengirim">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-2">Tujuan Bidang (Disposisi)</label>
                    <div class="custom-dropdown-container">
                        <div class="status-filter-dropdown dark:bg-white/5 bg-slate-50 h-[48px] border border-slate-200 dark:border-white/10 rounded-xl">
                            <input hidden="" class="sr-only" name="bidang-dropdown" id="bidang-dropdown" type="checkbox" />
                            <label for="bidang-dropdown" class="status-filter-trigger h-full rounded-xl w-full">
                                <span class="text-blue-600 dark:text-blue-400 font-bold text-sm" id="selected-bidang-label">
                                    Belum Ditentukan
                                </span>
                            </label>
                            <ul class="status-filter-list webkit-scrollbar">
                                <li class="status-filter-listitem">
                                    <div onclick="selectBidang('', 'Belum Ditentukan')" class="status-filter-article active">Belum Ditentukan</div>
                                </li>
                                @foreach($bidwasList as $b)
                                <li class="status-filter-listitem">
                                    <div onclick="selectBidang('{{ $b->id_bidwas }}', '{{ $b->nm_bidwas }}')" class="status-filter-article">
                                        {{ $b->nm_bidwas }}
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" name="target_bidang_id" id="bidang_input" value="">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-2">Perihal *</label>
                <textarea name="perihal" rows="3" required
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Isi perihal surat...">{{ old('perihal') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-2">Catatan Awal (opsional)</label>
                <textarea name="catatan" rows="2"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-2">Upload File (opsional)</label>
                <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-blue-500 file:text-white hover:file:bg-blue-600">
                <p class="text-xs text-slate-500 mt-1">Format: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max 10MB)</p>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan
                </button>
                <a href="{{ route('tindaklanjut.index') }}" class="px-6 py-3 bg-slate-200 dark:bg-white/10 hover:bg-slate-300 dark:hover:bg-white/20 rounded-xl font-bold transition-all text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@include('components.custom-dropdown-css')

@push('scripts')
<script>
    function selectBidang(val, label) {
        document.getElementById('bidang_input').value = val;
        document.getElementById('selected-bidang-label').innerText = label;
        document.getElementById('bidang-dropdown').checked = false;
        
        // Update active class
        document.querySelectorAll('.status-filter-article').forEach(el => {
            el.classList.remove('active');
            if (el.innerText.trim() === label.trim()) {
                el.classList.add('active');
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown-container')) {
            document.querySelectorAll('.status-filter-dropdown input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    });

    lucide.createIcons();
</script>
@endpush
