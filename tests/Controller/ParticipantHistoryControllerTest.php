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
        $this->assertEquals(1, $crawler->filter('h2:contains("Assistenter")')->count());

        // Assert that we have the correct data
        $this->assertStringContainsString('Petter Johansen', $client->getResponse()->getContent());
        $this->assertStringContainsString('petter@stud.ntnu.no', $client->getResponse()->getContent());
        $this->assertStringContainsString('Hovedstyret', $client->getResponse()->getContent());
        $this->assertStringContainsString('NTNU', $client->getResponse()->getContent());
        $this->assertStringContainsString('Bolk 2', $client->getResponse()->getContent());
        $this->assertStringContainsString('Onsdag', $client->getResponse()->getContent());
        $this->assertStringContainsString('Gimse', $client->getResponse()->getContent());

        // Check the count for the different variables
        $this->assertEquals(1, $crawler->filter('td a:contains("Petter Johansen")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("petter@stud.ntnu.no")')->count());
        $this->assertEquals(2, $crawler->filter('td:contains("Bolk 2")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Onsdag")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Gimse")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
