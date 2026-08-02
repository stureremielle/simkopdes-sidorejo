<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyimpananFile extends Model
{
    protected $table = 'penyimpanan_file';

    protected $fillable = [
        'nama_file',
        'nama_asli',
        'kategori_id',
        'kategori',
        'ukuran',
        'tipe',
        'keterangan',
    ];

    protected $appends = ['kategori'];

    const CREATED_AT = 'uploaded_at';
    const UPDATED_AT = null;

    public function kategoriRelation()
    {
        return $this->belongsTo(KategoriPenyimpanan::class, 'kategori_id');
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
        $cat = KategoriPenyimpanan::whereRaw('LOWER(nama) = ?', [strtolower($value)])->first();
        if ($cat) {
            $this->attributes['kategori_id'] = $cat->id;
        } else {
            $newCat = KategoriPenyimpanan::create(['nama' => ucwords($value)]);
            $this->attributes['kategori_id'] = $newCat->id;
        }
    }
}
