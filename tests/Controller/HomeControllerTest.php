<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class HomeControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->goTo('/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Vektorprogrammet');
        $this->assertSelectorTextContains('h2', 'Hovedsponsorer');
    }
}
