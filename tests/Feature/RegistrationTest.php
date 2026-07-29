<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Anggota;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_denies_registration_with_duplicate_nik()
    {
        // Clear seeded data from migration to isolate the test count
        Anggota::truncate();

        // 1. Create an existing member
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
            'nikKtp' => '1234567890123456', // Duplicate NIK
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

        // 3. Assert session has error for nikKtp with "NIK sudah terdaftar."
        $response->assertSessionHasErrors(['nikKtp' => 'NIK sudah terdaftar.']);

        // 4. Verify that only 1 anggota exists in the database
        $this->assertEquals(1, Anggota::count());
    }

    /** @test */
    public function it_allows_registration_with_unique_nik()
    {
        Anggota::truncate();

        $response = $this->post(route('daftar.store'), [
            'namaLengkap' => 'Unique Member',
            'nikKtp' => '9999888877776666',
            'jenisKelamin' => 'Laki-Laki',
            'tempatLahir' => 'Penajam',
            'tanggalLahir' => '1992-08-20',
            'alamatLengkap' => 'Jl. Anggrek No. 12',
            'rtSelect' => 'RT01',
            'dusunSelect' => 'Dusun I',
            'noHp' => '081299998888',
            'email' => 'unique@mail.com',
            'pekerjaan' => 'Wiraswasta',
            'pendidikan' => 'S1',
            'motivasi' => 'Ingin bergabung',
        ]);

        $response->assertRedirect(route('daftar'));
        $response->assertSessionHasNoErrors();
        $this->assertEquals(1, Anggota::count());
    }

    /** @test */
    public function it_denies_registration_with_invalid_phone_numbers()
    {
        Anggota::truncate();

        // 1. Submit number with non-digits
        $response = $this->post(route('daftar.store'), [
            'namaLengkap' => 'Test Phone Alpha',
            'nikKtp' => '1111222233334444',
            'jenisKelamin' => 'Laki-Laki',
            'tempatLahir' => 'Penajam',
            'tanggalLahir' => '1990-05-15',
            'alamatLengkap' => 'Jl. Merak No. 5',
            'rtSelect' => 'RT 06',
            'dusunSelect' => 'Dusun II',
            'noHp' => '08123456789a', // Letters!
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA/SMK',
            'motivasi' => 'Ingin meningkatkan kesejahteraan',
        ]);
        $response->assertSessionHasErrors(['noHp']);

        // 2. Submit number not starting with 08
        $response = $this->post(route('daftar.store'), [
            'namaLengkap' => 'Test Phone Prefix',
            'nikKtp' => '1111222233334445',
            'jenisKelamin' => 'Laki-Laki',
            'tempatLahir' => 'Penajam',
            'tanggalLahir' => '1990-05-15',
            'alamatLengkap' => 'Jl. Merak No. 5',
            'rtSelect' => 'RT 06',
            'dusunSelect' => 'Dusun II',
            'noHp' => '0211234567', // Not 08!
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA/SMK',
            'motivasi' => 'Ingin meningkatkan kesejahteraan',
        ]);
        $response->assertSessionHasErrors(['noHp']);

        // 3. Submit number that is too short
        $response = $this->post(route('daftar.store'), [
            'namaLengkap' => 'Test Phone Short',
            'nikKtp' => '1111222233334446',
            'jenisKelamin' => 'Laki-Laki',
            'tempatLahir' => 'Penajam',
            'tanggalLahir' => '1990-05-15',
            'alamatLengkap' => 'Jl. Merak No. 5',
            'rtSelect' => 'RT 06',
            'dusunSelect' => 'Dusun II',
            'noHp' => '0812345', // Too short!
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA/SMK',
            'motivasi' => 'Ingin meningkatkan kesejahteraan',
        ]);
        $response->assertSessionHasErrors(['noHp']);

        // 4. Submit NIK with letters
        $response = $this->post(route('daftar.store'), [
            'namaLengkap' => 'Test NIK Alpha',
            'nikKtp' => '111122223333444a', // Letters!
            'jenisKelamin' => 'Laki-Laki',
            'tempatLahir' => 'Penajam',
            'tanggalLahir' => '1990-05-15',
            'alamatLengkap' => 'Jl. Merak No. 5',
            'rtSelect' => 'RT 06',
            'dusunSelect' => 'Dusun II',
            'noHp' => '081234567890',
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA/SMK',
            'motivasi' => 'Ingin meningkatkan kesejahteraan',
        ]);
        $response->assertSessionHasErrors(['nikKtp']);

        // Assert database is empty
        $this->assertEquals(0, Anggota::count());
    }

    /** @test */
    public function it_handles_email_validation_and_max_capacity_correctly()
    {
        Anggota::truncate();

        // 1. Submit with empty email (optional) -> should succeed
        $response1 = $this->post(route('daftar.store'), [
            'namaLengkap' => 'Empty Email User',
            'nikKtp' => '1111111111111111',
            'jenisKelamin' => 'Laki-Laki',
            'tempatLahir' => 'Penajam',
            'tanggalLahir' => '1990-01-01',
            'alamatLengkap' => 'Jl. Mawar No. 1',
            'rtSelect' => 'RT 01',
            'dusunSelect' => 'Dusun I',
            'noHp' => '081234567891',
            'email' => null, // Optional email
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA/SMK',
            'motivasi' => 'Motivasi',
        ]);
        $response1->assertRedirect(route('daftar'));
        $response1->assertSessionHasNoErrors();
        $this->assertDatabaseHas('anggota', ['nik' => '1111111111111111', 'email' => null]);

        // 2. Submit with valid email of exactly 60 characters -> should succeed
        // "a" x 48 (48) + "@example.com" (12 chars) = 60 chars
        $email60 = str_repeat('a', 48) . '@example.com';
        $this->assertEquals(60, strlen($email60));

        $response2 = $this->post(route('daftar.store'), [
            'namaLengkap' => '60 Char Email User',
            'nikKtp' => '2222222222222222',
            'jenisKelamin' => 'Perempuan',
            'tempatLahir' => 'Penajam',
            'tanggalLahir' => '1992-02-02',
            'alamatLengkap' => 'Jl. Melati No. 2',
            'rtSelect' => 'RT 02',
            'dusunSelect' => 'Dusun I',
            'noHp' => '081234567892',
            'email' => $email60,
            'pekerjaan' => 'Pedagang',
            'pendidikan' => 'D3',
            'motivasi' => 'Motivasi',
        ]);
        $response2->assertRedirect(route('daftar'));
        $response2->assertSessionHasNoErrors();
        $this->assertDatabaseHas('anggota', ['nik' => '2222222222222222', 'email' => $email60]);

        // 3. Submit with email of 61 characters -> should fail validation
        // "a" x 49 (49) + "@example.com" (12 chars) = 61 chars
        $email61 = str_repeat('a', 49) . '@example.com';
        $this->assertEquals(61, strlen($email61));

        $response3 = $this->post(route('daftar.store'), [
            'namaLengkap' => '61 Char Email User',
            'nikKtp' => '3333333333333333',
            'jenisKelamin' => 'Laki-Laki',
            'tempatLahir' => 'Penajam',
            'tanggalLahir' => '1995-03-03',
            'alamatLengkap' => 'Jl. Kenanga No. 3',
            'rtSelect' => 'RT 03',
            'dusunSelect' => 'Dusun I',
            'noHp' => '081234567893',
            'email' => $email61,
            'pekerjaan' => 'Swasta',
            'pendidikan' => 'S1',
            'motivasi' => 'Motivasi',
        ]);
        $response3->assertSessionHasErrors(['email']);
    }
}
