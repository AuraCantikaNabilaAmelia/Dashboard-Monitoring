<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the column if it was partially created from a previous failed attempt
        if (Schema::hasColumn('r_pegawai', 'id_bidwas')) {
            Schema::table('r_pegawai', function (Blueprint $table) {
                $table->dropColumn('id_bidwas');
            });
        }

        Schema::table('r_pegawai', function (Blueprint $table) {
            $table->integer('id_bidwas')->nullable()->after('user_role');
            $table->foreign('id_bidwas')->references('id_bidwas')->on('r_bidwas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('r_pegawai', function (Blueprint $table) {
            $table->dropForeign(['id_bidwas']);
            $table->dropColumn('id_bidwas');
        });
    }
};
