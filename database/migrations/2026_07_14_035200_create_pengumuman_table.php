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
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 80);
            $table->text('isi');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->timestamps();
        });

        // Insert initial data matching the guidelines and requirements
        \DB::table('pengumuman')->insert([
            [
                'judul' => 'Koperasi Merah Putih Desa Sidorejo libur operasional.',
                'isi' => '<p>Sehubungan dengan libur bersama dan pemeliharaan sistem berkala, Koperasi Merah Putih Desa Sidorejo akan libur operasional mulai tanggal 20 Juli hingga 30 Juli 2026.</p><p>Seluruh layanan simpan pinjam dan administrasi kantor akan dinonaktifkan sementara selama periode tersebut dan akan kembali aktif seperti biasa pada hari Senin, 31 Juli 2026.</p><p>Terima kasih atas pengertian Bapak/Ibu anggota koperasi sekalian.</p>',
                'tanggal_mulai' => '2026-07-20',
                'tanggal_selesai' => '2026-07-30',
                'status' => 'Aktif',
                'created_at' => '2026-07-14 08:00:00',
                'updated_at' => '2026-07-14 08:00:00',
            ],
            [
                'judul' => 'Pendaftaran anggota baru masih dibuka.',
                'isi' => '<p>Kami mengundang seluruh warga Desa Sidorejo yang ingin bergabung menjadi anggota Koperasi Merah Putih Desa Sidorejo. Pendaftaran anggota baru masih dibuka secara resmi hingga tanggal 3 Mei 2027.</p><p>Pendaftaran dapat dilakukan dengan mengisi form pendaftaran online di website ini melalui menu "Daftar" atau mengunjungi kantor pelayanan koperasi dengan membawa KTP dan persyaratan lainnya.</p><p>Tumbuh bersama koperasi menuju masa depan berkelanjutan!</p>',
                'tanggal_mulai' => '2026-07-13',
                'tanggal_selesai' => '2027-05-03',
                'status' => 'Aktif',
                'created_at' => '2026-07-13 09:00:00',
                'updated_at' => '2026-07-13 09:00:00',
            ],
            [
                'judul' => 'Rapat Anggota Tahunan Khusus.',
                'isi' => '<p>Pemberitahuan kepada seluruh anggota aktif Koperasi Merah Putih Desa Sidorejo bahwa akan diselenggarakan Rapat Anggota Tahunan (RAT) Khusus membahas penyesuaian kontribusi anggota dan pembagian sisa hasil usaha kuartal kedua.</p><p>Rapat akan dilaksanakan pada hari Sabtu, 15 Agustus 2026 bertempat di Balai Pertemuan Desa Sidorejo mulai pukul 09.00 WITA. Diharapkan kehadiran seluruh anggota demi kelancaran musyawarah.</p>',
                'tanggal_mulai' => '2026-08-15',
                'tanggal_selesai' => null,
                'status' => 'Aktif',
                'created_at' => '2026-07-12 10:00:00',
                'updated_at' => '2026-07-12 10:00:00',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
