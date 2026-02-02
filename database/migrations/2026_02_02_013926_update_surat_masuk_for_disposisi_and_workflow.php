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
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->unsignedInteger('target_bidang_id')->nullable()->after('status');
            
            // Re-define status column with more detailed workflow
            // Note: In MySQL, we need to handle enum updates carefully
            $table->string('status')->change(); // Temporarily change to string to avoid enum issues
        });

        // Update existing 'aktif' status to 'masuk' for legacy data
        \DB::table('surat_masuk')->where('status', 'aktif')->update(['status' => 'masuk']);

        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->enum('status_new', ['masuk', 'disposisi', 'proses', 'selesai'])->default('masuk')->after('status');
        });

        // Copy data to new status column
        \DB::statement("UPDATE surat_masuk SET status_new = 
            CASE 
                WHEN status = 'aktif' THEN 'masuk'
                WHEN status = 'selesai' THEN 'selesai'
                ELSE 'masuk'
            END");

        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn('target_bidang_id');
            $table->enum('status_old', ['aktif', 'selesai'])->default('aktif');
        });

        \DB::statement("UPDATE surat_masuk SET status_old = 
            CASE 
                WHEN status = 'selesai' THEN 'selesai'
                ELSE 'aktif'
            END");

        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->renameColumn('status_old', 'status');
        });
    }
};
