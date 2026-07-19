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
        DB::table('pengaturan')->insertOrIgnore([
            [
                'key_name' => 'kategori_berita',
                'value' => json_encode(["Umum", "Pertanian", "Peternakan", "Perikanan", "Kerajinan", "Keuangan", "Lainnya"]),
            ],
            [
                'key_name' => 'kategori_galeri',
                'value' => json_encode(["Rapat & Musyawarah", "Panen & Pertanian", "Pelatihan", "Kegiatan Sosial", "Kegiatan"]),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('pengaturan')->whereIn('key_name', ['kategori_berita', 'kategori_galeri'])->delete();
    }
};
