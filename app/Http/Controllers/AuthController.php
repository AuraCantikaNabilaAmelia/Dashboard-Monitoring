<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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

        $pegawai = DB::table('r_pegawai')
            ->where('nip', $request->nip)
            ->first();

        if (!$pegawai) {
            return back()->withErrors(['nip' => 'NIP tidak ditemukan']);
        }

        if ($request->password !== $pegawai->nip) {
            return back()->withErrors(['password' => 'Password salah']);
        }

        Session::put('user', [
            'id' => $pegawai->id,
            'nip' => $pegawai->nip,
            'nama' => $pegawai->nama,
            'role' => $pegawai->user_role,
        ]);

        return redirect('/dashboard');
    }

    public function logout()
    {
        Session::forget('user');
        return redirect('/login');
    }
}
