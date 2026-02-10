<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('st_tindak_lanjut', function (Blueprint $table) {
            $table->id();
            // Referensi ke d_st.id_st. Menggunakan bigInteger karena id_st adalah bigint.
            // Karena primary key d_st adalah compound (id_topik, id_unit, id_bidwas, id_st), 
            // kita gunakan id_st sebagai pengait utama untuk tindak lanjut naratif.
            $table->bigInteger('id_st');
            $table->text('catatan');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('created_by_nip');
            $table->string('created_by_nama');
            $table->timestamps();

            // Index untuk kecepatan query
            $table->index('id_st');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('st_tindak_lanjut');
    }
};
