<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Tambahkan semua staf (role pegawai) yang belum punya ST aktif ke dummy ST
 * sesuai bidangnya masing-masing (round-robin), agar halaman Harian & Overview
 * selalu terisi data untuk setiap akun staf.
 */
class DummyStafTimSeeder extends Seeder
{
    public function run(): void
    {
        $today = now()->toDateString();

        // Dummy ST aktif per bidang (sudah diperpanjang ke 2026-12-31)
        $stPerBidang = DB::table('d_st')
            ->where(function ($q) {
                $q->where('no_surat_tugas', 'like', 'ST-AKTIF-%')
                  ->orWhere('no_surat_tugas', 'like', 'ST-KBD-%');
            })
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereIn('status_st', ['Realisasi', 'Perpanjangan ST'])
            ->pluck('id_st', 'id_bidwas')
            ->groupBy(fn ($idSt, $idBidwas) => $idBidwas);

        // Rebuild sebagai [id_bidwas => [id_st, ...]]
        $stMap = [];
        DB::table('d_st')
            ->where(function ($q) {
                $q->where('no_surat_tugas', 'like', 'ST-AKTIF-%')
                  ->orWhere('no_surat_tugas', 'like', 'ST-KBD-%');
            })
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereIn('status_st', ['Realisasi', 'Perpanjangan ST'])
            ->get(['id_st', 'id_bidwas'])
            ->each(function ($row) use (&$stMap) {
                $stMap[$row->id_bidwas][] = $row->id_st;
            });

        // Fallback: jika staf tidak punya bidang atau bidangnya tidak ada dummy ST,
        // gunakan semua dummy ST dari seluruh bidang
        $semuaDummySt = DB::table('d_st')
            ->where(function ($q) {
                $q->where('no_surat_tugas', 'like', 'ST-AKTIF-%')
                  ->orWhere('no_surat_tugas', 'like', 'ST-KBD-%');
            })
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereIn('status_st', ['Realisasi', 'Perpanjangan ST'])
            ->pluck('id_st')
            ->toArray();

        // Ambil semua staf yang belum punya ST aktif hari ini
        $stafKosong = DB::table('users')
            ->where('role', 'pegawai')
            ->whereNotNull('nip')
            ->whereNotIn('nip', function ($q) use ($today) {
                $q->select('d_st_tim.nip')
                  ->from('d_st_tim')
                  ->join('d_st', 'd_st_tim.id_st', '=', 'd_st.id_st')
                  ->where('d_st.start_date', '<=', $today)
                  ->where('d_st.end_date', '>=', $today)
                  ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST']);
            })
            ->get(['nip', 'name']);

        $timMax   = DB::table('d_st_tim')->max('id') ?? 0;
        $inserted = 0;
        $counters = []; // round-robin counter per bidang

        foreach ($stafKosong as $staf) {
            // Cari bidang staf dari r_pegawai
            $idBidwas = DB::table('r_pegawai')->where('nip', $staf->nip)->value('id_bidwas');

            // Pilih pool dummy ST
            $pool = ($idBidwas && isset($stMap[$idBidwas]))
                ? $stMap[$idBidwas]
                : $semuaDummySt;

            if (empty($pool)) continue;

            // Round-robin: ambil satu ST dari pool
            $key = $idBidwas ?? 'all';
            $counters[$key] = ($counters[$key] ?? 0);
            $idSt = $pool[$counters[$key] % count($pool)];
            $counters[$key]++;

            // Jangan duplikat
            $exists = DB::table('d_st_tim')
                ->where('id_st', $idSt)
                ->where('nip', $staf->nip)
                ->exists();

            if (!$exists) {
                $timMax++;
                DB::table('d_st_tim')->insert([
                    'id'    => $timMax,
                    'id_st' => $idSt,
                    'nip'   => $staf->nip,
                    'peran' => 'Anggota',
                ]);
                $inserted++;
            }
        }

        $this->command->info("{$inserted} staf berhasil ditambahkan ke dummy ST.");
        $this->command->info("Total staf kosong sebelumnya: {$stafKosong->count()}");
    }
}
