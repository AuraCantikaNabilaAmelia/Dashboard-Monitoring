<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Jika tabel r_pegawai belum ada, kita import data master dari bpkp.sql
        if (!Schema::hasTable('r_pegawai')) {
            $path = base_path('docs/bpkp.sql');
            
            if (File::exists($path)) {
                // Hapus table stand-in view jika ada yang nyangkut
                DB::statement('DROP VIEW IF EXISTS v_st_lhp_tim');
                DB::statement('DROP TABLE IF EXISTS v_st_lhp_tim');

                // Eksekusi SQL
                $sql = File::get($path);
                DB::unprepared($sql);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kita tidak menghapus tabel master di sini karena data master berharga
        // Jika ingin menghapus semua, gunakan php artisan db:wipe
    }
};
