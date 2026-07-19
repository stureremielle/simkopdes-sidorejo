<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\PenyimpananFile;
use App\Models\KategoriPenyimpanan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PenyimpananCategoryTest extends TestCase
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
    public function it_can_add_penyimpanan_category_via_ajax()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->postJson(route('admin.penyimpanan.kategori.store'), [
            'kategori' => 'Kategori Baru Penyimpanan'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $categories = KategoriPenyimpanan::pluck('nama')->toArray();
        $this->assertContains('Kategori Baru Penyimpanan', $categories);
    }

    /** @test */
    public function it_denies_duplicate_penyimpanan_category()
    {
        $this->actingAs($this->adminUser, 'admin');

        KategoriPenyimpanan::truncate();
        KategoriPenyimpanan::create(['nama' => 'Laporan']);

        $response = $this->postJson(route('admin.penyimpanan.kategori.store'), [
            'kategori' => 'laporan'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function it_allows_deleting_penyimpanan_category_not_used()
    {
        $this->actingAs($this->adminUser, 'admin');

        KategoriPenyimpanan::truncate();
        KategoriPenyimpanan::create(['nama' => 'Laporan']);
        KategoriPenyimpanan::create(['nama' => 'Kategori Unused']);

        $response = $this->deleteJson(route('admin.penyimpanan.kategori.destroy', 'Kategori Unused'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $categories = KategoriPenyimpanan::pluck('nama')->toArray();
        $this->assertNotContains('Kategori Unused', $categories);
    }

    /** @test */
    public function it_denies_deleting_penyimpanan_category_in_use()
    {
        $this->actingAs($this->adminUser, 'admin');

        KategoriPenyimpanan::truncate();
        KategoriPenyimpanan::create(['nama' => 'Laporan']);

        PenyimpananFile::create([
            'nama_file' => 'test.pdf',
            'nama_asli' => 'test.pdf',
            'kategori' => 'Laporan',
            'ukuran' => 100,
            'tipe' => 'application/pdf',
            'keterangan' => 'Keterangan',
        ]);

        $response = $this->deleteJson(route('admin.penyimpanan.kategori.destroy', 'Laporan'));

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);

        $categories = KategoriPenyimpanan::pluck('nama')->toArray();
        $this->assertContains('Laporan', $categories);
    }
}
