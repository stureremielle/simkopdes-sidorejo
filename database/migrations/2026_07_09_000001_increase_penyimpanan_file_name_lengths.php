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
        Schema::table('penyimpanan_file', function (Blueprint $table) {
            $table->string('nama_file', 255)->change();
            $table->string('nama_asli', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyimpanan_file', function (Blueprint $table) {
            $table->string('nama_file', 80)->change();
            $table->string('nama_asli', 50)->change();
        });
    }
};
