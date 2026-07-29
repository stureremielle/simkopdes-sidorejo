<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Berita;

class NewsLoadMoreVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_paginated_articles_via_ajax()
    {
        // 1. Clear database berita table
        Berita::truncate();

        // 2. Create 1 featured article (latest tanggal_publikasi) + 14 standard articles
        //    Featured article has the most recent tanggal_publikasi so it becomes the hero.
        $featured = Berita::create([
            'judul'              => 'Featured Article',
            'kategori'           => 'Pertanian',
            'isi'                => 'Content...',
            'penulis'            => 'Author',
            'status'             => 'tayang',
            'tanggal_publikasi'  => now()->addDays(5)->toDateString(),
        ]);

        // Standard articles 1-14, each with a different tanggal_publikasi (newer = higher number).
        // Order by COALESCE(tanggal_publikasi, DATE(created_at)) DESC, id DESC:
        // Article 14 (now+14m), 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1.
        for ($i = 1; $i <= 14; $i++) {
            Berita::create([
                'judul'             => 'Standard Article ' . $i,
                'kategori'          => 'Pertanian',
                'isi'               => 'Content of article ' . $i,
                'penulis'           => 'Author',
                'status'            => 'tayang',
                'tanggal_publikasi' => now()->addMinutes($i)->toDateString(),
            ]);
        }

        // Total standard articles: 14. Featured excluded from list.
        // Initial view loads first 6: Article 14, 13, 12, 11, 10, 9.
        // offset=6 loads next batch: Article 8, 7, 6, 5, 4, 3.
        // offset=12 loads last 2:   Article 2, 1.

        // 3. AJAX request offset=6 — expect articles 8 and 7
        $response = $this->getJson(route('berita', ['offset' => 6]), [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'has_more']);

        $data = $response->json();
        $this->assertTrue($data['has_more']);
        $this->assertStringContainsString('Standard Article 8', $data['html']);
        $this->assertStringContainsString('Standard Article 7', $data['html']);

        // 4. AJAX request offset=12 — expect articles 2 and 1, no more after
        $response = $this->getJson(route('berita', ['offset' => 12]), [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertFalse($data['has_more']);
        $this->assertStringContainsString('Standard Article 2', $data['html']);
        $this->assertStringContainsString('Standard Article 1', $data['html']);
    }
}
