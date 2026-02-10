<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PegawaiBidwasSeeder extends Seeder
{
    public function run(): void
    {
        // === 1. Assign pegawai yang sudah punya role spesifik ===
        $roleMapping = [
            'korwas_apd_1'            => 159, // APD
            'korwas_apd_2'            => 159,
            'korwas_an_1'             => 160, // AN
            'korwas_an_2'             => 160,
            'korwas_ipp_1'            => 158, // IPP
            'korwas_ipp_2'            => 158,
            'korwas_investigasi_1'    => 161, // Investigasi
            'korwas_investigasi_2'    => 161,
            'korwas_p3a'              => 320, // P3A
            'kepala_perwakilan'       => 157, // TU (Pimpinan)
            'kepala_bagian_umum'      => 553, // Umum
            'subkoor_keuangan'        => 552, // Keuangan
            'subkoor_bmn_rt_kearsipan'=> 553, // Umum
        ];

        foreach ($roleMapping as $role => $bidwasId) {
            DB::table('r_pegawai')
                ->where('user_role', $role)
                ->update(['id_bidwas' => $bidwasId]);
        }

        $this->command->info('✓ 13 pegawai dengan role spesifik berhasil di-assign ke bidang.');

        // === 2. Distribusikan 273 pegawai "staff" ke 6 bidang utama ===
        $mainBidwas = [
            158, // IPP
            159, // APD
            160, // AN
            161, // Investigasi
            320, // P3A
            157, // TU
        ];

        $staffIds = DB::table('r_pegawai')
            ->where('user_role', 'staff')
            ->whereNull('id_bidwas')
            ->pluck('id')
            ->toArray();

        // Shuffle untuk distribusi random yang lebih natural
        shuffle($staffIds);

        foreach ($staffIds as $index => $pegawaiId) {
            $bidwasId = $mainBidwas[$index % count($mainBidwas)];
            DB::table('r_pegawai')
                ->where('id', $pegawaiId)
                ->update(['id_bidwas' => $bidwasId]);
        }

        $this->command->info('✓ ' . count($staffIds) . ' pegawai staff berhasil didistribusikan ke 6 bidang utama.');

        // === 3. Ringkasan ===
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
