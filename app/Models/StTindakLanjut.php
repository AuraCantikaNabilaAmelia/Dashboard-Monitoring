<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StTindakLanjut extends Model
{
    use HasFactory;

    protected $table = 'st_tindak_lanjut';

    protected $fillable = [
        'id_st',
        'catatan',
        'file_path',
        'file_name',
        'created_by_nip',
        'created_by_nama',
    ];
}
