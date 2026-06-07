<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserPegawaiSeeder extends Seeder
{
    // Mapping user_role r_pegawai → role users
    private array $roleMap = [
        'kepala_perwakilan'        => 'pimpinan',
        'kepala_bagian_umum'       => 'pimpinan',
        'korwas_apd_1'             => 'kabid',
        'korwas_apd_2'             => 'kabid',
        'korwas_an_1'              => 'kabid',
        'korwas_an_2'              => 'kabid',
        'korwas_ipp_1'             => 'kabid',
        'korwas_ipp_2'             => 'kabid',
        'korwas_investigasi_1'     => 'kabid',
        'korwas_investigasi_2'     => 'kabid',
        'korwas_p3a'               => 'kabid',
        'subkoor_keuangan'         => 'kabid',
        'subkoor_bmn_rt_kearsipan' => 'kabid',
        'staff'                    => 'pegawai',
    ];

    public function run(): void
    {
        $pegawaiList = DB::table('r_pegawai')
            ->select('nip', 'nama', 'user_role', 'id_bidwas')
            ->whereNotNull('nip')
            ->get();

        // NIP yang sudah punya akun
        $existingNips = DB::table('users')
            ->whereNotNull('nip')
            ->pluck('nip')
            ->flip()
            ->toArray();

        $inserted = 0;
        $skipped  = 0;
        $batch    = [];

        foreach ($pegawaiList as $p) {
            if (isset($existingNips[$p->nip])) {
                $skipped++;
                continue;
            }

            $role = $this->roleMap[$p->user_role] ?? 'pegawai';

            $batch[] = [
                'name'              => $p->nama,
                'email'             => $p->nip . '@bpkp.go.id',
                'nip'               => $p->nip,
                'password'          => Hash::make('password'),
                'role'              => $role,
                'bidang_id'         => $p->id_bidwas,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            $inserted++;

            // Insert per 50 agar tidak timeout
            if (count($batch) >= 50) {
                DB::table('users')->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('users')->insert($batch);
        }

        $this->command->info("✓ {$inserted} user baru dibuat, {$skipped} dilewati (sudah ada).");
        $this->command->info("  Password default: password");
        $this->command->newLine();

        // Ringkasan per bidang
        $summary = DB::table('users')
            ->join('r_bidwas', 'users.bidang_id', '=', 'r_bidwas.id_bidwas')
            ->select('r_bidwas.nm_bidwas', DB::raw('count(*) as total'))
            ->groupBy('r_bidwas.nm_bidwas')
            ->orderBy('total', 'desc')
            ->get();

        $this->command->info("Distribusi user per bidang:");
        foreach ($summary as $row) {
            $label = str_pad(substr($row->nm_bidwas, 0, 55), 57);
            $this->command->info("  {$label} {$row->total}");
        }

        $this->command->newLine();
        $this->command->info("Role distribution:");
        $roleSummary = DB::table('users')
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get();
        foreach ($roleSummary as $r) {
            $this->command->info("  {$r->role}: {$r->total}");
        }
    }
}
