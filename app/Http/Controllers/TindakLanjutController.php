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
        $kataKunci = $request->search;
        $idBidang = $request->bidang_id;
        $tanggalMulai = $request->start_date;
        $tanggalSelesai = $request->end_date;

        $querySuratTugas = DB::table('d_st')
            ->leftJoin('r_bidwas', 'd_st.id_bidwas', '=', 'r_bidwas.id_bidwas')
            ->select(
                'd_st.*',
                'r_bidwas.nm_bidwas',
                'r_bidwas.kd_bidwas',
                DB::raw('(SELECT count(*) FROM st_tindak_lanjut WHERE st_tindak_lanjut.id_st = d_st.id_st) as entry_count')
            );

        if ($kataKunci) {
            $querySuratTugas->where(function ($query) use ($kataKunci) {
                $query->where('no_surat_tugas', 'like', "%{$kataKunci}%")
                    ->orWhere('nama_penugasan', 'like', "%{$kataKunci}%");
            });
        }

        if ($idBidang) {
            $querySuratTugas->where('d_st.id_bidwas', $idBidang);
        }

        if ($tanggalMulai) {
            $querySuratTugas->where('start_date', '>=', $tanggalMulai);
        }

        if ($tanggalSelesai) {
            $querySuratTugas->where('end_date', '<=', $tanggalSelesai);
        }

        $daftarSuratTugas = $querySuratTugas->orderBy('start_date', 'desc')->paginate(10);
        $daftarBidwas = DB::table('r_bidwas')->get();

        return view('Dashboard.tindaklanjut.index', compact('daftarSuratTugas', 'daftarBidwas', 'kataKunci', 'idBidang', 'tanggalMulai', 'tanggalSelesai'));
    }

    public function show($idSuratTugas)
    {
        $dataSuratTugas = DB::table('d_st')
            ->leftJoin('r_bidwas', 'd_st.id_bidwas', '=', 'r_bidwas.id_bidwas')
            ->where('id_st', $idSuratTugas)
            ->first();

        if (!$dataSuratTugas) abort(404);

        $riwayatTindakLanjut = StTindakLanjut::where('id_st', $idSuratTugas)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Dashboard.tindaklanjut.show', compact('dataSuratTugas', 'riwayatTindakLanjut'));
    }

    public function addEntry(Request $request, $idSuratTugas)
    {
        $request->validate([
            'catatan' => 'required',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:20480',
        ]);

        $dataSuratTugas = DB::table('d_st')->where('id_st', $idSuratTugas)->first();
        if (!$dataSuratTugas) abort(404);

        if (auth()->user()->role !== 'pimpinan' && auth()->user()->bidang_id != $dataSuratTugas->id_bidwas) {
            return back()->with('error', 'Anda hanya dapat menambahkan tindak lanjut untuk bidang Anda sendiri.');
        }

        $jalurFile = null;
        $namaFileOriginal = null;

        if ($request->hasFile('file')) {
            $berkas = $request->file('file');
            $namaFileOriginal = $berkas->getClientOriginalName();
            $jalurFile = $berkas->store('tindak_lanjut_st', 'public');
        }

        StTindakLanjut::create([
            'id_st' => $idSuratTugas,
            'catatan' => $request->catatan,
            'file_path' => $jalurFile,
            'file_name' => $namaFileOriginal,
            'created_by_nip' => auth()->user()->nip,
            'created_by_nama' => auth()->user()->name,
        ]);

        return back()->with('success', 'Catatan tindak lanjut berhasil ditambahkan.');
    }

    public function deleteEntry($idEntri)
    {
        $dataTindakLanjut = StTindakLanjut::findOrFail($idEntri);

        if ($dataTindakLanjut->created_by_nip !== auth()->user()->nip) {
            return back()->with('error', 'Anda hanya dapat menghapus catatan yang Anda buat sendiri.');
        }

        if ($dataTindakLanjut->file_path) {
            Storage::disk('public')->delete($dataTindakLanjut->file_path);
        }

        $dataTindakLanjut->delete();

        return back()->with('success', 'Catatan tindak lanjut berhasil dihapus.');
    }
}
