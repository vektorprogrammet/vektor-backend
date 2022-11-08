<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class HomeControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        // Create anonymous client
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Vektorprogrammet');
        $this->assertSelectorTextContains('h2', 'Hovedsponsorer');
    }
}
