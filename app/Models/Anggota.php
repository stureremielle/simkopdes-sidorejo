<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'anggota';

    protected $fillable = [
        'nama_lengkap',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat_lengkap',
        'rt',
        'dusun',
        'no_hp',
        'email',
        'pekerjaan',
        'pendidikan',
        'motivasi',
        'jabatan',
        'sumber',
        'status',
    ];

    public $timestamps = false;
}
