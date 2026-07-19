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
        // 1. Clean up old rows from pengaturan table
        DB::table('pengaturan')->whereIn('key_name', ['kategori_berita', 'kategori_galeri', 'kategori_layanan'])->delete();

        // 2. Create kategori_berita table
        Schema::create('kategori_berita', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->timestamps();
        });

        // Seed kategori_berita
        DB::table('kategori_berita')->insert([
            ['nama' => 'Umum', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Pertanian', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Peternakan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Perikanan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Kerajinan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Keuangan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Lainnya', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Create kategori_galeri table
        Schema::create('kategori_galeri', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->timestamps();
        });

        // Seed kategori_galeri
        DB::table('kategori_galeri')->insert([
            ['nama' => 'Rapat & Musyawarah', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Panen & Pertanian', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Pelatihan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Kegiatan Sosial', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Kegiatan', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Create kategori_layanan table
        Schema::create('kategori_layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->timestamps();
        });

        // Seed kategori_layanan
        DB::table('kategori_layanan')->insert([
            ['nama' => 'Pertanian', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Peternakan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Perikanan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Kerajinan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Keuangan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Lainnya', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_berita');
        Schema::dropIfExists('kategori_galeri');
        Schema::dropIfExists('kategori_layanan');
    }
};
