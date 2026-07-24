<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'kategori_id',
        'kategori',
        'isi',
        'penulis',
        'gambar_url',
        'is_featured',
        'status',
        'tanggal_publikasi',
    ];

    const UPDATED_AT = null; // tabel tidak punya kolom updated_at
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tanggal_publikasi)) {
                $model->tanggal_publikasi = date('Y-m-d');
            }
        });
    }


    public function kategoriRelation()
    {
        return $this->belongsTo(KategoriBerita::class, 'kategori_id');
    }

    public function getKategoriAttribute()
    {
        return $this->kategoriRelation?->nama ?? '';
    }

    public function setKategoriAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['kategori_id'] = null;
            return;
        }
        $cat = KategoriBerita::whereRaw('LOWER(nama) = ?', [strtolower($value)])->first();
        if ($cat) {
            $this->attributes['kategori_id'] = $cat->id;
        } else {
            $newCat = KategoriBerita::create(['nama' => ucwords($value)]);
            $this->attributes['kategori_id'] = $newCat->id;
        }
    }
}
