<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('pengaturan')
            ->whereIn('key_name', [
                'judul_halaman',
                'deskripsi_halaman',
                'judul_seksi_beranda',
                'deskripsi_seksi_beranda'
            ])
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('pengaturan')->insertOrIgnore([
            ['key_name' => 'judul_halaman', 'value' => 'Produk Unggulan Daerah Kami'],
            ['key_name' => 'deskripsi_halaman', 'value' => 'Temukan berbagai hasil pertanian dan peternakan berkualitas terbaik yang dihasilkan langsung dari Desa Sidorejo.'],
            ['key_name' => 'judul_seksi_beranda', 'value' => 'Segar dari Desa'],
            ['key_name' => 'deskripsi_seksi_beranda', 'value' => 'Temukan berbagai produk dan layanan lokal terbaik dari desa kami.']
        ]);
    }
};
