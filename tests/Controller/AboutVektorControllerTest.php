<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class AboutVektorControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->goTo('/omvektor');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Om Vektorprogrammet');
        $this->assertSame(1, $crawler->filter('h2:contains("Ofte stilte spørsmål og svar")')->count());
    }
}
