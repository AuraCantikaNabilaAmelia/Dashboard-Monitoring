<?php

namespace App\Http\Controllers;

use App\Models\StTindakLanjut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TindakLanjutController extends Controller
{
    /** Role yang bypass scoping & berhak edit/hapus catatan siapa pun. */
    private const MANAGER_ROLES = ['admin', 'pimpinan', 'kabid'];

    private function isManager(): bool
    {
        return in_array(auth()->user()->role, self::MANAGER_ROLES, true);
    }

    /** Cek apakah NIP user berada di tim ST tertentu. */
    private function userInTim($idSuratTugas): bool
    {
        return DB::table('d_st_tim')
            ->where('id_st', $idSuratTugas)
            ->where('nip', auth()->user()->nip)
            ->exists();
    }

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

        // Scoping: pegawai biasa hanya melihat ST di mana NIP-nya tertera di tim.
        if (!$this->isManager()) {
            $nipPengguna = auth()->user()->nip;
            $querySuratTugas->whereExists(function ($sub) use ($nipPengguna) {
                $sub->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $nipPengguna);
            });
        }

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

        $isManager = $this->isManager();
        $isUserInTim = $this->userInTim($idSuratTugas);

        // Pegawai biasa hanya boleh akses ST yang dia jadi anggota timnya.
        if (!$isManager && !$isUserInTim) {
            abort(403, 'Anda tidak terdaftar sebagai anggota tim penugasan ini.');
        }

        $riwayatTindakLanjut = StTindakLanjut::where('id_st', $idSuratTugas)
            ->orderBy('created_at', 'desc')
            ->get();

        // canAddCatatan = manager bypass, atau pegawai yang ada di tim ST
        $canAddCatatan = $isManager || $isUserInTim;

        return view('Dashboard.tindaklanjut.show', compact(
            'dataSuratTugas',
            'riwayatTindakLanjut',
            'isManager',
            'isUserInTim',
            'canAddCatatan'
        ));
    }

    public function addEntry(Request $request, $idSuratTugas)
    {
        $request->validate([
            'catatan' => 'required|string|max:2000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:20480',
        ], [
            'catatan.max' => 'Catatan tidak boleh lebih dari 2000 karakter.',
        ]);

        $dataSuratTugas = DB::table('d_st')->where('id_st', $idSuratTugas)->first();
        if (!$dataSuratTugas) abort(404);

        if (!$this->isManager() && !$this->userInTim($idSuratTugas)) {
            return back()->with('error', 'Anda hanya dapat menambahkan tindak lanjut untuk penugasan di mana nama Anda tertera dalam tim.');
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

    public function updateEntry(Request $request, $idEntri)
    {
        $request->validate([
            'catatan' => 'required|string|max:2000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:20480',
            'hapus_file' => 'nullable|boolean',
        ], [
            'catatan.max' => 'Catatan tidak boleh lebih dari 2000 karakter.',
        ]);

        $dataTindakLanjut = StTindakLanjut::findOrFail($idEntri);

        $isOwner = $dataTindakLanjut->created_by_nip === auth()->user()->nip;

        if (!$isOwner && !$this->isManager()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengedit catatan ini.');
        }

        $dataTindakLanjut->catatan = $request->catatan;

        if ($request->boolean('hapus_file') && $dataTindakLanjut->file_path) {
            Storage::disk('public')->delete($dataTindakLanjut->file_path);
            $dataTindakLanjut->file_path = null;
            $dataTindakLanjut->file_name = null;
        }

        if ($request->hasFile('file')) {
            if ($dataTindakLanjut->file_path) {
                Storage::disk('public')->delete($dataTindakLanjut->file_path);
            }

            $berkas = $request->file('file');
            $dataTindakLanjut->file_name = $berkas->getClientOriginalName();
            $dataTindakLanjut->file_path = $berkas->store('tindak_lanjut_st', 'public');
        }

        $dataTindakLanjut->save();

        return back()->with('success', 'Catatan tindak lanjut berhasil diperbarui.');
    }

    public function deleteEntry($idEntri)
    {
        $dataTindakLanjut = StTindakLanjut::findOrFail($idEntri);

        $isOwner = $dataTindakLanjut->created_by_nip === auth()->user()->nip;

        if (!$isOwner && !$this->isManager()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus catatan ini.');
        }

        if ($dataTindakLanjut->file_path) {
            Storage::disk('public')->delete($dataTindakLanjut->file_path);
        }

        $dataTindakLanjut->delete();

        return back()->with('success', 'Catatan tindak lanjut berhasil dihapus.');
    }
}
