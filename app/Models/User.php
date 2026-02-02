<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_PIMPINAN = 'pimpinan';
    const ROLE_KABID    = 'kabid';
    const ROLE_PEGAWAI  = 'pegawai';

    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'role',
        'jabatan',
        'bidang_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
