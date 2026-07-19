<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('pengaturan')->where('key_name', 'visi')->update([
            'value' => 'Menjadi koperasi agribisnis terdepan yang berbasis masyarakat, berkelanjutan, berdaya saing dan memberikan manfaat bagi masyarakat luas, dengan mengintegrasikan ekonomi, pendidikan, dan keberlanjutan lingkungan.'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('pengaturan')->where('key_name', 'visi')->update([
            'value' => 'Menjadi koperasi desa terdepan yang mampu meningkatkan kesejahteraan anggota dan masyarakat Desa Sidorejo secara berkelanjutan.'
        ]);
    }
};
