<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class AdmissionAdminViewTest extends BaseWebTestCase
{
    public function testShowAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('a.btn:contains("Ny søker")')->count());
        $this->assertEquals(0, $crawler->filter('option:contains("Fordel intervju")')->count());
        $this->assertEquals(0, $crawler->filter('option:contains("Slett søknad")')->count());
        $this->assertEquals(0, $crawler->filter('a.btn:contains("Utfør")')->count());
    }

    public function testShowAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptak');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertEquals(1, $crawler->filter('a.btn:contains("Ny søker")')->count());
        $this->assertEquals(1, $crawler->filter('option:contains("Fordel intervju")')->count());
        $this->assertEquals(0, $crawler->filter('option:contains("Slett søknad")')->count());
        $this->assertEquals(1, $crawler->filter('a.btn:contains("Utfør")')->count());
    }

    public function testShowAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptak');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertEquals(1, $crawler->filter('a.btn:contains("Ny søker")')->count());
        $this->assertEquals(1, $crawler->filter('option:contains("Fordel intervju")')->count());
        $this->assertEquals(1, $crawler->filter('option:contains("Slett søknad")')->count());
        $this->assertEquals(1, $crawler->filter('a.btn:contains("Utfør")')->count());
    }

    public function testAssignedAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak/fordelt');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('td a:contains("Sett opp")')->count());
        $this->assertEquals(0, $crawler->filter('td a:contains("Start intervju")')->count());
    }

    public function testAssignedAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptak/fordelt');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td a:contains("Sett opp")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td a:contains("Start intervju")')->count());
    }

    public function testAssignedAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptak/fordelt');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td a:contains("Sett opp")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td a:contains("Start intervju")')->count());
    }

    public function testInterviewedAsTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak/intervjuet');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertEquals(0, $crawler->filter('td button:contains("Slett")')->count());
    }

    public function testInterviewedAsTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptak/intervjuet');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertGreaterThanOrEqual(0, $crawler->filter('td button:contains("Slett")')->count());
    }

    public function testInterviewedAsAdmin()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/opptak/intervjuet');

        $this->assertEquals(1, $crawler->filter('h2:contains("Opptak")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td button:contains("Slett")')->count());
    }

    public function testCancelInterview()
    {
        $client = $this->createTeamLeaderClient();

        $crawler = $client->request('GET', '/kontrollpanel/opptak/fordelt');
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td:contains("Ruben Ravnå")')->count());

        $crawler = $client->request('GET', '/kontrollpanel/opptak/nye');
        $this->assertEquals(0, $crawler->filter('td:contains("Ruben Ravnå")')->count());
    }
}
