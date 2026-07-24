<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Berita;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BeritaDraftFilterVerificationTest extends TestCase
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
    public function it_filters_and_counts_draft_articles_correctly()
    {
        $this->actingAs($this->adminUser, 'admin');

        // Clean up any existing news
        Berita::query()->delete();

        // Create 2 tayang and 3 draft
        for ($i = 0; $i < 2; $i++) {
            Berita::create([
                'judul' => "Tayang Article {$i}",
                'kategori' => 'Umum',
                'isi' => 'Konten berita.',
                'penulis' => 'Penulis',
                'gambar_url' => '',
                'status' => 'tayang',
            ]);
        }

        for ($i = 0; $i < 3; $i++) {
            Berita::create([
                'judul' => "Draft Article {$i}",
                'kategori' => 'Umum',
                'isi' => 'Konten berita.',
                'penulis' => 'Penulis',
                'gambar_url' => '',
                'status' => 'draft',
            ]);
        }

        // 1. Visit index (no filter, 'semua')
        $response = $this->get('/admin/berita');
        $response->assertStatus(200);
        $response->assertSee('2 tayang');
        $response->assertSee('3 draft');
        $response->assertSee('Tayang Article 0');
        $response->assertSee('Draft Article 0');

        // 2. Visit index with status=draf
        $response = $this->get('/admin/berita?status=draf');
        $response->assertStatus(200);
        $response->assertSee('Draft Article 0');
        $response->assertSee('Draft Article 1');
        $response->assertSee('Draft Article 2');
        $response->assertDontSee('Tayang Article 0');

        // 3. Visit index with status=tayang
        $response = $this->get('/admin/berita?status=tayang');
        $response->assertStatus(200);
        $response->assertSee('Tayang Article 0');
        $response->assertDontSee('Draft Article 0');
    }
}
