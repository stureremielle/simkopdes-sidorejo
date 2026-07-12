<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPenyimpanan extends Model
{
    use HasFactory;

    protected $table = 'kategori_penyimpanan';

    protected $fillable = ['nama'];
}
