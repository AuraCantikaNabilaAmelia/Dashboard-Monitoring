<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakLanjutEntry extends Model
{
    protected $table = 'tindak_lanjut_entries';

    protected $fillable = [
        'surat_masuk_id',
        'tanggal',
        'keterangan',
        'file_path',
        'file_name',
        'created_by_nip',
        'created_by_nama',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }
}
