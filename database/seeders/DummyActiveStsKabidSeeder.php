<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Dummy ST aktif per bidang agar widget "Pegawai Beban Tinggi" di tampilan kabid terisi.
 * Bidang 158 & 159 sudah punya data (dari DummyActiveStsSeeder), jadi hanya 157, 160, 161, 320.
 *
 * Rencana beban (existing + dummy = max 3):
 *
 * Bidang 157 (Tata Usaha) — semua 0 ST real:
 *   Cecep(2)=0+3=3, Okke(26)=0+3=3, Aditya(28)=0+2=2, Elias(42)=0+2=2
 *
 * Bidang 160 (Akuntan Negara) — Tuti(6)=1, Raden(13)=1 real:
 *   Maman(3)=0+3=3, Luthfi(4)=0+3=3, Tuti(6)=1+2=3, Raden(13)=1+1=2
 *
 * Bidang 161 (Investigasi) — Rudy(21)=1, Aang(23)=1 real:
 *   Daniel(11)=0+3=3, Algi(14)=0+3=3, Rudy(21)=1+2=3, Aang(23)=1+1=2
 *
 * Bidang 320 (Program & Pelaporan) — semua 0 ST real:
 *   Indra(9)=0+3=3, Karlina(15)=0+3=3, Indra2(30)=0+2=2, Probo(31)=0+2=2
 */
class DummyActiveStsKabidSeeder extends Seeder
{
    public function run(): void
    {
        $today  = Carbon::today();
        $idMax  = DB::table('d_st')->max('id_st') ?? 300000;
        $timMax = DB::table('d_st_tim')->max('id') ?? 0;

        // Hapus dummy kabid lama jika ada
        $existing = DB::table('d_st')->where('no_surat_tugas', 'like', 'ST-KBD-%')->pluck('id_st');
        if ($existing->isNotEmpty()) {
            DB::table('d_st_tim')->whereIn('id_st', $existing)->delete();
            DB::table('d_st')->whereIn('id_st', $existing)->delete();
        }

        // Format: ['nm', 'bid', start_offset, end_offset, [nip_ketua, nip_anggota]]
        $stDummy = [

            // === Bidang 157 — Tata Usaha ===
            // ST-1: Cecep(2) + Okke(26)
            ['nm' => 'Reviu Pengelolaan Keuangan Internal Kantor',         'bid' => 157, 'start' => -10, 'end' => 20, 'tim' => ['2',  '26']],
            // ST-2: Cecep(2) + Aditya(28)
            ['nm' => 'Audit Kepatuhan Pengelolaan BMN Satuan Kerja',       'bid' => 157, 'start' => -8,  'end' => 22, 'tim' => ['2',  '28']],
            // ST-3: Cecep(2) + Elias(42)
            ['nm' => 'Reviu Laporan Keuangan Semester I Unit Kerja',       'bid' => 157, 'start' => -6,  'end' => 24, 'tim' => ['2',  '42']],
            // ST-4: Okke(26) + Aditya(28)
            ['nm' => 'Pemeriksaan Pengelolaan Aset Tetap Kantor Wilayah',  'bid' => 157, 'start' => -12, 'end' => 18, 'tim' => ['26', '28']],
            // ST-5: Okke(26) + Elias(42)
            ['nm' => 'Audit Pengadaan Barang dan Jasa Bidang Tata Usaha',  'bid' => 157, 'start' => -7,  'end' => 23, 'tim' => ['26', '42']],

            // === Bidang 160 — Akuntan Negara ===
            // ST-6: Maman(3) + Luthfi(4)
            ['nm' => 'Audit Kinerja Pengelolaan PNBP Kementerian Teknis',  'bid' => 160, 'start' => -9,  'end' => 21, 'tim' => ['3',  '4']],
            // ST-7: Maman(3) + Tuti(6)  [Tuti real=1, dummy ini ke-2]
            ['nm' => 'Reviu Laporan Keuangan Badan Layanan Umum',          'bid' => 160, 'start' => -11, 'end' => 19, 'tim' => ['3',  '6']],
            // ST-8: Maman(3) + Raden(13) [Raden real=1, dummy ini ke-2 → total=2]
            ['nm' => 'Pemeriksaan Subsidi Pemerintah Sektor Energi',       'bid' => 160, 'start' => -5,  'end' => 25, 'tim' => ['3',  '13']],
            // ST-9: Luthfi(4) + Tuti(6)  [Tuti dummy ke-3 → total=3]
            ['nm' => 'Audit Efektivitas Dana Dekonsentrasi Kementerian',   'bid' => 160, 'start' => -14, 'end' => 16, 'tim' => ['4',  '6']],
            // ST-10: Luthfi(4) + Ganis(19)
            ['nm' => 'Reviu Pengelolaan Investasi Pemerintah Pusat',       'bid' => 160, 'start' => -7,  'end' => 23, 'tim' => ['4',  '19']],

            // === Bidang 161 — Investigasi ===
            // ST-11: Daniel(11) + Algi(14)
            ['nm' => 'Audit Investigatif Dugaan Penyimpangan Pengadaan',   'bid' => 161, 'start' => -10, 'end' => 20, 'tim' => ['11', '14']],
            // ST-12: Daniel(11) + Rudy(21) [Rudy real=1, dummy ini ke-2]
            ['nm' => 'Reviu Investigasi Dana Bantuan Sosial Kabupaten',    'bid' => 161, 'start' => -8,  'end' => 22, 'tim' => ['11', '21']],
            // ST-13: Daniel(11) + Aang(23) [Aang real=1, dummy ini ke-2 → total=2]
            ['nm' => 'Pemeriksaan Khusus Pengelolaan Dana Hibah Daerah',   'bid' => 161, 'start' => -6,  'end' => 24, 'tim' => ['11', '23']],
            // ST-14: Algi(14) + Rudy(21)   [Rudy dummy ke-3 → total=3]
            ['nm' => 'Audit Investigatif Penyimpangan Belanja Modal',      'bid' => 161, 'start' => -12, 'end' => 18, 'tim' => ['14', '21']],
            // ST-15: Algi(14) + Arief(16)
            ['nm' => 'Reviu Kepatuhan Pengelolaan Dana BOS Investigasi',   'bid' => 161, 'start' => -4,  'end' => 26, 'tim' => ['14', '16']],

            // === Bidang 320 — Program & Pelaporan ===
            // ST-16: Indra(9) + Karlina(15)
            ['nm' => 'Reviu Penyusunan PKPT Bidang Pengawasan Internal',   'bid' => 320, 'start' => -9,  'end' => 21, 'tim' => ['9',  '15']],
            // ST-17: Indra(9) + Indra2(30)
            ['nm' => 'Pemantauan Kualitas Laporan Hasil Pengawasan',       'bid' => 320, 'start' => -7,  'end' => 23, 'tim' => ['9',  '30']],
            // ST-18: Indra(9) + Probo(31)
            ['nm' => 'Evaluasi Capaian Program Kerja Pengawasan Tahunan',  'bid' => 320, 'start' => -5,  'end' => 25, 'tim' => ['9',  '31']],
            // ST-19: Karlina(15) + Indra2(30)
            ['nm' => 'Reviu Penyusunan Laporan Kinerja Perwakilan',        'bid' => 320, 'start' => -11, 'end' => 19, 'tim' => ['15', '30']],
            // ST-20: Karlina(15) + Hendri(35)
            ['nm' => 'Audit Pengelolaan Anggaran Program Pelaporan APIP',  'bid' => 320, 'start' => -8,  'end' => 22, 'tim' => ['15', '35']],
        ];

        $i = 0;
        foreach ($stDummy as $d) {
            $i++;
            $idMax++;
            $startDate = $today->copy()->addDays($d['start'])->toDateString();
            $endDate   = $today->copy()->addDays($d['end'])->toDateString();

            DB::table('d_st')->insert([
                'id_topik'       => 0,
                'id_unit'        => 47,
                'id_bidwas'      => $d['bid'],
                'id_st'          => $idMax,
                'nama_penugasan' => $d['nm'],
                'no_surat_tugas' => 'ST-KBD-' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/' . $today->year,
                'status_st'      => 'Realisasi',
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'updated_at'     => $today->toDateTimeString(),
            ]);

            foreach ($d['tim'] as $j => $nip) {
                $timMax++;
                DB::table('d_st_tim')->insert([
                    'id'    => $timMax,
                    'id_st' => $idMax,
                    'nip'   => $nip,
                    'peran' => $j === 0 ? 'Ketua Tim' : 'Anggota',
                ]);
            }
        }

        $this->command->info("{$i} ST dummy kabid berhasil ditambahkan.");
    }
}
