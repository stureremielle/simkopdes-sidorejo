<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'kategori',
        'isi',
        'penulis',
        'gambar_url',
        'is_featured',
        'status',
    ];

    public $timestamps = false;
}
