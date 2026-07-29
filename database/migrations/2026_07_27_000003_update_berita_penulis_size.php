<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adjusts berita.penulis from VARCHAR(50) to VARCHAR(20).
     * All existing values are ≤ 7 chars, so no data is affected.
     */
    public function up(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->string('penulis', 20)->default('Admin')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->string('penulis', 50)->default('Admin')->change();
        });
    }
};
