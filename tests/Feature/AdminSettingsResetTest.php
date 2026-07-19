<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminSettingsResetTest extends TestCase
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
        
        Pengaturan::updateOrCreate(['key_name' => 'hero_background'], ['value' => 'active_hero.jpg']);
        Pengaturan::updateOrCreate(['key_name' => 'org_chart'], ['value' => 'active_org.jpg']);
    }

    /** @test */
    public function it_resets_hero_background_correctly()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post('/admin/pengaturan/hero', [
            'action' => 'reset',
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $response->assertSessionHas('success');

        $this->assertEquals('', Pengaturan::getValue('hero_background'));
    }

    /** @test */
    public function it_resets_org_chart_correctly()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post('/admin/pengaturan/org-chart', [
            'action' => 'reset',
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $response->assertSessionHas('success');

        $this->assertEquals('', Pengaturan::getValue('org_chart'));
    }
}
