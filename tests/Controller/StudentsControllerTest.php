<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class StudentsControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $client = $this->createAnonymousClient();

        $crawler = $client->request('GET', '/studenter');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('h1:contains("Assistenter")')->count());
        $this->assertEquals(1, $crawler->filter('h2:contains("Lærerassistent i matematikk")')->count());
        $this->assertEquals(1, $crawler->filter('p:contains("sender realfagssterke studenter til grunnskolen for å hjelpe elevene med matematikk")')->count());
    }
}
