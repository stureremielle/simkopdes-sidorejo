<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pengaturan;

class ContactPageVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_custom_telepon_and_email_setting_dynamically()
    {
        // Update or create settings
        Pengaturan::updateOrCreate(
            ['key_name' => 'telepon'],
            ['value' => '+62 899 9999 9999']
        );

        Pengaturan::updateOrCreate(
            ['key_name' => 'email'],
            ['value' => 'koperasi_test@admin.com']
        );

        $response = $this->get('/kontak');

        $response->assertStatus(200);
        $response->assertSee('+62 899 9999 9999');
        $response->assertSee('tel:+6289999999999');
        $response->assertSee('koperasi_test@admin.com');
        $response->assertSee('mailto:koperasi_test@admin.com');
    }

    /** @test */
    public function it_displays_custom_alamat_setting_dynamically()
    {
        Pengaturan::updateOrCreate(
            ['key_name' => 'alamat'],
            ['value' => 'Jl. Melati No 12 Dusun I Desa Sidorejo, Penajam']
        );

        $response = $this->get('/kontak');

        $response->assertStatus(200);
        $response->assertSee('Jl. Melati No 12 Dusun I Desa Sidorejo, Penajam');
    }
}
