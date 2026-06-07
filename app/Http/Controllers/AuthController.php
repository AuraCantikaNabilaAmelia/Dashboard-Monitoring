<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required',
            'password' => 'required',
        ]);

        $dataPegawai = DB::table('r_pegawai')->where('nip', $request->nip)->first();

        if (!$dataPegawai) {
            return back()->withErrors(['nip' => 'NIP tidak ditemukan dalam database kepegawaian']);
        }

        if ($request->password !== $dataPegawai->nip) {
            return back()->withErrors(['password' => 'Password salah']);
        }

        $peranUser = \App\Models\User::ROLE_PEGAWAI;

        // Ambil bidang_id langsung dari r_pegawai (sudah benar untuk semua role termasuk staff)
        $idBidang = $dataPegawai->id_bidwas ?? null;

        if (isset($dataPegawai->user_role)) {
            $peranDariDatabase = strtolower(trim($dataPegawai->user_role));

            // Role super-special (akses semua divisi): kepala perwakilan & kepala bagian umum
            $rolePimpinan = ['kepala_perwakilan', 'kepala_bagian_umum', 'pimpinan', 'admin'];

            if (in_array($peranDariDatabase, $rolePimpinan, true) || str_contains($peranDariDatabase, 'pimpinan')) {
                $peranUser = \App\Models\User::ROLE_PIMPINAN;
            } elseif (
                str_contains($peranDariDatabase, 'korwas') ||
                str_contains($peranDariDatabase, 'kabid') ||
                str_contains($peranDariDatabase, 'subkoor')
            ) {
                $peranUser = \App\Models\User::ROLE_KABID;
            }
        }

        $dataUser = \App\Models\User::updateOrCreate(
            ['nip' => $dataPegawai->nip],
            [
                'name' => $dataPegawai->nama,
                'email' => $dataPegawai->nip . '@bpkp.go.id',
                'password' => bcrypt($dataPegawai->nip),
                'role' => $peranUser,
                'jabatan' => $dataPegawai->user_role ?? '-',
                'bidang_id' => $idBidang,
            ]
        );

        Auth::login($dataUser);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
