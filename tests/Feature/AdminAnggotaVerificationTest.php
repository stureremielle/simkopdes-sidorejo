<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Anggota;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAnggotaVerificationTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');

        $this->adminUser = Admin::create([
            'username' => 'admin_test',
            'nama' => 'Administrator',
            'email' => 'admin_test@kopdes.id',
            'password' => md5('password123'),
        ]);
    }

    /** @test */
    public function it_calculates_total_anggota_representing_only_accepted_members()
    {
        $this->actingAs($this->adminUser, 'admin');

        // Clear existing members
        Anggota::query()->delete();

        // Create 2 accepted (Aktif) members
        Anggota::create([
            'nama_lengkap' => 'Anggota Aktif A',
            'nik' => '1111111111111111',
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak',
            'rt' => 'RT 06',
            'dusun' => 'Dusun I',
            'no_hp' => '0812',
            'jabatan' => 'Anggota',
            'status' => 'diterima',
        ]);
        Anggota::create([
            'nama_lengkap' => 'Anggota Aktif B',
            'nik' => '2222222222222222',
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak',
            'rt' => 'RT 06',
            'dusun' => 'Dusun I',
            'no_hp' => '0812',
            'jabatan' => 'Anggota',
            'status' => 'diterima',
        ]);

        // Create 1 pending (Menunggu) member
        Anggota::create([
            'nama_lengkap' => 'Anggota Pending',
            'nik' => '3333333333333333',
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak',
            'rt' => 'RT 06',
            'dusun' => 'Dusun I',
            'no_hp' => '0812',
            'jabatan' => 'Anggota',
            'status' => 'menunggu',
        ]);

        // Create 1 rejected (Ditolak) member
        Anggota::create([
            'nama_lengkap' => 'Anggota Ditolak',
            'nik' => '4444444444444444',
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak',
            'rt' => 'RT 06',
            'dusun' => 'Dusun I',
            'no_hp' => '0812',
            'jabatan' => 'Anggota',
            'status' => 'ditolak',
        ]);

        $response = $this->get('/admin/anggota');
        $response->assertStatus(200);

        // Verify the rendered stats in HTML:
        // Total Anggota should be 2 (equal to aktif count, not 4)
        // Anggota Aktif should be 2
        // Menunggu Verifikasi should be 1
        // Ditolak should be 1
        $response->assertSee('Total Anggota');
        $response->assertSee('Anggota Aktif');
        $response->assertSee('Menunggu Verifikasi');
        $response->assertSee('Ditolak');

        // Let's assert database values explicitly passed to view via controller:
        $statTotal = $response->viewData('statTotal');
        $statAktif = $response->viewData('statAktif');
        $statMenunggu = $response->viewData('statMenunggu');
        $statDitolak = $response->viewData('statDitolak');

        $this->assertEquals(4, $statTotal);
        $this->assertEquals(2, $statAktif);
        $this->assertEquals(1, $statMenunggu);
        $this->assertEquals(1, $statDitolak);
    }

    /** @test */
    public function it_denies_storing_anggota_with_invalid_nik_or_phone()
    {
        $this->actingAs($this->adminUser, 'admin');

        // 1. Submit with invalid NIK (alphabetical)
        $response = $this->post('/admin/anggota', [
            'nama_lengkap' => 'Invalid NIK Member',
            'nik' => '111122223333444a', // alphanumeric NIK
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak',
            'rt' => 'RT01',
            'dusun' => 'Dusun I',
            'no_hp' => '081234567890',
            'email' => 'test@test.com',
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA',
            'jabatan' => 'Anggota',
            'sumber' => 'Admin',
            'status' => 'diterima',
        ]);
        $response->assertSessionHasErrors(['nik']);

        // 2. Submit with invalid Phone (does not start with 08)
        $response = $this->post('/admin/anggota', [
            'nama_lengkap' => 'Invalid Phone Member',
            'nik' => '1111222233334444',
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak',
            'rt' => 'RT01',
            'dusun' => 'Dusun I',
            'no_hp' => '021123456789', // Not 08!
            'email' => 'test@test.com',
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA',
            'jabatan' => 'Anggota',
            'sumber' => 'Admin',
            'status' => 'diterima',
        ]);
        $response->assertSessionHasErrors(['no_hp']);
    }

    /** @test */
    public function it_denies_updating_anggota_with_invalid_nik_or_phone()
    {
        $this->actingAs($this->adminUser, 'admin');

        $anggota = Anggota::create([
            'nama_lengkap' => 'Original Member',
            'nik' => '1234567890123456',
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak',
            'rt' => 'RT01',
            'dusun' => 'Dusun I',
            'no_hp' => '081234567890',
            'jabatan' => 'Anggota',
            'status' => 'diterima',
        ]);

        // Update with non-numeric NIK
        $response = $this->put('/admin/anggota/' . $anggota->id, [
            'nama_lengkap' => 'Updated Member',
            'nik' => '123456789012345a', // alpha
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak',
            'rt' => 'RT01',
            'dusun' => 'Dusun I',
            'no_hp' => '081234567890',
            'email' => 'test@test.com',
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA',
            'jabatan' => 'Anggota',
            'sumber' => 'Admin',
            'status' => 'diterima',
        ]);
        $response->assertSessionHasErrors(['nik']);

        // Update with phone not starting with 08
        $response = $this->put('/admin/anggota/' . $anggota->id, [
            'nama_lengkap' => 'Updated Member',
            'nik' => '1234567890123456',
            'jenis_kelamin' => 'Laki-Laki',
            'tempat_lahir' => 'Penajam',
            'tanggal_lahir' => '1990-05-15',
            'alamat_lengkap' => 'Jl. Merak',
            'rt' => 'RT01',
            'dusun' => 'Dusun I',
            'no_hp' => '091234567890', // Starts with 09 instead of 08
            'email' => 'test@test.com',
            'pekerjaan' => 'Petani',
            'pendidikan' => 'SMA',
            'jabatan' => 'Anggota',
            'sumber' => 'Admin',
            'status' => 'diterima',
        ]);
        $response->assertSessionHasErrors(['no_hp']);
    }
}
