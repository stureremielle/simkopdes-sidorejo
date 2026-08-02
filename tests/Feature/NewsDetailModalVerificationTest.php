<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Berita;

class NewsDetailModalVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_renders_news_detail_as_modal_popup_over_news_background()
    {
        // 1. Clear berita to isolate
        Berita::truncate();

        // 2. Create featured news article (which is shown in the background)
        $featured = Berita::create([
            'judul' => 'Rapat Bulanan Petani Bahas Teknik Organik Baru',
            'kategori' => 'Pertanian',
            'isi' => '<p>Koperasi kami baru saja mengadakan pertemuan...</p><h3>Pengendalian Hama Alami</h3><blockquote>"Tanah yang sehat..."</blockquote>',
            'penulis' => 'Budi Santoso',
            'gambar' => 'featured_office_lounge.png',
            'status' => 'tayang'
        ]);

        // Create standard news article
        $standard = Berita::create([
            'judul' => 'Musim Panen Melampaui Harapan',
            'kategori' => 'Pertanian',
            'isi' => 'Berkat cuaca yang mendukung...',
            'penulis' => 'Admin',
            'gambar' => 'panen.png',
            'status' => 'tayang'
        ]);

        // 3. Request the detail page of the featured article
        $response = $this->get(route('berita.detail', $featured->id));

        $response->assertStatus(200);

        // 4. Assert modal close button to return to index
        $response->assertSee('class="modal-close-x-btn"', false);
        $response->assertSee('href="' . route('berita') . '"', false);

        // 5. Assert database HTML contents are correctly rendered unescaped ({!! $berita->isi !!})
        $response->assertSee('<h3>Pengendalian Hama Alami</h3>', false);
        $response->assertSee('<blockquote>"Tanah yang sehat..."</blockquote>', false);

        // 6. Assert background news elements are present (indicating it lists standard in background)
        $response->assertSee('Musim Panen Melampaui Harapan');
        $response->assertSee('ambient-news-bg');
    }
}
