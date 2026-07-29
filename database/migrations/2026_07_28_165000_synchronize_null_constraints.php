<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Synchronizes database NULL/NOT NULL constraints with current application implementation:
     * - layanan: deskripsi (required in forms & controllers -> NOT NULL)
     */
    public function up(): void
    {
        Schema::table('layanan', function (Blueprint $table) {
            $table->text('deskripsi')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('layanan', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->change();
        });
    }
};
