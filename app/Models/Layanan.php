<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'kategori_id',
        'kategori',
        'deskripsi',
        'harga',
        'satuan',
        'gambar_url',
        'status',
        'is_featured',
    ];

    protected $appends = ['kategori'];

    public $timestamps = false;

    public function kategoriRelation()
    {
        return $this->belongsTo(KategoriLayanan::class, 'kategori_id');
    }

    public function getKategoriAttribute()
    {
        return $this->kategoriRelation?->nama ?? 'Lainnya';
    }

    public function setKategoriAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['kategori_id'] = null;
            return;
        }
        $cat = KategoriLayanan::whereRaw('LOWER(nama) = ?', [strtolower($value)])->first();
        if ($cat) {
            $this->attributes['kategori_id'] = $cat->id;
        } else {
            $newCat = KategoriLayanan::create(['nama' => ucwords($value)]);
            $this->attributes['kategori_id'] = $newCat->id;
        }
    }
}
