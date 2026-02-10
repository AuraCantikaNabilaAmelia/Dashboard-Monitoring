<?php

namespace App\Http\Controllers;

use App\Models\StTindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TindakLanjutController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $bidang_id = $request->bidang_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = DB::table('d_st')
            ->leftJoin('r_bidwas', 'd_st.id_bidwas', '=', 'r_bidwas.id_bidwas')
            ->select(
                'd_st.*',
                'r_bidwas.nm_bidwas',
                'r_bidwas.kd_bidwas',
                DB::raw('(SELECT count(*) FROM st_tindak_lanjut WHERE st_tindak_lanjut.id_st = d_st.id_st) as entry_count')
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_tugas', 'like', "%{$search}%")
                    ->orWhere('nama_penugasan', 'like', "%{$search}%");
            });
        }

        if ($bidang_id) {
            $query->where('d_st.id_bidwas', $bidang_id);
        }

        if ($startDate) {
            $query->where('start_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('end_date', '<=', $endDate);
        }

        $stList = $query->orderBy('start_date', 'desc')->paginate(10);
        $bidwasList = DB::table('r_bidwas')->get();

        return view('Dashboard.tindaklanjut.index', compact('stList', 'bidwasList', 'search', 'bidang_id', 'startDate', 'endDate'));
    }

    public function show($id)
    {
        $st = DB::table('d_st')
            ->leftJoin('r_bidwas', 'd_st.id_bidwas', '=', 'r_bidwas.id_bidwas')
            ->where('id_st', $id)
            ->first();

        if (!$st) abort(404);

        $entries = StTindakLanjut::where('id_st', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Dashboard.tindaklanjut.show', compact('st', 'entries'));
    }

    public function addEntry(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:20480',
        ]);

        $st = DB::table('d_st')->where('id_st', $id)->first();
        if (!$st) abort(404);

        if (auth()->user()->role !== 'pimpinan' && auth()->user()->bidang_id != $st->id_bidwas) {
            return back()->with('error', 'Anda hanya dapat menambahkan tindak lanjut untuk bidang Anda sendiri.');
        }

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->store('tindak_lanjut_st', 'public');
        }

        StTindakLanjut::create([
            'id_st' => $id,
            'catatan' => $request->catatan,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'created_by_nip' => auth()->user()->nip,
            'created_by_nama' => auth()->user()->name,
        ]);

        return back()->with('success', 'Catatan tindak lanjut berhasil ditambahkan.');
    }

    public function deleteEntry($id)
    {
        $entry = StTindakLanjut::findOrFail($id);

        if ($entry->created_by_nip !== auth()->user()->nip) {
            return back()->with('error', 'Anda hanya dapat menghapus catatan yang Anda buat sendiri.');
        }

        if ($entry->file_path) {
            Storage::disk('public')->delete($entry->file_path);
        }

        $entry->delete();

        return back()->with('success', 'Catatan tindak lanjut berhasil dihapus.');
    }
}
