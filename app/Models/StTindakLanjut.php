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

    /**
     * Get the ST that owns the tindak lanjut.
     * Note: Since d_st doesn't have a simple ID in Eloquent (compound key),
     * we will use raw query or DB facade in controller for d_st related info.
     */
}
