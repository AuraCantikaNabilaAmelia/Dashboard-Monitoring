<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PegawaiBidwasSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya korwas yang masuk divisi (sesuai bidangnya).
        // Kepala perwakilan, kepala bagian umum, dan subkoor TIDAK masuk divisi manapun (NULL).
        $roleMapping = [
            'korwas_apd_1'            => 159,
            'korwas_apd_2'            => 159,
            'korwas_an_1'             => 160,
            'korwas_an_2'             => 160,
            'korwas_ipp_1'            => 158,
            'korwas_ipp_2'            => 158,
            'korwas_investigasi_1'    => 161,
            'korwas_investigasi_2'    => 161,
            'korwas_p3a'              => 320,
        ];

        foreach ($roleMapping as $role => $bidwasId) {
            DB::table('r_pegawai')
                ->where('user_role', $role)
                ->update(['id_bidwas' => $bidwasId]);
        }

        // Pastikan role non-divisi selalu NULL
        DB::table('r_pegawai')
            ->whereIn('user_role', ['kepala_perwakilan', 'kepala_bagian_umum', 'subkoor_keuangan', 'subkoor_bmn_rt_kearsipan'])
            ->update(['id_bidwas' => null]);

        $this->command->info('✓ 9 korwas di-assign ke bidang; kepala & subkoor di-set NULL (tanpa divisi).');

        $mainBidwas = [
            158,
            159,
            160,
            161,
            320,
            157,
        ];

        $staffIds = DB::table('r_pegawai')
            ->where('user_role', 'staff')
            ->whereNull('id_bidwas')
            ->pluck('id')
            ->toArray();

        shuffle($staffIds);

        foreach ($staffIds as $index => $pegawaiId) {
            $bidwasId = $mainBidwas[$index % count($mainBidwas)];
            DB::table('r_pegawai')
                ->where('id', $pegawaiId)
                ->update(['id_bidwas' => $bidwasId]);
        }

        $this->command->info('✓ ' . count($staffIds) . ' pegawai staff berhasil didistribusikan ke 6 bidang utama.');

        $summary = DB::table('r_pegawai')
            ->join('r_bidwas', 'r_pegawai.id_bidwas', '=', 'r_bidwas.id_bidwas')
            ->select('r_bidwas.nm_bidwas', DB::raw('count(*) as total'))
            ->groupBy('r_bidwas.nm_bidwas')
            ->get();

        $this->command->info("\nRingkasan Distribusi:");
        foreach ($summary as $row) {
            $this->command->info("  {$row->nm_bidwas}: {$row->total} pegawai");
        }
    }
}
