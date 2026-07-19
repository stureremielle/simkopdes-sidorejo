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

        // 2. Create 1 featured article + 14 standard active articles
        $featured = new Berita([
            'judul' => 'Featured Article',
            'kategori' => 'Pertanian',
            'isi' => 'Content...',
            'penulis' => 'Author',
            'is_featured' => 1,
            'status' => 'tayang',
        ]);
        $featured->created_at = now()->addDays(5);
        $featured->save();

        for ($i = 1; $i <= 14; $i++) {
            $art = new Berita([
                'judul' => 'Standard Article ' . $i,
                'kategori' => 'Pertanian',
                'isi' => 'Content of article ' . $i,
                'penulis' => 'Author',
                'is_featured' => 0,
                'status' => 'tayang',
            ]);
            $art->created_at = now()->addMinutes($i);
            $art->save();
        }

        // Total standard articles: 14.
        // Order by created_at desc, id desc:
        // Standard Article 14 (now + 14m), 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1.

        // 3. Make mock AJAX request with offset = 6 (First page ends at 6 articles: 14 to 9 are loaded in initial view)
        // Skip 6 loads: 8, 7, 6, 5, 4, 3
        $response = $this->getJson(route('berita', ['offset' => 6]), [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'has_more']);
        
        $data = $response->json();
        $this->assertTrue($data['has_more']);
        $this->assertStringContainsString('Standard Article 7', $data['html']);
        $this->assertStringContainsString('Standard Article 8', $data['html']);

        // 4. Make mock AJAX request with offset = 12 (Second page: 14 to 3 are skipped)
        // Skip 12 loads: 2, 1. has_more is false.
        $response = $this->getJson(route('berita', ['offset' => 12]), [
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertFalse($data['has_more']);
        $this->assertStringContainsString('Standard Article 1', $data['html']);
        $this->assertStringContainsString('Standard Article 2', $data['html']);
    }
}
