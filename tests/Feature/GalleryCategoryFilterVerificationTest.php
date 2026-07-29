<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Galeri;
use App\Models\KategoriGaleri;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GalleryCategoryFilterVerificationTest extends TestCase
{
    private $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');

        $this->adminUser = Admin::create([
            'username' => 'admin_test',
            'nama' => 'Administrator',
            'email' => 'admin_test@kopdes.id',
            'password' => md5('password123'),
        ]);
    }

    /** @test */
    public function it_only_shows_categories_with_active_gallery_items()
    {
        // 1. Clear database
        Galeri::query()->delete();
        KategoriGaleri::query()->delete();

        // 2. Create categories (will be triggered implicitly by setKategoriAttribute or created manually)
        $catA = KategoriGaleri::create(['nama' => 'Kategori A']);
        $catB = KategoriGaleri::create(['nama' => 'Kategori B']);
        $catC = KategoriGaleri::create(['nama' => 'Kategori C']);

        // 3. Create galleries:
        // Kategori A has an active gallery item
        Galeri::create([
            'judul' => 'Kegiatan A',
            'kategori' => 'Kategori A',
            'gambar' => 'assets/images/placeholder.jpg',
            'keterangan' => 'Keterangan A',
            'status' => 'aktif',
        ]);

        // Kategori B has only an inactive/non-published gallery item
        Galeri::create([
            'judul' => 'Kegiatan B',
            'kategori' => 'Kategori B',
            'gambar' => 'assets/images/placeholder.jpg',
            'keterangan' => 'Keterangan B',
            'status' => 'nonaktif',
        ]);

        // Kategori C has no gallery items at all

        $response = $this->get('/galeri');
        $response->assertStatus(200);

        // Assert Kategori A is visible in filter tabs
        $response->assertSee('data-filter="Kategori A"', false);

        // Assert Kategori B and Kategori C are not visible in filter tabs
        $response->assertDontSee('data-filter="Kategori B"', false);
        $response->assertDontSee('data-filter="Kategori C"', false);
    }

    /** @test */
    public function it_does_not_show_deleted_categories_in_admin_filter()
    {
        Galeri::query()->delete();
        KategoriGaleri::query()->delete();

        $catA = KategoriGaleri::create(['nama' => 'Kategori A']);
        $catB = KategoriGaleri::create(['nama' => 'Kategori B']);

        $this->actingAs($this->adminUser, 'admin');

        // Delete Kategori B
        $catB->delete();

        $response = $this->get('/admin/galeri');
        $response->assertStatus(200);

        // Kategori A is visible, Kategori B is not
        $response->assertSee('Kategori A');
        $response->assertDontSee('Kategori B');
    }

    /** @test */
    public function it_does_not_show_deleted_categories_even_with_orphan_references()
    {
        Galeri::query()->delete();
        KategoriGaleri::query()->delete();

        $catA = KategoriGaleri::create(['nama' => 'Kategori A']);
        $catB = KategoriGaleri::create(['nama' => 'Kategori B']);

        // Create gallery item referencing Kategori A
        Galeri::create([
            'judul' => 'Kegiatan A',
            'kategori' => 'Kategori A',
            'gambar' => 'assets/images/placeholder.jpg',
            'keterangan' => 'Keterangan A',
            'status' => 'aktif',
        ]);

        // Create gallery item referencing Kategori B
        $gallery = Galeri::create([
            'judul' => 'Kegiatan B',
            'kategori' => 'Kategori B',
            'gambar' => 'assets/images/placeholder.jpg',
            'keterangan' => 'Keterangan B',
            'status' => 'aktif',
        ]);

        $this->actingAs($this->adminUser, 'admin');

        // Force delete Kategori B bypass constraint or manually
        \Schema::disableForeignKeyConstraints();
        $catB->delete();
        \Schema::enableForeignKeyConstraints();

        // 1. Check admin filter
        $responseAdmin = $this->get('/admin/galeri');
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Kategori A');
        $responseAdmin->assertDontSee('Kategori B');

        // 2. Check public filter
        $responsePublic = $this->get('/galeri');
        $responsePublic->assertStatus(200);
        $responsePublic->assertSee('Kategori A');
        $responsePublic->assertDontSee('data-filter="Kategori B"', false);
    }

    /** @test */
    public function it_displays_newly_added_gallery_items_at_the_very_top()
    {
        Galeri::query()->delete();

        // 1. Create old item
        $old = Galeri::create([
            'judul' => 'Kegiatan Lama',
            'kategori' => 'Kategori A',
            'gambar' => 'assets/images/placeholder.jpg',
            'keterangan' => 'Keterangan Lama',
            'status' => 'aktif',
        ]);

        // 2. Create new item
        $new = Galeri::create([
            'judul' => 'Kegiatan Baru Paling Atas',
            'kategori' => 'Kategori A',
            'gambar' => 'assets/images/placeholder.jpg',
            'keterangan' => 'Keterangan Baru',
            'status' => 'aktif',
        ]);

        // Check Admin view
        $this->actingAs($this->adminUser, 'admin');
        $responseAdmin = $this->get('/admin/galeri');
        $responseAdmin->assertStatus(200);

        $adminGalleries = $responseAdmin->viewData('galeriList');
        $this->assertEquals($new->id, $adminGalleries->first()->id);

        // Check Public view
        $responsePublic = $this->get('/galeri');
        $responsePublic->assertStatus(200);

        $publicGalleries = $responsePublic->viewData('galeriList');
        $this->assertEquals($new->id, $publicGalleries->first()->id);
    }
}

