<?php

namespace Tests\Feature;

use Tests\TestCase;

class FooterIconsVerificationTest extends TestCase
{
    /** @test */
    public function it_has_red_footer_contact_icons_in_css()
    {
        $cssPath = public_path('assets/css/style.css');
        $this->assertFileExists($cssPath);

        $cssContent = file_get_contents($cssPath);
        
        // Assert that footer contact icon class has color set to #EF4444
        $this->assertTrue(
            preg_match('/\.footer-contact-icon\s*\{\s*color:\s*#EF4444;/i', $cssContent) === 1,
            'Footer contact icon color is not #EF4444 red in assets/css/style.css'
        );
    }
}
