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
        // 1. Table: admin
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('username', 30)->unique();
            $table->string('password', 60);
            $table->timestamp('created_at')->useCurrent();
        });

        // 2. Table: anggota
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 50);
            $table->char('nik', 16);
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan']);
            $table->string('tempat_lahir', 25);
            $table->date('tanggal_lahir');
            $table->string('alamat_lengkap', 100);
            $table->string('rt', 5)->nullable();
            $table->string('dusun', 15)->nullable();
            $table->string('no_hp', 15);
            $table->string('email', 35)->nullable();
            $table->string('pekerjaan', 30)->nullable();
            $table->string('pendidikan', 10)->nullable();
            $table->text('motivasi')->nullable();
            $table->string('jabatan', 30)->default('Anggota');
            $table->string('sumber', 15)->default('Pendaftaran');
            $table->enum('status', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->timestamp('created_at')->useCurrent();
        });

        // 3. Table: layanan
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->string('kategori', 30);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2)->default(0.00);
            $table->string('satuan', 10)->default('unit');
            $table->string('gambar_url', 255)->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamp('created_at')->useCurrent();
        });

        // 4. Table: berita
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 80);
            $table->string('kategori', 30)->default('Umum');
            $table->text('isi');
            $table->string('penulis', 50)->default('Admin');
            $table->string('gambar_url', 255)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['tayang', 'draft'])->default('tayang');
            $table->timestamp('created_at')->useCurrent();
        });

        // 5. Table: galeri
        Schema::create('galeri', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 50);
            $table->string('kategori', 30)->default('Umum');
            $table->string('gambar_url', 255);
            $table->string('materi_url', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamp('created_at')->useCurrent();
        });

        // 6. Table: penyimpanan_file
        Schema::create('penyimpanan_file', function (Blueprint $table) {
            $table->id();
            $table->string('nama_file', 255);
            $table->string('nama_asli', 255);
            $table->string('kategori', 30)->default('Umum');
            $table->integer('ukuran')->default(0);
            $table->string('tipe', 80)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
        });

        // 7. Table: pengaturan
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('key_name', 50)->unique();
            $table->text('value')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        // Insert initial data
        DB::table('admin')->insert([
            'username' => 'admin',
            'password' => md5('admin123'), // MD5 to match native demo
        ]);

        DB::table('anggota')->insert([
            [
                'nama_lengkap' => 'Ahmad Fauzi',
                'nik' => '3401010101010001',
                'jenis_kelamin' => 'Laki-Laki',
                'tempat_lahir' => 'Penajam',
                'tanggal_lahir' => '1990-05-15',
                'alamat_lengkap' => 'Jl. Merak No. 5',
                'rt' => 'RT 06',
                'dusun' => 'Dusun II',
                'no_hp' => '081234567890',
                'email' => 'ahmad@mail.com',
                'pekerjaan' => 'Petani',
                'pendidikan' => 'SMA/SMK',
                'motivasi' => 'Ingin meningkatkan kesejahteraan',
                'status' => 'menunggu',
            ],
            [
                'nama_lengkap' => 'Rina Susanti',
                'nik' => '3401010101010002',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Balikpapan',
                'tanggal_lahir' => '1992-08-20',
                'alamat_lengkap' => 'Jl. Kupu No. 3',
                'rt' => 'RT 07',
                'dusun' => 'Dusun III',
                'no_hp' => '089876543210',
                'email' => 'rina@mail.com',
                'pekerjaan' => 'Pedagang',
                'pendidikan' => 'D3',
                'motivasi' => 'Ingin bergabung komunitas koperasi',
                'status' => 'menunggu',
            ],
        ]);

        DB::table('layanan')->insert([
            [
                'nama' => 'Padi Unggulan',
                'kategori' => 'Pertanian',
                'deskripsi' => 'Beras padi berkualitas dari hasil panen lokal dengan metode pertanian organik.',
                'harga' => 12000,
                'satuan' => 'kg',
                'gambar_url' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&fit=crop&q=80',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Sapi Ternak',
                'kategori' => 'Peternakan',
                'deskripsi' => 'Sapi ternak sehat dengan perawatan dan pakan berkualitas dari peternak lokal.',
                'harga' => 15000000,
                'satuan' => 'ekor',
                'gambar_url' => 'https://images.unsplash.com/photo-1570042225831-d98fa7577f1e?w=400&fit=crop&q=80',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Ayam Kampung',
                'kategori' => 'Peternakan',
                'deskripsi' => 'Ayam kampung sehat dari peternakan lokal, bebas penyakit dan terawat dengan baik.',
                'harga' => 75000,
                'satuan' => 'ekor',
                'gambar_url' => 'https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?w=400&fit=crop&q=80',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Ikan Lele Segar',
                'kategori' => 'Perikanan',
                'deskripsi' => 'Ikan lele segar hasil budidaya lokal, kaya protein dan dipanen setiap hari.',
                'harga' => 25000,
                'satuan' => 'kg',
                'gambar_url' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400&fit=crop&q=80',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Simpan Pinjam',
                'kategori' => 'Keuangan',
                'deskripsi' => 'Layanan simpan pinjam dengan bunga rendah untuk anggota aktif koperasi.',
                'harga' => 0,
                'satuan' => 'paket',
                'gambar_url' => 'https://images.unsplash.com/photo-1628157582853-a796fa650a6a?w=600&fit=crop&q=80',
                'status' => 'aktif',
            ],
        ]);

        DB::table('berita')->insert([
            [
                'judul' => 'Rapat Bulanan Petani Bahas Teknik Organik Baru',
                'kategori' => 'Pertanian',
                'isi' => 'Koperasi kami baru saja mengadakan pertemuan bulanan untuk berbagi pengetahuan tentang pengendalian hama alami dan perbaikan kesehatan tanah tanpa pupuk kimia.',
                'penulis' => 'Budi Santoso',
                'gambar_url' => 'featured_office_lounge.png',
                'is_featured' => 1,
                'status' => 'tayang',
            ],
            [
                'judul' => 'Musim Panen Melampaui Harapan',
                'kategori' => 'Pertanian',
                'isi' => 'Berkat cuaca yang mendukung dan upaya kolektif, hasil panen padi musim ini melampaui proyeksi awal kami sebesar 15%.',
                'penulis' => 'Admin',
                'gambar_url' => 'https://images.unsplash.com/photo-1508962914676-134849a727f0?q=80&w=600&auto=format&fit=crop&q=80',
                'is_featured' => 0,
                'status' => 'tayang',
            ],
            [
                'judul' => 'Lokakarya Kerajinan Tangan Baru Dibuka untuk Perempuan',
                'kategori' => 'Kerajinan',
                'isi' => 'Inisiatif baru untuk memberdayakan perempuan lokal melalui tenun tradisional dan pembuatan batik telah resmi diluncurkan.',
                'penulis' => 'Admin',
                'gambar_url' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=600&auto=format&fit=crop&q=80',
                'is_featured' => 0,
                'status' => 'tayang',
            ],
            [
                'judul' => 'Koperasi Bagikan Sisa Hasil Usaha (SHU) Tahunan',
                'kategori' => 'Keuangan',
                'isi' => 'Seluruh anggota Koperasi Merah Putih yang terdaftar telah menerima pembagian SHU tahunan berdasarkan partisipasi mereka.',
                'penulis' => 'Admin',
                'gambar_url' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?q=80&w=600&auto=format&fit=crop&q=80',
                'is_featured' => 0,
                'status' => 'draft',
            ],
        ]);

        DB::table('galeri')->insert([
            [
                'judul' => 'Rapat Anggota Tahunan 2024',
                'kategori' => 'Rapat & Musyawarah',
                'gambar_url' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=800&auto=format&fit=crop',
                'keterangan' => 'Penyampaian Laporan Pertanggungjawaban pengurus Koperasi Desa Merah Putih Sidorejo.',
                'materi_url' => 'Notulen_RAT_2024.pdf',
                'status' => 'aktif',
                'created_at' => '2024-12-01 09:00:00',
            ],
            [
                'judul' => 'Panen Padi Musim Gadu',
                'kategori' => 'Panen & Pertanian',
                'gambar_url' => 'https://images.unsplash.com/photo-1569880153113-76e33fc52d5f?q=80&w=800&auto=format&fit=crop',
                'keterangan' => 'Kegiatan panen raya padi varietas unggul hasil binaan koperasi.',
                'materi_url' => null,
                'status' => 'aktif',
                'created_at' => '2024-09-01 09:00:00',
            ],
            [
                'judul' => 'Pelatihan Agribisnis Anggota Baru',
                'kategori' => 'Pelatihan',
                'gambar_url' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=800&auto=format&fit=crop',
                'keterangan' => 'Pelatihan intensif pengembangan unit usaha agribisnis bagi anggota baru.',
                'materi_url' => 'Materi_Pelatihan_Agribisnis.pdf',
                'status' => 'aktif',
                'created_at' => '2024-08-01 09:00:00',
            ],
            [
                'judul' => 'Bakti Sosial Anggota Koperasi',
                'kategori' => 'Kegiatan Sosial',
                'gambar_url' => 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?q=80&w=800&auto=format&fit=crop',
                'keterangan' => 'Kegiatan donor darah dan bakti sosial kemanusiaan oleh anggota koperasi.',
                'materi_url' => null,
                'status' => 'aktif',
                'created_at' => '2024-06-01 09:00:00',
            ],
            [
                'judul' => 'Pengembangan Hortikultura Desa',
                'kategori' => 'Panen & Pertanian',
                'gambar_url' => 'https://images.unsplash.com/photo-1610832958506-ee56336191a1?q=80&w=800&auto=format&fit=crop',
                'keterangan' => 'Pembinaan budidaya hortikultura buah-buahan lokal untuk meningkatkan pendapatan petani.',
                'materi_url' => null,
                'status' => 'aktif',
                'created_at' => '2024-04-01 09:00:00',
            ],
            [
                'judul' => 'Rapat Kerja & Rencana Anggaran',
                'kategori' => 'Rapat & Musyawarah',
                'gambar_url' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?q=80&w=800&auto=format&fit=crop',
                'keterangan' => 'Rapat kerja penyusunan program dan rencana anggaran tahunan koperasi.',
                'materi_url' => 'Program_Kerja_2024.pdf',
                'status' => 'aktif',
                'created_at' => '2024-03-01 09:00:00',
            ],
            [
                'judul' => 'Rapat Koordinasi Pengurus',
                'kategori' => 'Rapat & Musyawarah',
                'gambar_url' => 'https://images.unsplash.com/photo-1531535934027-667f687cada1?q=80&w=800&auto=format&fit=crop',
                'keterangan' => 'Rapat koordinasi berkala antara pengurus dan pengelola koperasi.',
                'materi_url' => 'Rencana_Kerja_Pengurus.pdf',
                'status' => 'aktif',
                'created_at' => '2024-02-01 09:00:00',
            ],
            [
                'judul' => 'Pelatihan Budidaya Sayuran',
                'kategori' => 'Pelatihan',
                'gambar_url' => 'https://images.unsplash.com/photo-1592417817098-8f3d6eb19675?q=80&w=800&auto=format&fit=crop',
                'keterangan' => 'Penyuluhan optimalisasi lahan perkarangan untuk sayuran organik.',
                'materi_url' => null,
                'status' => 'aktif',
                'created_at' => '2023-11-01 09:00:00',
            ],
            [
                'judul' => 'Pelatihan Keuangan Koperasi',
                'kategori' => 'Pelatihan',
                'gambar_url' => 'https://images.unsplash.com/photo-1554224155-6f9664d00d3d?q=80&w=800&auto=format&fit=crop',
                'keterangan' => 'Pembekalan literasi dan pengelolaan laporan keuangan bagi anggota.',
                'materi_url' => 'Modul_Pelatihan_Keuangan.pdf',
                'status' => 'aktif',
                'created_at' => '2023-10-01 09:00:00',
            ]
        ]);

        DB::table('pengaturan')->insert([
            ['key_name' => 'nama_koperasi', 'value' => 'Koperasi Desa Merah Putih Sidorejo'],
            ['key_name' => 'alamat', 'value' => 'Jl. Pariwisata RT 04 Dusun II Desa Sidorejo, Kec. Penajam, Kab. Penajam Paser Utara, Kalimantan Timur'],
            ['key_name' => 'telepon', 'value' => '+62 812 3456 7890'],
            ['key_name' => 'email', 'value' => 'info@merahputih.co.id'],
            ['key_name' => 'visi', 'value' => 'Menjadi koperasi desa terdepan yang mampu meningkatkan kesejahteraan anggota dan masyarakat Desa Sidorejo secara berkelanjutan.'],
            ['key_name' => 'misi', 'value' => '[{"title":"Memperluas Kemitraan dan Pasar","items":["Memperluas jaringan kemitraan dengan kelompok tani dan pelaku usaha (UMKM) dan koperasi lain untuk menciptakan ekosistem ekonomi yang kokoh.","Memperluas jaringan pasar untuk memastikan hasil pertanian memiliki akses ke pasar yg lebih luas, sehingga dapat meningkatkan penjualan bagi koperasi serta para mitra petani, pelaku UMKM dan koperasi lainnya."]},{"title":"Penguatan Kelembagaan","items":["Mengembangkan tata kelola koperasi yg transparan, profesional, terpercaya dan terintegrasi.","Memperkuat kemandirian koperasi dalam meningkatkan kesejahteraan anggota dan masyarakat sekitar."]},{"title":"Agrowisata & Pemancingan","items":["Menyediakan program agrowisata yg inovatif dan edukatif.","Mengembangkan fasilitas dan kegiatan wisata berbasis agribisnis yg menarik dan mendukung pembelajaran praktis."]},{"title":"Pengelolaan Sampah Berkelanjutan","items":["Mewujudkan masyarakat yang sadar lingkungan dan peduli terhadap pengelolaan Sampah.","Menciptakan sistem pengelolaan Sampah yang efisien, terintegrasi dan berkelanjutan. Mulai dari pemilahan dari Rumah, Pengumpulan, Pengolahan, hingga pemanfaatan kembali.","Meningkatkan nilai ekonomi sampah untuk mengurangi jumlah sampah, misal daur ulang kerajinan atau kompos.","Menekankan pentingnya kerja sama antara pemerintah, masyarakat, dunia usaha, swadaya masyarakat dalam pengelolaan Sampah.","Pemantauan dan evaluasi kinerja pengolahan sampah untuk memastikan sistem berjalan sesuai target dan dilakukan perbaikan terus menerus."]},{"title":"Klinik Bisnis & Magang","items":["Menyelenggarakan pelatihan agribisnis dan UMKM untuk menciptakan sumber daya manusia yang kompeten dan berdaya saing.","Membuka program magang bagi generasi muda yang ingin mendalami agribisnis dan UMKM."]}]'],
            ['key_name' => 'hero_background', 'value' => 'hero_1783162777.jpg'],
            ['key_name' => 'org_chart', 'value' => 'org_1783318221.jpg']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
        Schema::dropIfExists('penyimpanan_file');
        Schema::dropIfExists('galeri');
        Schema::dropIfExists('berita');
        Schema::dropIfExists('layanan');
        Schema::dropIfExists('anggota');
        Schema::dropIfExists('admin');
    }
};
