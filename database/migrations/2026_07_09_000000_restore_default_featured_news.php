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
        $judul = 'Rapat Bulanan Petani Bahas Teknik Organik Baru';
        $excerpt = 'Koperasi kami baru saja mengadakan pertemuan bulanan untuk berbagi pengetahuan tentang pengendalian hama alami dan perbaikan kesehatan tanah tanpa pupuk kimia.';
        
        $longContent = "<p>Koperasi kami baru saja mengadakan pertemuan bulanan untuk berbagi pengetahuan tentang pengendalian hama alami dan perbaikan kesehatan tanah tanpa pupuk kimia.</p>\n\n<p>Koperasi Merah Putih baru saja mengadakan pertemuan bulanan yang dihadiri oleh lebih dari 50 anggota petani dari berbagai desa di wilayah kami. Pertemuan yang berlangsung di balai desa ini membahas berbagai topik penting seputar pertanian organik.</p>\n\n<h3>Pengendalian Hama Alami</h3>\n\n<p>Salah satu topik utama yang dibahas adalah teknik pengendalian hama secara alami tanpa menggunakan pestisida kimia. Para petani senior berbagi pengalaman mereka dalam menggunakan predator alami seperti burung hantu untuk mengendalikan populasi tikus, serta penggunaan tanaman pengusir hama seperti mimba dan serai di sekitar area pertanian.</p>\n\n<h3>Perbaikan Kesehatan Tanah</h3>\n\n<p>Sesi kedua fokus pada perbaikan kesehatan tanah melalui penggunaan kompos dan pupuk hijau. Pak Budi, seorang ahli pertanian organik dari universitas setempat, menjelaskan pentingnya menjaga mikrobioma tanah untuk meningkatkan kesuburan secara berkelanjutan.</p>\n\n<blockquote>\"Tanah yang sehat adalah fondasi dari pertanian yang sukses. Dengan mengembalikan kehidupan biologis tanah, kita tidak hanya meningkatkan hasil panen, tetapi juga menjaga lingkungan untuk generasi mendatang.\"</blockquote>\n\n<h3>Rencana Tindak Lanjut</h3>\n\n<p>Pertemuan ini menghasilkan beberapa kesepakatan penting, termasuk pembentukan kelompok studi lapangan untuk praktik pertanian organik, serta rencana kunjungan ke koperasi pertanian organik sukses di Jawa Tengah sebagai studi banding.</p>\n\n<p>Seluruh anggota sangat antusias dengan inisiatif ini dan berkomitmen untuk menerapkan teknik-teknik baru yang telah dipelajari pada musim tanam berikutnya.</p>";

        $exists = DB::table('berita')->where('judul', $judul)->first();

        if ($exists) {
            DB::table('berita')
                ->where('id', $exists->id)
                ->update([
                    'is_featured' => 1,
                    'status' => 'tayang',
                    'isi' => $longContent,
                    'kategori' => 'Pertanian',
                    'penulis' => 'Budi Santoso',
                    'gambar_url' => 'featured_office_lounge.png'
                ]);
        } else {
            DB::table('berita')->insert([
                'judul' => $judul,
                'kategori' => 'Pertanian',
                'isi' => $longContent,
                'penulis' => 'Budi Santoso',
                'gambar_url' => 'featured_office_lounge.png',
                'is_featured' => 1,
                'status' => 'tayang',
                'created_at' => '2023-10-12 09:00:00'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('berita')->where('judul', 'Rapat Bulanan Petani Bahas Teknik Organik Baru')->delete();
    }
};
