<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\PenyimpananFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPenyimpananPreviewTest extends TestCase
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

        // Create a couple of files for testing inside route
        PenyimpananFile::create([
            'nama_file' => 'test_document.pdf',
            'nama_asli' => 'TestDocument.pdf',
            'kategori' => 'Legalitas',
            'ukuran' => 1024,
            'tipe' => 'application/pdf',
            'keterangan' => 'Test PDF description',
            'uploaded_at' => now(),
        ]);

        PenyimpananFile::create([
            'nama_file' => 'test_image.png',
            'nama_asli' => 'TestImage.png',
            'kategori' => 'Kegiatan',
            'ukuran' => 2048,
            'tipe' => 'image/png',
            'keterangan' => 'Test PNG description',
            'uploaded_at' => now(),
        ]);
    }

    /** @test */
    public function it_renders_preview_elements_in_penyimpanan_views()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->get(route('admin.penyimpanan'));

        $response->assertStatus(200);

        // Verify "Lihat File" action items are present
        $response->assertSee('title="Lihat File"', false);
        $response->assertSee('previewFile(', false);

        // Verify the HTML wrapper of the preview modal exists
        $response->assertSee('id="previewModal"', false);
        $response->assertSee('id="previewImage"', false);
        $response->assertSee('id="previewFallbackMessage"', false);
    }
}
