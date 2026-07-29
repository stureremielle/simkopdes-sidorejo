<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Anggota;

class AboutPageVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_and_displays_dynamic_member_distribution()
    {
        // 1. Clear database seeded members to isolate counts
        Anggota::truncate();

        // 2. Create active members with status='diterima'
        // Let's create members in RT 01 and RT 04
        Anggota::create([
            'nama_lengkap' => 'Active Member RT 01',
            'nik' => '1111111111111111',
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak No. 1',
            'rt' => 'RT 01',
            'dusun' => 'Dusun I',
            'no_hp' => '081234567890',
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA/SMK',
            'motivasi' => 'Motivasi',
            'status' => 'diterima',
        ]);

        Anggota::create([
            'nama_lengkap' => 'Active Member RT 04',
            'nik' => '2222222222222222',
            'jenis_kelamin' => 'Perempuan',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1995-10-15',
            'alamat_lengkap' => 'Jl. Merak No. 4',
            'rt' => 'RT 04',
            'dusun' => 'Dusun II',
            'no_hp' => '089876543210',
            'pekerjaan' => 'Pedagang',
            'pendidikan' => 'D3',
            'motivasi' => 'Motivasi',
            'status' => 'diterima',
        ]);

        // Create a pending member (should not be counted)
        Anggota::create([
            'nama_lengkap' => 'Pending Member',
            'nik' => '3333333333333333',
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1992-05-15',
            'alamat_lengkap' => 'Jl. Merak No. 5',
            'rt' => 'RT 01',
            'dusun' => 'Dusun I',
            'no_hp' => '081234567891',
            'pekerjaan' => 'Swasta',
            'pendidikan' => 'S1',
            'motivasi' => 'Motivasi',
            'status' => 'menunggu',
        ]);

        // Create a member with status='diterima' but using alternative 'RT01' (no space) formatting to test normalization
        Anggota::create([
            'nama_lengkap' => 'Active Member RT 01 Alternative formatting',
            'nik' => '4444444444444444',
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak No. 1 Alternative',
            'rt' => 'RT01',
            'dusun' => 'DI', // Alternate dusun format
            'no_hp' => '081234567892',
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA/SMK',
            'motivasi' => 'Motivasi',
            'status' => 'diterima',
        ]);

        // Total active members in db: 3 (2 in RT 01, 1 in RT 04)
        // Total RTs: 2 unique active RTs (RT 01 and RT 04), min RTs to show is 8.
        // Anggota Baru (current year): 3

        // 3. Make request
        $response = $this->get('/tentang-kami');

        $response->assertStatus(200);

        // 4. Assert summary metrics exist in target HTML structure
        $response->assertSee('3'); // Total Anggota
        $response->assertSee('8'); // Max/Jumlah RT is 8 (since unique counted RTs is 2, fallback is 8)
        $response->assertSee('89%'); // Keaktifan (remains static)

        // 5. Assert RT Progress Grid totals and label groups exist
        // RT 01: count=2, pct=round(2/3*100) = 67%
        $response->assertSee('RT 01');
        $response->assertSee('Dusun I');
        $response->assertSee('2'); // count for RT 01
        $response->assertSee('67%'); // pct for RT 01

        // RT 04: count=1, pct=round(1/3*100) = 33%
        $response->assertSee('RT 04');
        $response->assertSee('Dusun II');
        $response->assertSee('1'); // count for RT 04
        $response->assertSee('33%'); // pct for RT 04

        // RT 02: count=0, pct=0%
        $response->assertSee('RT 02');
        $response->assertSee('0'); // count for RT 02
        $response->assertSee('0%'); // pct for RT 02
    }

    /** @test */
    public function it_displays_custom_visi_setting_dynamically()
    {
        \App\Models\Pengaturan::updateOrCreate(
            ['key_name' => 'visi'],
            ['value' => 'Visi Koperasi Keren Abis']
        );

        $response = $this->get('/tentang-kami');

        $response->assertStatus(200);
        $response->assertSee('Visi Koperasi Keren Abis');
    }

    /** @test */
    public function it_displays_custom_misi_setting_dynamically()
    {
        \App\Models\Pengaturan::updateOrCreate(
            ['key_name' => 'misi'],
            ['value' => 'Misi Khusus Satu|Misi Khusus Dua']
        );

        $response = $this->get('/tentang-kami');

        $response->assertStatus(200);
        $response->assertSee('Misi Khusus Satu');
        $response->assertSee('Misi Khusus Dua');
    }

    /** @test */
    public function it_displays_custom_nama_koperasi_and_alamat_setting_dynamically()
    {
        \App\Models\Pengaturan::updateOrCreate(
            ['key_name' => 'nama_koperasi'],
            ['value' => 'Koperasi Desa Mulia Abadi']
        );

        \App\Models\Pengaturan::updateOrCreate(
            ['key_name' => 'alamat'],
            ['value' => 'Jl. Melati No 12 Dusun I Desa Sidorejo, Penajam']
        );

        $response = $this->get('/tentang-kami');

        $response->assertStatus(200);
        $response->assertSee('Tentang Koperasi Desa Mulia Abadi');
        $response->assertSee('Koperasi Desa Mulia Abadi');
        $response->assertSee('Jl. Melati No 12 Dusun I Desa Sidorejo, Penajam');
    }
}
