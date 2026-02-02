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
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat');
            $table->date('tanggal_surat');
            $table->text('perihal');
            $table->string('pengirim');
            $table->enum('status', ['masuk', 'disposisi', 'rapat', 'notulen', 'st', 'selesai'])->default('masuk');
            $table->string('penerima_nip')->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('id_st')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
