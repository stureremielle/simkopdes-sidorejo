<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Synchronizes NOT NULL constraints across anggota, layanan, and penyimpanan_file tables
     * to match application validation and frontend form requirements.
     */
    public function up(): void
    {
        // 1. Data sanitization before changing column constraints
        DB::table('anggota')->whereNull('rt')->update(['rt' => 'RT 01']);
        DB::table('anggota')->whereNull('dusun')->update(['dusun' => 'Dusun I']);
        DB::table('anggota')->whereNull('pekerjaan')->update(['pekerjaan' => '-']);
        DB::table('anggota')->whereNull('pendidikan')->update(['pendidikan' => '-']);
        DB::table('anggota')->whereNull('motivasi')->update(['motivasi' => '-']);

        DB::table('layanan')->whereNull('deskripsi')->update(['deskripsi' => '-']);

        DB::table('penyimpanan_file')->whereNull('keterangan')->update(['keterangan' => '-']);

        // 2. Modify columns to NOT NULL (nullable = false)
        Schema::table('anggota', function (Blueprint $table) {
            $table->string('rt', 5)->nullable(false)->change();
            $table->string('dusun', 8)->nullable(false)->change();
            $table->string('pekerjaan', 20)->nullable(false)->change();
            $table->string('pendidikan', 10)->nullable(false)->change();
            $table->text('motivasi')->nullable(false)->change();
        });

        Schema::table('layanan', function (Blueprint $table) {
            $table->text('deskripsi')->nullable(false)->change();
        });

        Schema::table('penyimpanan_file', function (Blueprint $table) {
            $table->text('keterangan')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->string('rt', 5)->nullable()->change();
            $table->string('dusun', 8)->nullable()->change();
            $table->string('pekerjaan', 20)->nullable()->change();
            $table->string('pendidikan', 10)->nullable()->change();
            $table->text('motivasi')->nullable()->change();
        });

        Schema::table('layanan', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->change();
        });

        Schema::table('penyimpanan_file', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->change();
        });
    }
};
