<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Resize nama_asli to VARCHAR(150) and nama_file to VARCHAR(180)
     * based on realistic administrative document naming needs.
     */
    public function up(): void
    {
        Schema::table('penyimpanan_file', function (Blueprint $table) {
            $table->string('nama_asli', 150)->change();
            $table->string('nama_file', 180)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyimpanan_file', function (Blueprint $table) {
            $table->string('nama_asli', 255)->change();
            $table->string('nama_file', 255)->change();
        });
    }
};
