<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    protected $table = 'galeri';

    protected $fillable = [
        'judul',
        'kategori_id',
        'kategori',
        'gambar',
        'materi',
        'keterangan',
        'status',
        'created_at',
    ];

    protected $appends = ['kategori'];

    public $timestamps = false;

    public function kategoriRelation()
    {
        return $this->belongsTo(KategoriGaleri::class, 'kategori_id');
    }

    public function getKategoriAttribute()
    {
        return $this->kategoriRelation?->nama ?? 'Kegiatan';
    }

    public function setKategoriAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['kategori_id'] = null;
            return;
        }
        $cat = KategoriGaleri::whereRaw('LOWER(nama) = ?', [strtolower($value)])->first();
        if ($cat) {
            $this->attributes['kategori_id'] = $cat->id;
        } else {
            $newCat = KategoriGaleri::create(['nama' => ucwords($value)]);
            $this->attributes['kategori_id'] = $newCat->id;
        }
    }
}
