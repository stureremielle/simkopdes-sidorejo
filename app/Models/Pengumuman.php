<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'isi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    /**
     * Accessor for 'tanggal' to maintain compatibility with the homepage view.
     */
    public function getTanggalAttribute(): string
    {
        if (!$this->tanggal_mulai) {
            return '';
        }

        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $startTs = strtotime($this->tanggal_mulai);
        $startY = date('Y', $startTs);
        $startM = (int)date('n', $startTs);
        $startD = date('j', $startTs);

        if (!$this->tanggal_selesai || $this->tanggal_mulai === $this->tanggal_selesai) {
            return $startD . ' ' . $bulanIndo[$startM] . ' ' . $startY;
        }

        $endTs = strtotime($this->tanggal_selesai);
        $endY = date('Y', $endTs);
        $endM = (int)date('n', $endTs);
        $endD = date('j', $endTs);

        if ($startY === $endY && $startM === $endM) {
            return $startD . ' – ' . $endD . ' ' . $bulanIndo[$startM] . ' ' . $startY;
        } elseif ($startY === $endY) {
            return $startD . ' ' . $bulanIndo[$startM] . ' – ' . $endD . ' ' . $bulanIndo[$endM] . ' ' . $startY;
        } else {
            return $startD . ' ' . $bulanIndo[$startM] . ' ' . $startY . ' – ' . $endD . ' ' . $bulanIndo[$endM] . ' ' . $endY;
        }
    }
}
