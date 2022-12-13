<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class ParticipantHistoryControllerTest extends BaseWebTestCase
{
    public function testIndex()
    {
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/deltakerhistorikk');

        // Assert that we have the correct page
        $this->assertSame(1, $crawler->filter('h2:contains("Assistenter")')->count());

        // Assert that we have the correct data
        $this->assertStringContainsString('Petter Johansen', $client->getResponse()->getContent());
        $this->assertStringContainsString('petter@stud.ntnu.no', $client->getResponse()->getContent());
        $this->assertStringContainsString('Hovedstyret', $client->getResponse()->getContent());
        $this->assertStringContainsString('NTNU', $client->getResponse()->getContent());
        $this->assertStringContainsString('Bolk 2', $client->getResponse()->getContent());
        $this->assertStringContainsString('Onsdag', $client->getResponse()->getContent());
        $this->assertStringContainsString('Gimse', $client->getResponse()->getContent());

        // Check the count for the different variables
        $this->assertSame(1, $crawler->filter('td a:contains("Petter Johansen")')->count());
        $this->assertSame(1, $crawler->filter('td:contains("petter@stud.ntnu.no")')->count());
        $this->assertSame(2, $crawler->filter('td:contains("Bolk 2")')->count());
        $this->assertSame(1, $crawler->filter('td:contains("Onsdag")')->count());
        $this->assertSame(1, $crawler->filter('td:contains("Gimse")')->count());

        // Assert a specific 200 status code
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
