<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Layanan;
use App\Models\KategoriLayanan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryManagementVerificationTest extends TestCase
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
    public function it_initializes_default_categories_when_accessing_layanan_index()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->get('/admin/layanan');
        $response->assertStatus(200);

        // Verify the KategoriLayanan table has seeded categories
        $categories = KategoriLayanan::pluck('nama')->toArray();
        $this->assertNotEmpty($categories);
        $this->assertContains('Pertanian', $categories);
        $this->assertContains('Peternakan', $categories);
    }

    /** @test */
    public function it_saves_new_category_successfully()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post('/admin/layanan/kategori', [
            'kategori' => 'Perikanan Darat'
        ]);

        $response->assertRedirect('/admin/layanan');
        $response->assertSessionHas('success');

        // Check if category is ucwords formatted and exists in DB
        $categories = KategoriLayanan::pluck('nama')->toArray();
        $this->assertContains('Perikanan Darat', $categories);
    }

    /** @test */
    public function it_denies_storing_duplicate_categories()
    {
        $this->actingAs($this->adminUser, 'admin');

        // Seed initial categories (clear table first to make sure count is exactly 1)
        Layanan::query()->delete();
        KategoriLayanan::truncate();
        KategoriLayanan::create(['nama' => 'Pertanian']);

        $response = $this->post('/admin/layanan/kategori', [
            'kategori' => 'pertanian' // case-insensitive check
        ]);

        $response->assertRedirect('/admin/layanan');
        $response->assertSessionHas('error');

        $categories = KategoriLayanan::pluck('nama')->toArray();
        $this->assertCount(1, $categories); // Didn't add
    }

    /** @test */
    public function it_allows_deleting_categories_with_no_products()
    {
        $this->actingAs($this->adminUser, 'admin');

        // Seed initial categories containing an empty one
        Layanan::query()->delete();
        KategoriLayanan::truncate();
        KategoriLayanan::create(['nama' => 'Pertanian']);
        KategoriLayanan::create(['nama' => 'Peternakan']);
        KategoriLayanan::create(['nama' => 'Kategori Kosong']);

        $response = $this->delete(route('admin.kategori.destroy', 'Kategori Kosong'));

        $response->assertRedirect('/admin/layanan');
        $response->assertSessionHas('success');

        $categories = KategoriLayanan::pluck('nama')->toArray();
        $this->assertNotContains('Kategori Kosong', $categories);
    }

    /** @test */
    public function it_denies_deleting_categories_with_products()
    {
        $this->actingAs($this->adminUser, 'admin');

        // Initializing category settings first
        $this->get('/admin/layanan');

        // Create product associated
        Layanan::create([
            'nama' => 'Padi Super Unggul',
            'kategori' => 'Pertanian',
            'deskripsi' => 'Padi berkualitas',
            'harga' => 15000,
            'satuan' => 'kg',
            'gambar_url' => '',
            'status' => 'aktif'
        ]);

        $response = $this->delete(route('admin.kategori.destroy', 'Pertanian'));

        $response->assertRedirect('/admin/layanan');
        $response->assertSessionHas('error');

        $categories = KategoriLayanan::pluck('nama')->toArray();
        $this->assertContains('Pertanian', $categories);
    }
}
