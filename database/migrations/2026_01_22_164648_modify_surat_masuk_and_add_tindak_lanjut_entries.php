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
        // Modify surat_masuk table
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->string('penerima')->nullable()->after('pengirim'); // Ke siapa
            $table->dropColumn('status');
        });

        // Re-add status with simplified values
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'selesai'])->default('aktif')->after('penerima');
        });

        // Drop unused columns
        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn(['penerima_nip', 'id_st']);
        });

        // Create tindak_lanjut_entries table for free-text follow-ups
        Schema::create('tindak_lanjut_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_masuk_id');
            $table->date('tanggal');
            $table->text('keterangan');
            $table->string('created_by_nip')->nullable();
            $table->string('created_by_nama')->nullable();
            $table->timestamps();

            $table->foreign('surat_masuk_id')->references('id')->on('surat_masuk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut_entries');

        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->dropColumn(['penerima', 'status']);
        });

        Schema::table('surat_masuk', function (Blueprint $table) {
            $table->enum('status', ['masuk', 'disposisi', 'rapat', 'notulen', 'st', 'selesai'])->default('masuk');
            $table->string('penerima_nip')->nullable();
            $table->unsignedBigInteger('id_st')->nullable();
        });
    }
};
