<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pengumuman;

class PengumumanFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_all_active_announcements_on_homepage_sorted_by_date()
    {
        // 1. Truncate announcements
        Pengumuman::truncate();

        // 2. Create inactive announcements (should not appear)
        \DB::table('pengumuman')->insert([
            'judul' => 'Pengumuman Inaktif 1',
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => null,
            'isi' => 'Konten Inaktif 1',
            'status' => 'Tidak Aktif',
            'created_at' => '2026-07-10 10:00:00',
            'updated_at' => '2026-07-10 10:00:00'
        ]);

        // 3. Create active announcements (different dates)
        // First one (newest post)
        \DB::table('pengumuman')->insert([
            'judul' => 'Pengumuman Newest Active 1',
            'tanggal_mulai' => '2026-07-20',
            'tanggal_selesai' => '2026-07-30',
            'isi' => 'Konten Newest Active 1',
            'status' => 'Aktif',
            'created_at' => '2026-07-14 08:00:00',
            'updated_at' => '2026-07-14 08:00:00'
        ]);

        // Second one (middle)
        \DB::table('pengumuman')->insert([
            'judul' => 'Pengumuman Middle Active 2',
            'tanggal_mulai' => '2026-07-15',
            'tanggal_selesai' => null,
            'isi' => 'Konten Middle Active 2',
            'status' => 'Aktif',
            'created_at' => '2026-07-13 08:00:00',
            'updated_at' => '2026-07-13 08:00:00'
        ]);

        // Third one (older)
        \DB::table('pengumuman')->insert([
            'judul' => 'Pengumuman Older Active 3',
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => null,
            'isi' => 'Konten Older Active 3',
            'status' => 'Aktif',
            'created_at' => '2026-07-12 08:00:00',
            'updated_at' => '2026-07-12 08:00:00'
        ]);

        // Fourth one (oldest - should also display because there is no limit)
        \DB::table('pengumuman')->insert([
            'judul' => 'Pengumuman Oldest Active 4',
            'tanggal_mulai' => '2026-07-05',
            'tanggal_selesai' => null,
            'isi' => 'Konten Oldest Active 4',
            'status' => 'Aktif',
            'created_at' => '2026-07-11 08:00:00',
            'updated_at' => '2026-07-11 08:00:00'
        ]);

        // 4. Request Homepage
        $response = $this->get('/');

        $response->assertStatus(200);

        // 5. Assert all active announcements are visible
        $response->assertSee('Pengumuman Newest Active 1');
        $response->assertSee('Pengumuman Middle Active 2');
        $response->assertSee('Pengumuman Older Active 3');
        $response->assertSee('Pengumuman Oldest Active 4');

        // 7. Assert the inactive announcement is NOT visible
        $response->assertDontSee('Pengumuman Inaktif 1');
    }

    /** @test */
    public function it_loads_the_detail_page_of_an_active_announcement()
    {
        // 1. Create a dummy active announcement
        $announce = Pengumuman::create([
            'judul' => 'Detail Title Active',
            'tanggal_mulai' => '2026-07-25',
            'tanggal_selesai' => null,
            'isi' => '<p>Daftar konten lengkap detail</p>',
            'status' => 'Aktif',
        ]);

        // 2. Request details
        $response = $this->get(route('pengumuman.detail', $announce->id));

        $response->assertStatus(200);
        $response->assertSee('Detail Title Active');
        $response->assertSee('Daftar konten lengkap detail');
    }

    /** @test */
    public function it_aborts_with_404_if_announcement_is_inactive()
    {
        // 1. Create an inactive announcement
        $announce = Pengumuman::create([
            'judul' => 'Detail Title Inactive',
            'tanggal_mulai' => '2026-07-25',
            'tanggal_selesai' => null,
            'isi' => 'Detail konten inaktif',
            'status' => 'Tidak Aktif',
        ]);

        // 2. Request details (should return 404)
        $response = $this->get(route('pengumuman.detail', $announce->id));

        $response->assertStatus(404);
    }
}
