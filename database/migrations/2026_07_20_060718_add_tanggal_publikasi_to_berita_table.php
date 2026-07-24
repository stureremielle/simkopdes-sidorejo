<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->date('tanggal_publikasi')->nullable()->after('status');
        });

        // Set default value for existing records
        \Illuminate\Support\Facades\DB::table('berita')
            ->whereNull('tanggal_publikasi')
            ->update([
                'tanggal_publikasi' => \Illuminate\Support\Facades\DB::raw('DATE(created_at)')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->dropColumn('tanggal_publikasi');
        });
    }
};
