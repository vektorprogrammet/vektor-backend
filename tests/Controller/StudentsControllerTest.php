<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class StudentsControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/studenter');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Assistenter');
        $this->assertEquals(1, $crawler->filter('p:contains("Vektorprogrammet er en studentorganisasjon som")')->count());
    }
}
