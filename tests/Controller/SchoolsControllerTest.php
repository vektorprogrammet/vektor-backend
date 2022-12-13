<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class SchoolsControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/laerere');

        // Assert a specific 200 status code
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct amount of data
        $this->assertSame(1, $crawler->filter('h1:contains("Vektorassistenter i skolen")')->count());
        $this->assertSame(1, $crawler->filter('p:contains("Vektorprogrammet er Norges største")')->count());
    }
}
