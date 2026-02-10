<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bidwas extends Model
{
    protected $table = 'r_bidwas';
    protected $primaryKey = 'id_bidwas';
    public $timestamps = false;

    protected $fillable = [
        'id_bidwas',
        'id_unit',
        'kd_bidwas',
        'nm_bidwas',
    ];
}
