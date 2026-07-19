<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class AdminPasswordSettingsTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user for authentication testing
        $this->adminUser = Admin::create([
            'username' => 'admin_test',
            'nama' => 'Administrator',
            'email' => 'admin_test@kopdes.id',
            'password' => md5('password123'),
        ]);
    }

    /** @test */
    public function it_updates_koperasi_info_but_keeps_password_when_password_fields_are_empty()
    {
        // Authenticate admin
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post('/admin/pengaturan', [
            'nama_koperasi' => 'Kopdes Baru',
            'alamat' => 'Jl. Baru RT 01',
            'telepon' => '+628999',
            'email' => 'baru@koperasi.com',
            'password_lama' => '',
            'password_baru' => '',
            'password_konfirmasi' => '',
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $response->assertSessionHas('success');

        // Confirm database settings updated
        $this->assertEquals('Kopdes Baru', Pengaturan::getValue('nama_koperasi'));
        $this->assertEquals('Jl. Baru RT 01', Pengaturan::getValue('alamat'));

        // Confirm password is unchanged
        $this->adminUser->refresh();
        $this->assertEquals(md5('password123'), $this->adminUser->password);
    }

    /** @test */
    public function it_fails_password_update_when_old_password_is_incorrect()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post('/admin/pengaturan', [
            'nama_koperasi' => 'Kopdes Baru',
            'alamat' => 'Jl. Baru RT 01',
            'telepon' => '+628999',
            'email' => 'baru@koperasi.com',
            'password_lama' => 'wrongpassword',
            'password_baru' => 'newpassword123',
            'password_konfirmasi' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['password_lama']);

        // Confirm password is unchanged
        $this->adminUser->refresh();
        $this->assertEquals(md5('password123'), $this->adminUser->password);
    }

    /** @test */
    public function it_fails_password_update_when_new_password_is_too_short()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post('/admin/pengaturan', [
            'nama_koperasi' => 'Kopdes Baru',
            'alamat' => 'Jl. Baru RT 01',
            'telepon' => '+628999',
            'email' => 'baru@koperasi.com',
            'password_lama' => 'password123',
            'password_baru' => '123', // too short
            'password_konfirmasi' => '123',
        ]);

        $response->assertSessionHasErrors(['password_baru']);
    }

    /** @test */
    public function it_fails_password_update_when_new_password_and_confirmation_do_not_match()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post('/admin/pengaturan', [
            'nama_koperasi' => 'Kopdes Baru',
            'alamat' => 'Jl. Baru RT 01',
            'telepon' => '+628999',
            'email' => 'baru@koperasi.com',
            'password_lama' => 'password123',
            'password_baru' => 'newpassword123',
            'password_konfirmasi' => 'different123',
        ]);

        $response->assertSessionHasErrors(['password_konfirmasi']);
    }

    /** @test */
    public function it_updates_password_successfully_when_valid_payload_provided()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post('/admin/pengaturan', [
            'nama_koperasi' => 'Kopdes Baru',
            'alamat' => 'Jl. Baru RT 01',
            'telepon' => '+628999',
            'email' => 'baru@koperasi.com',
            'password_lama' => 'password123',
            'password_baru' => 'newpassword123',
            'password_konfirmasi' => 'newpassword123',
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $response->assertSessionHas('success');

        // Confirm database settings updated
        $this->assertEquals('Kopdes Baru', Pengaturan::getValue('nama_koperasi'));

        // Confirm password is changed
        $this->adminUser->refresh();
        $this->assertEquals(md5('newpassword123'), $this->adminUser->password);
    }

    /** @test */
    public function it_triggers_validation_when_only_old_password_is_filled()
    {
        $this->actingAs($this->adminUser, 'admin');

        $response = $this->post('/admin/pengaturan', [
            'nama_koperasi' => 'Kopdes Baru',
            'alamat' => 'Jl. Baru RT 01',
            'telepon' => '+628999',
            'email' => 'baru@koperasi.com',
            'password_lama' => 'password123',
            'password_baru' => '',
            'password_konfirmasi' => '',
        ]);

        $response->assertSessionHasErrors(['password_baru']);
    }
}
