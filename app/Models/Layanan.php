<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'harga',
        'satuan',
        'gambar_url',
        'status',
    ];

    public $timestamps = false;
}
