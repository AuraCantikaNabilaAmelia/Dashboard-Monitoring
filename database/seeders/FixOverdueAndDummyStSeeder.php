<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FixOverdueAndDummyStSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        // 1. Selesaikan semua ST yang overdue (lewat end_date, bukan Batal/Selesai/Final)
        $statusTakOverdue = ['Selesai', 'Final', 'tidak aktif', 'Batal'];
        DB::table('d_st')
            ->where('end_date', '<', $today->toDateString())
            ->whereNotIn('status_st', $statusTakOverdue)
            ->update([
                'status_st'  => 'Selesai',
                'updated_at' => $today->toDateTimeString(),
            ]);

        // 2. Ambil referensi bidwas & pegawai untuk dummy data
        $bidwasIds = DB::table('r_bidwas')
            ->whereIn('id_bidwas', [158, 159, 160, 161, 320])
            ->pluck('id_bidwas')
            ->toArray();

        $pegawaiPerBidwas = [];
        foreach ($bidwasIds as $bid) {
            $pegawaiPerBidwas[$bid] = DB::table('r_pegawai')
                ->where('id_bidwas', $bid)
                ->pluck('nip')
                ->toArray();
        }

        $idMax    = DB::table('d_st')->max('id_st') ?? 200000;
        $timIdMax = DB::table('d_st_tim')->max('id') ?? 0;

        // 3. Definisi 25 ST dummy dengan distribusi seimbang:
        //    4  aktif on-track (end_date masa depan)
        //    8  telat 1-6 hari   → muncul di perhatian khusus
        //    8  telat 7-30 hari  → muncul di perhatian khusus
        //    5  telat >30 hari   → muncul di perhatian khusus (updated_at baru, belum auto-batal)
        $dummy = [
            // === AKTIF (4) ===
            ['nm' => 'Audit Kinerja Pelayanan Publik Sektor Kesehatan', 'bid' => 158, 'start' => -10, 'end' => 20,  'status' => 'Realisasi'],
            ['nm' => 'Reviu Laporan Keuangan Triwulan II', 'bid' => 159, 'start' => -5,  'end' => 25,  'status' => 'Realisasi'],
            ['nm' => 'Pemeriksaan Pengelolaan Dana Desa Kabupaten X', 'bid' => 160, 'start' => -3,  'end' => 30,  'status' => 'Realisasi'],
            ['nm' => 'Audit Pengadaan Barang dan Jasa Instansi Pusat', 'bid' => 161, 'start' => -7,  'end' => 14,  'status' => 'Perpanjangan ST'],

            // === TELAT 1-6 HARI (8) ===
            ['nm' => 'Reviu Dana Alokasi Khusus Bidang Pendidikan', 'bid' => 158, 'start' => -20, 'end' => -1,  'status' => 'Realisasi'],
            ['nm' => 'Audit Kepatuhan Pengelolaan Keuangan Daerah', 'bid' => 159, 'start' => -25, 'end' => -2,  'status' => 'Realisasi'],
            ['nm' => 'Pemeriksaan Aset Tetap Pemerintah Kota Y', 'bid' => 160, 'start' => -30, 'end' => -3,  'status' => 'Realisasi'],
            ['nm' => 'Reviu Anggaran Pendapatan Belanja Daerah', 'bid' => 161, 'start' => -22, 'end' => -4,  'status' => 'Realisasi'],
            ['nm' => 'Audit Investigatif Dugaan Penyimpangan Dana BOS', 'bid' => 320, 'start' => -18, 'end' => -1,  'status' => 'Realisasi'],
            ['nm' => 'Pemantauan Tindak Lanjut Hasil Audit Tahun Lalu', 'bid' => 158, 'start' => -28, 'end' => -2,  'status' => 'Perpanjangan ST'],
            ['nm' => 'Reviu Penyerapan Anggaran Semester I', 'bid' => 159, 'start' => -15, 'end' => -5,  'status' => 'Realisasi'],
            ['nm' => 'Audit Kinerja Program Nasional Penurunan Stunting', 'bid' => 160, 'start' => -20, 'end' => -6,  'status' => 'Realisasi'],

            // === TELAT 7-30 HARI (8) ===
            ['nm' => 'Audit Penggunaan Dana Hibah Provinsi Z', 'bid' => 161, 'start' => -45, 'end' => -7,  'status' => 'Realisasi'],
            ['nm' => 'Pemeriksaan Kepatuhan Perpajakan BUMN Sektor Energi', 'bid' => 320, 'start' => -50, 'end' => -10, 'status' => 'Realisasi'],
            ['nm' => 'Reviu Laporan Keuangan Pemerintah Daerah Kabupaten A', 'bid' => 158, 'start' => -55, 'end' => -12, 'status' => 'Perpanjangan ST'],
            ['nm' => 'Audit Dana Otonomi Khusus Papua', 'bid' => 159, 'start' => -60, 'end' => -15, 'status' => 'Realisasi'],
            ['nm' => 'Pemeriksaan Pengadaan Infrastruktur Jalan Daerah', 'bid' => 160, 'start' => -65, 'end' => -18, 'status' => 'Realisasi'],
            ['nm' => 'Reviu Sistem Pengendalian Intern Pemerintah', 'bid' => 161, 'start' => -70, 'end' => -20, 'status' => 'Realisasi'],
            ['nm' => 'Audit Efektivitas Program Bantuan Sosial', 'bid' => 320, 'start' => -75, 'end' => -25, 'status' => 'Realisasi'],
            ['nm' => 'Pemeriksaan Pengelolaan BUMD Sektor Air Bersih', 'bid' => 158, 'start' => -80, 'end' => -30, 'status' => 'Perpanjangan ST'],

            // === TELAT >30 HARI (5) — updated_at baru agar belum auto-batal ===
            ['nm' => 'Audit Kinerja Layanan Kesehatan Dasar Puskesmas', 'bid' => 159, 'start' => -120, 'end' => -35, 'status' => 'Realisasi'],
            ['nm' => 'Pemeriksaan Dana Transfer ke Daerah Khusus', 'bid' => 160, 'start' => -130, 'end' => -40, 'status' => 'Realisasi'],
            ['nm' => 'Reviu Perencanaan Anggaran Infrastruktur Nasional', 'bid' => 161, 'start' => -140, 'end' => -45, 'status' => 'Realisasi'],
            ['nm' => 'Audit Efisiensi Belanja Pegawai Pemerintah Pusat', 'bid' => 320, 'start' => -150, 'end' => -50, 'status' => 'Realisasi'],
            ['nm' => 'Pemeriksaan Pengelolaan Keuangan Dana Pendidikan', 'bid' => 158, 'start' => -160, 'end' => -60, 'status' => 'Perpanjangan ST'],
        ];

        $i = 1;
        foreach ($dummy as $d) {
            $idBaru    = $idMax + $i;
            $startDate = $today->copy()->addDays($d['start'])->toDateString();
            $endDate   = $today->copy()->addDays($d['end'])->toDateString();
            $bidId     = $d['bid'];

            // updated_at: untuk >30 hari telat, set updated_at = hari ini (baru disentuh)
            // agar belum kena auto-batal. Untuk lainnya, set = end_date.
            $updatedAt = $d['end'] < -30
                ? $today->toDateTimeString()
                : Carbon::parse($endDate)->toDateTimeString();

            DB::table('d_st')->insert([
                'id_topik'       => 0,
                'id_unit'        => 47,
                'id_bidwas'      => $bidId,
                'id_st'          => $idBaru,
                'nama_penugasan' => $d['nm'],
                'no_surat_tugas' => 'ST-DUMMY-' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/' . $today->year,
                'status_st'      => $d['status'],
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'updated_at'     => $updatedAt,
            ]);

            // Tambahkan 1-2 anggota tim dari bidwas yang sama
            $nipPool = $pegawaiPerBidwas[$bidId] ?? [];
            if (!empty($nipPool)) {
                $anggota = array_slice($nipPool, ($i - 1) % count($nipPool), 2);
                $timRows = [];
                foreach ($anggota as $j => $nip) {
                    $timIdMax++;
                    $timRows[] = [
                        'id'    => $timIdMax,
                        'id_st' => $idBaru,
                        'nip'   => $nip,
                        'peran' => $j === 0 ? 'Ketua Tim' : 'Anggota',
                    ];
                }
                if ($timRows) DB::table('d_st_tim')->insert($timRows);
            }

            $i++;
        }

        $this->command->info('Selesai: ' . ($i - 1) . ' ST dummy ditambahkan, semua ST overdue lama diselesaikan.');
    }
}
