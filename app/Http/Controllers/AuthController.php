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
        $idBidang = null;

        if (isset($dataPegawai->user_role)) {
            $peranDariDatabase = strtolower($dataPegawai->user_role);

            if (str_contains($peranDariDatabase, 'apd')) $idBidang = 159;
            elseif (str_contains($peranDariDatabase, 'an')) $idBidang = 160;
            elseif (str_contains($peranDariDatabase, 'ipp')) $idBidang = 158;
            elseif (str_contains($peranDariDatabase, 'investigasi')) $idBidang = 161;
            elseif (str_contains($peranDariDatabase, 'p3a') || str_contains($peranDariDatabase, 'program')) $idBidang = 320;
            elseif (str_contains($peranDariDatabase, 'keuangan')) $idBidang = 552;
            elseif (str_contains($peranDariDatabase, 'kepegawaian')) $idBidang = 551;
            elseif (str_contains($peranDariDatabase, 'umum')) $idBidang = 553;
            elseif (str_contains($peranDariDatabase, 'bagian tata usaha')) $idBidang = 157;

            if (
                str_contains($peranDariDatabase, 'admin') ||
                str_contains($peranDariDatabase, 'pimpinan') ||
                str_contains($peranDariDatabase, 'kepala_perwakilan')
            ) {
                $peranUser = \App\Models\User::ROLE_PIMPINAN;
            } elseif (
                str_contains($peranDariDatabase, 'korwas') ||
                str_contains($peranDariDatabase, 'kabid') ||
                str_contains($peranDariDatabase, 'kepala bagian') ||
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

    public function logout()
    {
        Session::forget('user');
        return redirect('/login');
    }
}
