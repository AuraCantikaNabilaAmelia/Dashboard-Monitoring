@extends('layouts.app')

@section('title', ($mode === 'create' ? 'Tambah' : 'Edit') . ' Tindak Lanjut')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('tindaklanjut.show', $surat->id) }}" class="text-blue-500 hover:text-blue-600 text-sm font-bold flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Detail Surat
        </a>
    </div>

    <div class="glass p-8 rounded-[2rem] shadow-2xl">
        <div class="flex justify-between items-center mb-10 border-b border-slate-100 dark:border-white/5 pb-6">
            <div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">
                    {{ $mode === 'create' ? 'Tambah Tindak Lanjut' : 'Edit Tindak Lanjut' }}
                </h3>
                <p class="text-slate-500 text-sm mt-1">Surat: {{ $surat->nomor_surat }}</p>
            </div>
            <div class="p-3 bg-blue-100 dark:bg-blue-500/20 rounded-2xl text-blue-500">
                <i data-lucide="file-text" class="w-8 h-8"></i>
            </div>
        </div>

        <form method="POST" action="{{ $mode === 'create' ? route('tindaklanjut.addEntry', $surat->id) : route('tindaklanjut.updateEntry', $entry->id) }}" enctype="multipart/form-data">
            @csrf
            @if($mode === 'edit')
                @method('PUT')
            @endif

            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal" value="{{ $mode === 'create' ? date('Y-m-d') : $entry->tanggal->format('Y-m-d') }}" required
                            class="w-full px-5 py-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        @error('tanggal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Update Tahapan Alur</label>
                        <div class="custom-dropdown-container">
                            <div class="status-filter-dropdown dark:bg-white/5 bg-slate-50 h-[56px] border border-slate-200 dark:border-white/10 rounded-2xl">
                                <input hidden="" class="sr-only" name="status-dropdown" id="status-dropdown" type="checkbox" />
                                <label for="status-dropdown" class="status-filter-trigger h-full rounded-2xl w-full">
                                    <span class="text-blue-600 dark:text-blue-400 font-bold text-sm" id="selected-status-label">
                                        {{ \App\Models\SuratMasuk::statusLabels()[$surat->status] }}
                                    </span>
                                </label>
                                <ul class="status-filter-list">
                                    @foreach(\App\Models\SuratMasuk::statusLabels() as $val => $label)
                                    <li class="status-filter-listitem">
                                        <div onclick="selectStatus('{{ $val }}', '{{ $label }}')" class="status-filter-article {{ $surat->status === $val ? 'active' : '' }}">
                                            {{ $label }}
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="status" id="status_input" value="{{ $surat->status }}">
                        <p class="text-[10px] text-slate-400 mt-2">Opsional: Update status surat saat ini</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Upload File (opsional)</label>
                        <div class="relative">
                            <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                class="w-full px-5 py-4 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-500 file:text-white hover:file:bg-blue-600 transition-all">
                        </div>
                        <div class="flex justify-between mt-2">
                            <p class="text-[10px] text-slate-400">PDF, Excel, Word (Max 10MB)</p>
                            @if($mode === 'edit' && $entry->file_path)
                                <p class="text-[10px] text-amber-500">File: {{ $entry->file_name }}</p>
                            @endif
                        </div>
                        @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Keterangan / Progress Tindak Lanjut</label>
                    <textarea name="keterangan" rows="10" placeholder="Berikan penjelasan detail mengenai tindak lanjut yang dilakukan..."
                        class="w-full px-6 py-5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-[1.5rem] text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">{{ $mode === 'create' ? old('keterangan') : $entry->keterangan }}</textarea>
                    @error('keterangan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-4 pt-6 border-t border-slate-100 dark:border-white/5">
                    <a href="{{ route('tindaklanjut.show', $surat->id) }}" class="flex-1 py-4 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-700 dark:text-slate-300 rounded-2xl font-bold text-sm text-center transition-all">
                        Batal
                    </a>
                    <button type="submit" class="flex-[2] py-4 bg-blue-500 hover:bg-blue-600 text-white rounded-2xl font-bold text-sm transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-500/25">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        {{ $mode === 'create' ? 'Simpan Tindak Lanjut' : 'Update Tindak Lanjut' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@include('components.custom-dropdown-css')

@push('scripts')
<script>
    function selectStatus(val, label) {
        document.getElementById('status_input').value = val;
        document.getElementById('selected-status-label').innerText = label;
        document.getElementById('status-dropdown').checked = false;
        
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
