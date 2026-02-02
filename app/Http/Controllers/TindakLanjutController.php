<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\TindakLanjutEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TindakLanjutController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $bidang_id = $request->get('bidang_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = SuratMasuk::with(['tindakLanjutEntries', 'targetBidang'])->withCount('tindakLanjutEntries');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('pengirim', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($bidang_id) {
            $query->where('target_bidang_id', $bidang_id);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_surat', [$startDate, $endDate]);
        }

        // Access Control Logic
        /* SCOPING_BY_BIDANG: Temporarily disabled by user request
        $user = auth()->user();
        if ($user->role !== \App\Models\User::ROLE_PIMPINAN) {
            // Jika bukan pimpinan/admin, hanya bisa melihat surat yang ditujukan ke bidangnya
            // atau surat yang belum ditentukan bidangnya (untuk diklaim/disposisi)
            if ($user->bidang_id) {
                $query->where(function($q) use ($user) {
                    $q->where('target_bidang_id', $user->bidang_id)
                      ->orWhereNull('target_bidang_id');
                });
            }
        }
        */

        $suratList = $query->orderBy('tanggal_surat', 'desc')->paginate(15);

        $statusSummary = SuratMasuk::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statuses = array_keys(SuratMasuk::statusLabels());
        $bidwasList = \App\Models\Bidwas::all();

        return view('dashboard.tindaklanjut.index', compact('suratList', 'search', 'status', 'statuses', 'statusSummary', 'bidang_id', 'bidwasList', 'startDate', 'endDate'));
    }

    public function create()
    {
        $bidwasList = \App\Models\Bidwas::all();
        return view('dashboard.tindaklanjut.create', compact('bidwasList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100',
            'tanggal_surat' => 'required|date',
            'perihal' => 'required|string',
            'pengirim' => 'required|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'target_bidang_id' => 'nullable|exists:r_bidwas,id_bidwas',
            'catatan' => 'nullable|string',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        $validated['status'] = $request->target_bidang_id ? 'disposisi' : 'surat_masuk';

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('surat_masuk', $fileName, 'public');
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
        }
        unset($validated['file']);

        SuratMasuk::create($validated);

        return redirect('/dashboard/tindaklanjut')->with('success', 'Surat Masuk berhasil ditambahkan.');
    }

    public function show($id)
    {
        $surat = SuratMasuk::with('tindakLanjutEntries')->findOrFail($id);

        return view('dashboard.tindaklanjut.show', compact('surat'));
    }

    public function addEntryForm($id)
    {
        $surat = SuratMasuk::findOrFail($id);
        
        return view('dashboard.tindaklanjut.entry_form', [
            'surat' => $surat,
            'mode' => 'create',
            'entry' => null
        ]);
    }

    public function addEntry(Request $request, $id)
    {
        $surat = SuratMasuk::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        $user = auth()->user();

        $entryData = [
            'surat_masuk_id' => $surat->id,
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'] ?? null,
            'created_by_nip' => $user->nip ?? null,
            'created_by_nama' => $user->name ?? 'System',
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('tindak_lanjut', $fileName, 'public');
            $entryData['file_path'] = $filePath;
            $entryData['file_name'] = $file->getClientOriginalName();
        }

        TindakLanjutEntry::create($entryData);

        // Status update logic can be manual by the user or automatic for the first step
        if ($surat->status === 'surat_masuk') {
            $surat->update(['status' => 'disposisi']);
        }

        if ($request->status) {
            $surat->update(['status' => $request->status]);
        }

        return redirect()->route('tindaklanjut.show', $id)->with('success', 'Tindak lanjut berhasil ditambahkan.');
    }

    public function editEntryForm($entry_id)
    {
        $entry = TindakLanjutEntry::findOrFail($entry_id);
        $surat = $entry->suratMasuk;

        if ($entry->created_by_nip !== auth()->user()->nip) {
            return redirect()->back()->with('error', 'Anda hanya dapat mengubah catatan yang Anda buat sendiri.');
        }

        return view('dashboard.tindaklanjut.entry_form', [
            'surat' => $surat,
            'mode' => 'edit',
            'entry' => $entry
        ]);
    }

    public function updateEntry(Request $request, $id)
    {
        $entry = TindakLanjutEntry::findOrFail($id);
        
        if ($entry->created_by_nip !== auth()->user()->nip) {
            return redirect()->back()->with('error', 'Anda hanya dapat mengubah catatan yang Anda buat sendiri.');
        }
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        $entryData = [
            'tanggal' => $validated['tanggal'],
            'keterangan' => $validated['keterangan'] ?? null,
        ];

        if ($request->hasFile('file')) {
            if ($entry->file_path && \Storage::disk('public')->exists($entry->file_path)) {
                \Storage::disk('public')->delete($entry->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('tindak_lanjut', $fileName, 'public');
            $entryData['file_path'] = $filePath;
            $entryData['file_name'] = $file->getClientOriginalName();
        }

        $entry->update($entryData);

        if ($request->status) {
            $entry->suratMasuk->update(['status' => $request->status]);
        }

        return redirect()->route('tindaklanjut.show', $entry->surat_masuk_id)->with('success', 'Tindak lanjut berhasil diperbarui.');
    }

    public function deleteEntry($id)
    {
        $entry = TindakLanjutEntry::findOrFail($id);
        
        if ($entry->created_by_nip !== auth()->user()->nip) {
            return redirect()->back()->with('error', 'Anda hanya dapat menghapus catatan yang Anda buat sendiri.');
        }
        
        if ($entry->file_path && \Storage::disk('public')->exists($entry->file_path)) {
            \Storage::disk('public')->delete($entry->file_path);
        }

        $entry->delete();

        return redirect()->back()->with('success', 'Tindak lanjut berhasil dihapus.');
    }

    public function markSelesai($id)
    {
        $surat = SuratMasuk::findOrFail($id);
        $surat->update(['status' => 'keputusan']);

        return redirect()->back()->with('success', 'Surat telah ditandai selesai.');
    }

    public function reopen($id)
    {
        $surat = SuratMasuk::findOrFail($id);
        
        // Reopen defaults to first stage or based on context
        $newStatus = $surat->target_bidang_id ? 'disposisi' : 'surat_masuk';
        
        $surat->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Surat telah dibuka kembali.');
    }

    public function claim(Request $request, $id)
    {
        $surat = SuratMasuk::findOrFail($id);
        $user = auth()->user();

        if (!$user->bidang_id) {
            return redirect()->back()->with('error', 'User Anda belum terdaftar di bidang manapun.');
        }

        $surat->update([
            'target_bidang_id' => $user->bidang_id,
            'status' => 'disposisi'
        ]);

        return redirect()->route('tindaklanjut.show', $id)->with('success', 'Surat berhasil diklaim ke bidang Anda.');
    }

    public function updateStatus(Request $request, $id)
    {
        $surat = SuratMasuk::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', array_keys(SuratMasuk::statusLabels())),
        ]);

        $surat->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Tahapan alur berhasil diperbarui menjadi: ' . $surat->status_label);
    }

    public function destroy($id)
    {
        $surat = SuratMasuk::findOrFail($id);
        $surat->delete();

        return redirect('/dashboard/tindaklanjut')->with('success', 'Surat berhasil dihapus.');
    }
}
