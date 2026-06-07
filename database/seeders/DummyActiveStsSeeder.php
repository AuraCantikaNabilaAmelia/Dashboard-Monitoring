<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Tambah ST aktif dummy agar widget "Pegawai Beban Tinggi" terisi.
 * Setiap pegawai target sudah punya 1 ST real aktif, jadi dummy ditambah
 * seperlunya agar total tidak melebihi 3:
 *
 *   Tuti (18)    : 1 real + 2 dummy = 3 ST
 *   Ma`mun (27)  : 1 real + 2 dummy = 3 ST
 *   Yulius (37)  : 1 real + 1 dummy = 2 ST
 *   Dicky (48)   : 1 real + 1 dummy = 2 ST
 *   Bunyamin (1) : 0 real + 2 dummy = 2 ST
 */
class DummyActiveStsSeeder extends Seeder
{
    public function run(): void
    {
        $today  = Carbon::today();
        $idMax  = DB::table('d_st')->max('id_st') ?? 300000;
        $timMax = DB::table('d_st_tim')->max('id') ?? 0;

        $stDummy = [
            // +1 untuk Tuti(18) dan Ma`mun(27)
            ['nm' => 'Audit Kinerja Pengelolaan APBD Provinsi',       'bid' => 158, 'start' => -10, 'end' => 20, 'tim' => ['18', '27']],
            // +1 untuk Tuti(18) dan Yulius(37)
            ['nm' => 'Reviu Penggunaan Dana BOS Kabupaten Selatan',   'bid' => 158, 'start' => -8,  'end' => 22, 'tim' => ['18', '37']],
            // +1 untuk Ma`mun(27) dan Dicky(48)
            ['nm' => 'Pemeriksaan Pengelolaan Aset Dinas Pendidikan', 'bid' => 158, 'start' => -7,  'end' => 23, 'tim' => ['27', '48']],
            // +1 untuk Bunyamin(1) dan Yayan(5)
            ['nm' => 'Audit Kinerja Pelayanan Puskesmas Utara',       'bid' => 159, 'start' => -10, 'end' => 20, 'tim' => ['1', '5']],
            // +1 untuk Bunyamin(1) dan Asep(7)
            ['nm' => 'Reviu Pengelolaan Dana Desa Kabupaten Barat',   'bid' => 159, 'start' => -8,  'end' => 22, 'tim' => ['1', '7']],
        ];

        $i = 0;
        foreach ($stDummy as $d) {
            $i++;
            $idBaru    = $idMax + $i;
            $startDate = $today->copy()->addDays($d['start'])->toDateString();
            $endDate   = $today->copy()->addDays($d['end'])->toDateString();

            DB::table('d_st')->insert([
                'id_topik'       => 0,
                'id_unit'        => 47,
                'id_bidwas'      => $d['bid'],
                'id_st'          => $idBaru,
                'nama_penugasan' => $d['nm'],
                'no_surat_tugas' => 'ST-AKTIF-' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/' . $today->year,
                'status_st'      => 'Realisasi',
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'updated_at'     => $today->toDateTimeString(),
            ]);

            foreach ($d['tim'] as $j => $nip) {
                $timMax++;
                DB::table('d_st_tim')->insert([
                    'id'    => $timMax,
                    'id_st' => $idBaru,
                    'nip'   => $nip,
                    'peran' => $j === 0 ? 'Ketua Tim' : 'Anggota',
                ]);
            }
        }

        $this->command->info("{$i} ST aktif dummy berhasil ditambahkan.");
    }
}
