<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('d_st', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable()->after('end_date');
        });

        // Isi updated_at awal dengan end_date agar data lama tidak langsung kena auto-batal
        DB::statement('UPDATE d_st SET updated_at = end_date WHERE updated_at IS NULL');
    }

    public function down(): void
    {
        Schema::table('d_st', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};
