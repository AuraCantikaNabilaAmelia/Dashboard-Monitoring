<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $table = 'surat_masuk';

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'pengirim',
        'penerima',
        'status',
        'target_bidang_id',
        'catatan',
        'file_path',
        'file_name',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'target_bidang_id' => 'integer',
    ];

    public function tindakLanjutEntries()
    {
        return $this->hasMany(TindakLanjutEntry::class, 'surat_masuk_id')->orderBy('tanggal', 'desc');
    }

    public function targetBidang()
    {
        return $this->belongsTo(Bidwas::class, 'target_bidang_id', 'id_bidwas');
    }

    public static function statusLabels(): array
    {
        return [
            'surat_masuk' => 'Surat Masuk',
            'disposisi'   => 'Disposisi',
            'rapat'       => 'Rapat / Expose',
            'notulen'     => 'Notulen',
            'surat_tugas' => 'Surat Tugas',
            'lapangan'    => 'Tindakan Lapangan',
            'keputusan'   => 'Keputusan Akhir',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'surat_masuk' => 'slate',
            'disposisi'   => 'amber',
            'rapat'       => 'indigo',
            'notulen'     => 'purple',
            'surat_tugas' => 'blue',
            'lapangan'    => 'orange',
            'keputusan'   => 'green',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::statusColors()[$this->status] ?? 'slate';
    }
}
