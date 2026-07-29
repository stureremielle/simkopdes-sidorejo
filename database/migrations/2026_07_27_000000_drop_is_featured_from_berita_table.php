<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove is_featured column from berita table.
     * Featured article is now determined automatically by latest tanggal_publikasi.
     */
    public function up(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->tinyInteger('is_featured')->default(0)->after('gambar_url');
        });
    }
};
