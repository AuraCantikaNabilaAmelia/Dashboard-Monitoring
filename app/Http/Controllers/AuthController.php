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

        $pegawai = DB::table('r_pegawai')->where('nip', $request->nip)->first();

        if (!$pegawai) {
            return back()->withErrors(['nip' => 'NIP tidak ditemukan dalam database kepegawaian']);
        }

        if ($request->password !== $pegawai->nip) {
            return back()->withErrors(['password' => 'Password salah']);
        }

        $role = \App\Models\User::ROLE_PEGAWAI;
        $bidang_id = null;

        if (isset($pegawai->user_role)) {
            $dbRole = strtolower($pegawai->user_role);
            
            // Mapping Bidang ID berdasarkan role/unit di r_pegawai
            if (str_contains($dbRole, 'apd')) $bidang_id = 159;
            elseif (str_contains($dbRole, 'an')) $bidang_id = 160;
            elseif (str_contains($dbRole, 'ipp')) $bidang_id = 158;
            elseif (str_contains($dbRole, 'investigasi')) $bidang_id = 161;
            elseif (str_contains($dbRole, 'p3a') || str_contains($dbRole, 'program')) $bidang_id = 320;
            elseif (str_contains($dbRole, 'keuangan')) $bidang_id = 552;
            elseif (str_contains($dbRole, 'kepegawaian')) $bidang_id = 551;
            elseif (str_contains($dbRole, 'umum')) $bidang_id = 553;
            elseif (str_contains($dbRole, 'bagian tata usaha')) $bidang_id = 157;

            // Mapping Role Aplikasi
            if (
                str_contains($dbRole, 'admin') || 
                str_contains($dbRole, 'pimpinan') || 
                str_contains($dbRole, 'kepala_perwakilan')
            ) {
                $role = \App\Models\User::ROLE_PIMPINAN;
            } 
            elseif (
                str_contains($dbRole, 'korwas') || 
                str_contains($dbRole, 'kabid') || 
                str_contains($dbRole, 'kepala bagian') ||
                str_contains($dbRole, 'subkoor')
            ) {
                $role = \App\Models\User::ROLE_KABID;
            }
        }

        $user = \App\Models\User::updateOrCreate(
            ['nip' => $pegawai->nip],
            [
                'name' => $pegawai->nama,
                'email' => $pegawai->nip . '@bpkp.go.id',
                'password' => bcrypt($pegawai->nip),
                'role' => $role,
                'jabatan' => $pegawai->user_role ?? '-',
                'bidang_id' => $bidang_id,
            ]
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function logout()
    {
        Session::forget('user');
        return redirect('/login');
    }
}
