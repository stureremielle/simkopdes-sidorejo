<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Shrinks admin.username to VARCHAR(20) and admin.password to VARCHAR(32).
     * MD5 hashes are always exactly 32 hex characters, so VARCHAR(32) is exact.
     */
    public function up(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            $table->string('username', 20)->change();
            $table->string('password', 32)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            $table->string('username', 30)->unique()->change();
            $table->string('password', 60)->change();
        });
    }
};
