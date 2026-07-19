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
        // 1. Create Kategori Penyimpanan table
        Schema::create('kategori_penyimpanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->timestamps();
        });

        // Seed default file storage categories
        $defaultFileCategories = ['Legalitas', 'Laporan', 'Keuangan', 'Keanggotaan', 'Lainnya'];
        foreach ($defaultFileCategories as $cat) {
            DB::table('kategori_penyimpanan')->insert([
                'nama' => $cat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Add kategori_id (unsignedBigInteger/nullable) to all 4 tables
        Schema::table('berita', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable()->after('kategori');
        });

        Schema::table('galeri', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable()->after('kategori');
        });

        Schema::table('layanan', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable()->after('kategori');
        });

        Schema::table('penyimpanan_file', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable()->after('kategori');
        });

        // 3. Migrate data from old kategori string to new kategori_id relationship
        // Berita
        $dbBerita = DB::table('berita')->get();
        foreach ($dbBerita as $row) {
            if ($row->kategori) {
                $category = DB::table('kategori_berita')->where('nama', $row->kategori)->first();
                if (!$category) {
                    $catId = DB::table('kategori_berita')->insertGetId([
                        'nama' => $row->kategori,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $catId = $category->id;
                }
                DB::table('berita')->where('id', $row->id)->update(['kategori_id' => $catId]);
            }
        }

        // Galeri
        $dbGaleri = DB::table('galeri')->get();
        foreach ($dbGaleri as $row) {
            if ($row->kategori) {
                $category = DB::table('kategori_galeri')->where('nama', $row->kategori)->first();
                if (!$category) {
                    $catId = DB::table('kategori_galeri')->insertGetId([
                        'nama' => $row->kategori,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $catId = $category->id;
                }
                DB::table('galeri')->where('id', $row->id)->update(['kategori_id' => $catId]);
            }
        }

        // Layanan
        $dbLayanan = DB::table('layanan')->get();
        foreach ($dbLayanan as $row) {
            if ($row->kategori) {
                $category = DB::table('kategori_layanan')->where('nama', $row->kategori)->first();
                if (!$category) {
                    $catId = DB::table('kategori_layanan')->insertGetId([
                        'nama' => $row->kategori,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $catId = $category->id;
                }
                DB::table('layanan')->where('id', $row->id)->update(['kategori_id' => $catId]);
            }
        }

        // Penyimpanan File
        $dbPenyimpanan = DB::table('penyimpanan_file')->get();
        foreach ($dbPenyimpanan as $row) {
            if ($row->kategori) {
                $category = DB::table('kategori_penyimpanan')->where('nama', $row->kategori)->first();
                if (!$category) {
                    $catId = DB::table('kategori_penyimpanan')->insertGetId([
                        'nama' => $row->kategori,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $catId = $category->id;
                }
                DB::table('penyimpanan_file')->where('id', $row->id)->update(['kategori_id' => $catId]);
            }
        }

        // 4. Define foreign keys & Drop the original string kategori columns
        Schema::table('berita', function (Blueprint $table) {
            $table->foreign('kategori_id')->references('id')->on('kategori_berita')->onDelete('restrict');
            $table->dropColumn('kategori');
        });

        Schema::table('galeri', function (Blueprint $table) {
            $table->foreign('kategori_id')->references('id')->on('kategori_galeri')->onDelete('restrict');
            $table->dropColumn('kategori');
        });

        Schema::table('layanan', function (Blueprint $table) {
            $table->foreign('kategori_id')->references('id')->on('kategori_layanan')->onDelete('restrict');
            $table->dropColumn('kategori');
        });

        Schema::table('penyimpanan_file', function (Blueprint $table) {
            $table->foreign('kategori_id')->references('id')->on('kategori_penyimpanan')->onDelete('restrict');
            $table->dropColumn('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Restore legacy string kategori columns
        Schema::table('berita', function (Blueprint $table) {
            $table->string('kategori', 30)->default('Umum')->after('kategori_id');
        });
        Schema::table('galeri', function (Blueprint $table) {
            $table->string('kategori', 30)->default('Umum')->after('kategori_id');
        });
        Schema::table('layanan', function (Blueprint $table) {
            $table->string('kategori', 30)->after('kategori_id');
        });
        Schema::table('penyimpanan_file', function (Blueprint $table) {
            $table->string('kategori', 30)->default('Umum')->after('kategori_id');
        });

        // 2. Map data back matching name strings
        // Berita
        $dbBerita = DB::table('berita')->get();
        foreach ($dbBerita as $row) {
            if ($row->kategori_id) {
                $category = DB::table('kategori_berita')->where('id', $row->kategori_id)->first();
                if ($category) {
                    DB::table('berita')->where('id', $row->id)->update(['kategori' => $category->nama]);
                }
            }
        }
        // Galeri
        $dbGaleri = DB::table('galeri')->get();
        foreach ($dbGaleri as $row) {
            if ($row->kategori_id) {
                $category = DB::table('kategori_galeri')->where('id', $row->kategori_id)->first();
                if ($category) {
                    DB::table('galeri')->where('id', $row->id)->update(['kategori' => $category->nama]);
                }
            }
        }
        // Layanan
        $dbLayanan = DB::table('layanan')->get();
        foreach ($dbLayanan as $row) {
            if ($row->kategori_id) {
                $category = DB::table('kategori_layanan')->where('id', $row->kategori_id)->first();
                if ($category) {
                    DB::table('layanan')->where('id', $row->id)->update(['kategori' => $category->nama]);
                }
            }
        }
        // Penyimpanan file
        $dbPenyimpanan = DB::table('penyimpanan_file')->get();
        foreach ($dbPenyimpanan as $row) {
            if ($row->kategori_id) {
                $category = DB::table('kategori_penyimpanan')->where('id', $row->kategori_id)->first();
                if ($category) {
                    DB::table('penyimpanan_file')->where('id', $row->id)->update(['kategori' => $category->nama]);
                }
            }
        }

        // 3. Drop foreign keys and kategori_id columns
        Schema::table('berita', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
        });

        Schema::table('galeri', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
        });

        Schema::table('layanan', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
        });

        Schema::table('penyimpanan_file', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
        });

        // 4. Drop Kategori Penyimpanan table
        Schema::dropIfExists('kategori_penyimpanan');
    }
};
