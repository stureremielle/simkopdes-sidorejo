<?php

namespace Tests\Feature;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

class MissionGridVerificationTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_renders_all_five_mission_cards_unconditionally()
    {
        \Illuminate\Support\Facades\DB::table('pengaturan')->where('key_name', 'misi')->update(['value' => '']);

        $response = $this->get('/tentang-kami');

        $response->assertStatus(200);

        // Assert presence of all five mission titles
        $response->assertSee('1. Memperluas Kemitraan dan Pasar');
        $response->assertSee('2. Penguatan Kelembagaan');
        $response->assertSee('3. Agrowisata &amp; Pemancingan', false);
        $response->assertSee('4. Pengelolaan Sampah Berkelanjutan');
        $response->assertSee('5. Klinik Bisnis &amp; Magang', false);

        // Assert typo correction
        $response->assertSee('Menyelenggarakan pelatihan agribisnis', false);
    }
}
