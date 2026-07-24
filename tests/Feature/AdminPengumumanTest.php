<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Pengumuman;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPengumumanTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = Admin::create([
            'username' => 'admin_test',
            'nama' => 'Administrator',
            'email' => 'admin_test@kopdes.id',
            'password' => md5('password123'),
        ]);

        $this->artisan('migrate');
    }

    /** @test */
    public function admin_can_access_announcement_index()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->get('/admin/pengumuman');
        $response->assertStatus(200);
        $response->assertSee('Pengumuman');
        $response->assertSee('Kelola informasi atau pengumuman yang akan ditampilkan pada halaman Beranda website.');
    }

    /** @test */
    public function admin_can_add_announcement()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post('/admin/pengumuman', [
            'judul' => 'Pengumuman Baru Test',
            'tanggal_mulai' => '2026-07-15',
            'tanggal_selesai' => '2026-07-25',
            'isi' => 'Konten Pengumuman Baru',
            'status' => 'Aktif',
        ]);

        $response->assertRedirect('/admin/pengumuman');
        $this->assertDatabaseHas('pengumuman', [
            'judul' => 'Pengumuman Baru Test',
            'status' => 'Aktif',
        ]);
    }

    /** @test */
    public function admin_can_update_announcement()
    {
        $this->actingAs($this->adminUser, 'admin');

        $announce = Pengumuman::create([
            'judul' => 'Judul Awal',
            'tanggal_mulai' => '2026-07-15',
            'isi' => 'Isisi awal',
            'status' => 'Aktif',
        ]);

        $response = $this->put("/admin/pengumuman/{$announce->id}", [
            'judul' => 'Judul Update',
            'tanggal_mulai' => '2026-07-15',
            'tanggal_selesai' => '2026-07-28',
            'isi' => 'Isisi awal',
            'status' => 'Tidak Aktif',
        ]);

        $response->assertRedirect('/admin/pengumuman');
        $this->assertDatabaseHas('pengumuman', [
            'id' => $announce->id,
            'judul' => 'Judul Update',
            'status' => 'Tidak Aktif',
        ]);
    }

    /** @test */
    public function admin_can_delete_announcement()
    {
        $this->actingAs($this->adminUser, 'admin');

        $announce = Pengumuman::create([
            'judul' => 'Akan Dihapus',
            'tanggal_mulai' => '2026-07-15',
            'isi' => 'Akan dihapus segera',
            'status' => 'Aktif',
        ]);

        $response = $this->delete("/admin/pengumuman/{$announce->id}");

        $response->assertRedirect('/admin/pengumuman');
        $this->assertDatabaseMissing('pengumuman', [
            'id' => $announce->id,
        ]);
    }
}
