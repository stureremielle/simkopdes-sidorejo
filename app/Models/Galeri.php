<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    protected $table = 'galeri';

    protected $fillable = [
        'judul',
        'kategori',
        'gambar_url',
        'materi_url',
        'keterangan',
        'status',
        'created_at',
    ];

    public $timestamps = false;
}
