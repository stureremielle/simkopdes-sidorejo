<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update the existing draft article (Koperasi Bagikan SHU Tahunan) to 'tayang'
        DB::table('berita')
            ->where('id', 4)
            ->update(['status' => 'tayang']);

        // 2. Insert the three additional standard articles from the mockup
        DB::table('berita')->insert([
            [
                'id' => 5,
                'judul' => 'Pelatihan Penggunaan Teknologi Pertanian Modern',
                'kategori' => 'Teknologi',
                'isi' => 'Koperasi mengadakan pelatihan penggunaan drone untuk pemantauan lahan dan aplikasi mobile untuk pencatatan hasil panen bagi para anggota.',
                'penulis' => 'Admin',
                'gambar_url' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&fit=crop&q=80',
                'is_featured' => 0,
                'status' => 'tayang',
                'created_at' => '2026-07-04 06:47:16'
            ],
            [
                'id' => 6,
                'judul' => 'Program Penghijauan Desa Bersama Komunitas Lokal',
                'kategori' => 'Lingkungan',
                'isi' => 'Koperasi bersama komunitas lokal melaksanakan penanaman 1000 pohon di area kritis untuk menjaga kelestarian lingkungan dan sumber air.',
                'penulis' => 'Admin',
                'gambar_url' => 'https://images.unsplash.com/photo-1502082553048-f009c37129b9?q=80&w=600&auto=format&fit=crop&q=80',
                'is_featured' => 0,
                'status' => 'tayang',
                'created_at' => '2026-07-04 06:47:17'
            ],
            [
                'id' => 7,
                'judul' => 'Ekspor Pertama Produk Olahan Pertanian ke Singapura',
                'kategori' => 'Ekspor',
                'isi' => 'Koperasi berhasil melakukan ekspor pertama produk olahan pertanian berupa keripik buah dan selai ke pasar Singapura.',
                'penulis' => 'Admin',
                'gambar_url' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=600&auto=format&fit=crop&q=80',
                'is_featured' => 0,
                'status' => 'tayang',
                'created_at' => '2026-07-04 06:47:18'
            ],
            // Add a 7th standard article (ID 8) so the button is clickable and loads this article dynamically
            [
                'id' => 8,
                'judul' => 'Peluang Usaha Baru di Bidang Peternakan',
                'kategori' => 'Peternakan',
                'isi' => 'Koperasi merambah ke sektor peternakan dengan memberikan pelatihan dan bantuan modal bergulir bagi peternak sapi perah lokal.',
                'penulis' => 'Admin',
                'gambar_url' => 'https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?q=80&w=600&auto=format&fit=crop&q=80',
                'is_featured' => 0,
                'status' => 'tayang',
                'created_at' => '2026-07-04 06:47:12' // Older date so it is on page 2 (to be loaded)
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('berita')
            ->where('id', 4)
            ->update(['status' => 'draft']);

        DB::table('berita')->whereIn('id', [5, 6, 7, 8])->delete();
    }
};
