<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\KategoriLayanan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminDashboardVerificationTest extends TestCase
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
    public function it_displays_dashboard_with_correct_member_trend_and_dynamic_categories()
    {
        $this->actingAs($this->adminUser, 'admin');

        // Verify with 0 categories/products
        \App\Models\Layanan::query()->delete();
        KategoriLayanan::truncate();
        
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('+2 tahun ini');
        $response->assertSee('0 kategori');

        // Create categories
        $catA = KategoriLayanan::create(['nama' => 'Pertanian']);
        $catB = KategoriLayanan::create(['nama' => 'Peternakan']);
        $catC = KategoriLayanan::create(['nama' => 'Perikanan']);
        $catD = KategoriLayanan::create(['nama' => 'Perkebunan']);

        // Create products
        // Active products: using catA and catB (2 unique categories)
        \App\Models\Layanan::create([
            'nama' => 'Padi Super',
            'kategori_id' => $catA->id,
            'deskripsi' => 'Deskripsi',
            'harga' => 1000,
            'satuan' => 'kg',
            'status' => 'aktif'
        ]);
        \App\Models\Layanan::create([
            'nama' => 'Jagung Manis',
            'kategori_id' => $catA->id, // Duplicate category A
            'deskripsi' => 'Deskripsi',
            'harga' => 2000,
            'satuan' => 'kg',
            'status' => 'aktif'
        ]);
        \App\Models\Layanan::create([
            'nama' => 'Sapi Perah',
            'kategori_id' => $catB->id,
            'deskripsi' => 'Deskripsi',
            'harga' => 3000,
            'satuan' => 'ekor',
            'status' => 'aktif'
        ]);

        // Inactive product: using catC (should not be counted)
        \App\Models\Layanan::create([
            'nama' => 'Lele Sangkuriang',
            'kategori_id' => $catC->id,
            'deskripsi' => 'Deskripsi',
            'harga' => 4000,
            'satuan' => 'kg',
            'status' => 'nonaktif' // Inactive!
        ]);

        // catD is left unused
        
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('+2 tahun ini');
        $response->assertSee('2 kategori'); // Only Pertanian and Peternakan are counted!
    }
}
