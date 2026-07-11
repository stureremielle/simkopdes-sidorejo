<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyimpananFile extends Model
{
    protected $table = 'penyimpanan_file';

    protected $fillable = [
        'nama_file',
        'nama_asli',
        'kategori',
        'ukuran',
        'tipe',
        'keterangan',
    ];

    const CREATED_AT = 'uploaded_at';
    const UPDATED_AT = null;
}
