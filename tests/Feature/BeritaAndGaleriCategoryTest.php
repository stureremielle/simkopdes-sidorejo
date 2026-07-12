<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\KategoriBerita;
use App\Models\KategoriGaleri;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BeritaAndGaleriCategoryTest extends TestCase
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

        // Run migrations/seeders implicitly
        $this->artisan('migrate');
    }

    /** @test */
    public function it_can_add_berita_category_via_ajax()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->postJson(route('admin.berita.kategori.store'), [
            'kategori' => 'Kategori Baru Berita'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $categories = KategoriBerita::pluck('nama')->toArray();
        $this->assertContains('Kategori Baru Berita', $categories);
    }

    /** @test */
    public function it_denies_duplicate_berita_category()
    {
        $this->actingAs($this->adminUser, 'admin');

        Berita::query()->delete();
        KategoriBerita::truncate();
        KategoriBerita::create(['nama' => 'Umum']);

        $response = $this->postJson(route('admin.berita.kategori.store'), [
            'kategori' => 'umum'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function it_allows_deleting_berita_category_not_used()
    {
        $this->actingAs($this->adminUser, 'admin');

        Berita::query()->delete();
        KategoriBerita::truncate();
        KategoriBerita::create(['nama' => 'Umum']);
        KategoriBerita::create(['nama' => 'Kategori Unused']);

        $response = $this->deleteJson(route('admin.berita.kategori.destroy', 'Kategori Unused'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $categories = KategoriBerita::pluck('nama')->toArray();
        $this->assertNotContains('Kategori Unused', $categories);
    }

    /** @test */
    public function it_denies_deleting_berita_category_in_use()
    {
        $this->actingAs($this->adminUser, 'admin');

        Berita::query()->delete();
        KategoriBerita::truncate();
        KategoriBerita::create(['nama' => 'Umum']);

        Berita::create([
            'judul' => 'Judul Berita',
            'kategori' => 'Umum',
            'isi' => 'Konten berita.',
            'penulis' => 'Penulis',
            'gambar_url' => '',
            'status' => 'tayang',
        ]);

        $response = $this->deleteJson(route('admin.berita.kategori.destroy', 'Umum'));

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
        
        $categories = KategoriBerita::pluck('nama')->toArray();
        $this->assertContains('Umum', $categories);
    }

    /** @test */
    public function it_can_add_galeri_category_via_ajax()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->postJson(route('admin.galeri.kategori.store'), [
            'kategori' => 'Kategori Baru Galeri'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $categories = KategoriGaleri::pluck('nama')->toArray();
        $this->assertContains('Kategori Baru Galeri', $categories);
    }

    /** @test */
    public function it_denies_duplicate_galeri_category()
    {
        $this->actingAs($this->adminUser, 'admin');

        Galeri::query()->delete();
        KategoriGaleri::truncate();
        KategoriGaleri::create(['nama' => 'Kegiatan']);

        $response = $this->postJson(route('admin.galeri.kategori.store'), [
            'kategori' => 'kegiatan'
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function it_allows_deleting_galeri_category_not_used()
    {
        $this->actingAs($this->adminUser, 'admin');

        Galeri::query()->delete();
        KategoriGaleri::truncate();
        KategoriGaleri::create(['nama' => 'Kegiatan']);
        KategoriGaleri::create(['nama' => 'Kategori Galeri Unused']);

        $response = $this->deleteJson(route('admin.galeri.kategori.destroy', 'Kategori Galeri Unused'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $categories = KategoriGaleri::pluck('nama')->toArray();
        $this->assertNotContains('Kategori Galeri Unused', $categories);
    }

    /** @test */
    public function it_denies_deleting_galeri_category_in_use()
    {
        $this->actingAs($this->adminUser, 'admin');

        Galeri::query()->delete();
        KategoriGaleri::truncate();
        KategoriGaleri::create(['nama' => 'Kegiatan']);

        Galeri::create([
            'judul' => 'Judul Kegiatan',
            'kategori' => 'Kegiatan',
            'gambar_url' => '',
            'keterangan' => 'Keterangan',
            'status' => 'aktif',
        ]);

        $response = $this->deleteJson(route('admin.galeri.kategori.destroy', 'Kegiatan'));

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);

        $categories = KategoriGaleri::pluck('nama')->toArray();
        $this->assertContains('Kegiatan', $categories);
    }
}
