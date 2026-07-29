<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Makes tanggal_publikasi column in berita table NOT NULL with data sanitization.
     */
    public function up(): void
    {
        // 1. Data sanitization: fill any legacy NULL tanggal_publikasi with DATE(created_at) or current date
        DB::table('berita')
            ->whereNull('tanggal_publikasi')
            ->update([
                'tanggal_publikasi' => DB::raw('COALESCE(DATE(created_at), CURRENT_DATE)')
            ]);

        // 2. Change column constraint to NOT NULL (nullable = false)
        Schema::table('berita', function (Blueprint $table) {
            $table->date('tanggal_publikasi')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->date('tanggal_publikasi')->nullable()->change();
        });
    }
};
