<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Buat LHP dummy untuk:
 * 1. ST aktif dummy (ST-AKTIF-*) — LHP terbit bulan ini
 * 2. Beberapa bulan sebelumnya (Jan–Mei 2026) agar grafik tren terisi
 */
class DummyLhpSeeder extends Seeder
{
    public function run(): void
    {
        $today  = Carbon::today();
        $tahun  = $today->year;
        $idMax  = DB::table('d_lhp')->max('id_lhp') ?? 0;

        // Hapus LHP dummy lama jika ada
        DB::table('d_lhp')->where('nomor_lhp', 'like', 'LHP-DUMMY-%')->delete();

        $rows = [];

        // --- 1. LHP untuk ST aktif dummy (3 dari 5 ST punya LHP) ---
        $stDummy = DB::table('d_st')
            ->where('no_surat_tugas', 'like', 'ST-AKTIF-%')
            ->get(['id_st', 'id_bidwas', 'nama_penugasan']);

        $counter = 0;
        foreach ($stDummy->take(3) as $st) {
            $counter++;
            $idMax++;
            $rows[] = [
                'id_topik'          => 0,
                'id_unit'           => 47,
                'id_bidwas'         => $st->id_bidwas,
                'id_st'             => $st->id_st,
                'id_lhp'            => $idMax,
                'judul_lhp'         => 'Laporan Hasil Pengawasan — ' . $st->nama_penugasan,
                'status_lhp'        => $counter === 1 ? 'Final' : 'Draft',
                'status_dl'         => $counter === 1 ? 'Final' : 'Draft',
                'nomor_lhp'         => 'LHP-DUMMY-' . str_pad($counter, 3, '0', STR_PAD_LEFT) . '/' . $tahun,
                'tanggal_lhp'       => $today->copy()->subDays(rand(1, 7))->toDateString(),
                'tidak_perlu_esign' => 0,
            ];
        }

        // --- 2. LHP tren bulanan Jan–Mei 2026 (pakai ST existing yang ada) ---
        $stExisting = DB::table('d_st')
            ->where('no_surat_tugas', 'not like', 'ST-AKTIF-%')
            ->orderByDesc('id_st')
            ->limit(60)
            ->get(['id_st', 'id_bidwas', 'nama_penugasan']);

        $distribusi = [1 => 4, 2 => 6, 3 => 5, 4 => 7, 5 => 5]; // bulan => jumlah LHP
        $stPool     = $stExisting->values();
        $poolSize   = $stPool->count();
        $poolIdx    = 0;

        foreach ($distribusi as $bulan => $jumlah) {
            $tglBulan = Carbon::create($tahun, $bulan, 1);
            for ($j = 0; $j < $jumlah; $j++) {
                $counter++;
                $idMax++;
                $st = $stPool[$poolIdx % $poolSize];
                $poolIdx++;

                $rows[] = [
                    'id_topik'          => 0,
                    'id_unit'           => 47,
                    'id_bidwas'         => $st->id_bidwas,
                    'id_st'             => $st->id_st,
                    'id_lhp'            => $idMax,
                    'judul_lhp'         => 'Laporan Hasil Pengawasan — ' . $st->nama_penugasan,
                    'status_lhp'        => 'Final',
                    'status_dl'         => 'Final',
                    'nomor_lhp'         => 'LHP-DUMMY-' . str_pad($counter, 3, '0', STR_PAD_LEFT) . '/' . $tahun,
                    'tanggal_lhp'       => $tglBulan->copy()->addDays(rand(1, 25))->toDateString(),
                    'tidak_perlu_esign' => 0,
                ];
            }
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('d_lhp')->insert($chunk);
        }

        $this->command->info(count($rows) . ' LHP dummy berhasil ditambahkan.');
    }
}
