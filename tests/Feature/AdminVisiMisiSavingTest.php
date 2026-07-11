<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminVisiMisiSavingTest extends TestCase
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

        // Seed some initial settings
        Pengaturan::updateOrCreate(['key_name' => 'nama_koperasi'], ['value' => 'Kopdes Lama']);
        Pengaturan::updateOrCreate(['key_name' => 'visi'], ['value' => 'Visi Lama']);
        Pengaturan::updateOrCreate(['key_name' => 'misi'], ['value' => 'Misi Lama Satu|Misi Lama Dua']);
    }

    /** @test */
    public function it_saves_visi_and_misi_correctly_from_admin_form()
    {
        $this->actingAs($this->adminUser, 'admin');

        // Submit Form 2 (Visi & Misi tab form)
        $response = $this->post('/admin/pengaturan', [
            'nama_koperasi' => 'Kopdes Lama',
            'alamat' => '',
            'telepon' => '',
            'email' => '',
            'visi' => 'Visi Baru Yang Keren',
            'misi' => 'Misi Baru Satu|Misi Baru Dua|Misi Baru Tiga',
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $response->assertSessionHas('success');

        // Verify database has updated values
        $this->assertEquals('Visi Baru Yang Keren', Pengaturan::getValue('visi'));
        $this->assertEquals('Misi Baru Satu|Misi Baru Dua|Misi Baru Tiga', Pengaturan::getValue('misi'));

        // Visit Tentang Kami page and assert it shows the new values
        $response = $this->get('/tentang-kami');
        $response->assertStatus(200);
        $response->assertSee('Visi Baru Yang Keren');
        $response->assertSee('Misi Baru Satu');
        $response->assertSee('Misi Baru Dua');
        $response->assertSee('Misi Baru Tiga');
    }
}
