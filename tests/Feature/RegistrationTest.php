<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Anggota;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_registration_with_duplicate_nik()
    {
        // Clear seeded data from migration to isolate the test count
        Anggota::truncate();

        // 1. Create a member with a specific NIK
        Anggota::create([
            'nama_lengkap' => 'Existing Member',
            'nik' => '1234567890123456',
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak No. 5',
            'rt' => 'RT 06',
            'dusun' => 'Dusun II',
            'no_hp' => '081234567890',
            'email' => 'existing@mail.com',
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA/SMK',
            'motivasi' => 'Ingin meningkatkan kesejahteraan',
            'status' => 'menunggu',
        ]);

        // 2. Submit another registration with the same NIK
        $response = $this->post(route('daftar.store'), [
            'namaLengkap' => 'New Member',
            'nikKtp' => '1234567890123456', // Same NIK
            'jenisKelamin' => 'Perempuan',
            'tempatLahir' => 'Penajam',
            'tanggalLahir' => '1995-10-15',
            'alamatLengkap' => 'Jl. Kupu No. 3',
            'rtSelect' => 'RT 07',
            'dusunSelect' => 'Dusun II',
            'noHp' => '089876543210',
            'email' => 'new@mail.com',
            'pekerjaan' => 'Pedagang',
            'pendidikan' => 'D3',
            'motivasi' => 'Ingin meminjam modal untuk toko kelontong',
        ]);

        // 3. Assert successful redirect and registration
        $response->assertRedirect(route('daftar'));
        $response->assertSessionHasNoErrors();

        // 4. Verify that two anggota exist offline in the database
        $this->assertEquals(2, Anggota::count());
    }
}
