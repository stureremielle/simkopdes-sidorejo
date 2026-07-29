<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adjusts anggota column sizes to match spec:
     *   nama_lengkap  50  → 40
     *   alamat_lengkap 100 → 80
     *   dusun          15  →  8
     *   pekerjaan      30  → 20
     *   jabatan        30  → 20
     */
    public function up(): void
    {
        // Truncate any existing dusun values longer than 8 chars before resizing column
        // Note: 'Dusun I' = 7, 'Dusun II' = 8, 'Dusun III' = 9 (needs truncation)
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE anggota SET dusun = SUBSTR(dusun, 1, 8) WHERE LENGTH(dusun) > 8"
        );

        Schema::table('anggota', function (Blueprint $table) {
            $table->string('nama_lengkap', 40)->change();
            $table->string('alamat_lengkap', 80)->change();
            $table->string('dusun', 8)->nullable()->change();
            $table->string('pekerjaan', 20)->nullable()->change();
            $table->string('jabatan', 20)->default('Anggota')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->string('nama_lengkap', 50)->change();
            $table->string('alamat_lengkap', 100)->change();
            $table->string('dusun', 15)->nullable()->change();
            $table->string('pekerjaan', 30)->nullable()->change();
            $table->string('jabatan', 30)->default('Anggota')->change();
        });
    }
};
