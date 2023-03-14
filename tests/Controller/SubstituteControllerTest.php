<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class SubstituteControllerTest extends BaseWebTestCase
{
    /**
     * Assert that team-leader has access to edit/delete buttons.
     */
    public function testShowTeamLeader()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/vikar');

        // Verify that edit/delete buttons show on page
        $this->assertGreaterThanOrEqual(1, $crawler->filter('a:contains("Rediger")')->count());
        $this->assertGreaterThanOrEqual(0, $crawler->filter('button:contains("Slett")')->count());
    }

    /**
     * Assert that team-member does not have access to edit/delete buttons.
     */
    public function testShowTeamMember()
    {
        $crawler = $this->teamMemberGoTo('/kontrollpanel/vikar');

        // Verify that edit/delete buttons don't show on page
        $this->assertEquals(0, $crawler->filter('a:contains("Slett")')->count());
        $this->assertEquals(0, $crawler->filter('a:contains("Rediger")')->count());
    }

    public function testShowSubstitutesByDepartment()
    {
        // Team leader
        $crawler = $this->adminGoTo('/kontrollpanel/vikar?department=1&semester=1');

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h2:contains("Vikarer")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Team")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("Johansen")')->count());

        // Assert that edit/delete buttons show on page
        // Note: Requires that at least 1 substitute is present in the database
        $this->assertGreaterThan(0, $crawler->filter('button:contains("Slett")')->count());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("Rediger")')->count());
    }

    public function testIllegalCreateMethod()
    {
        $client = self::createTeamLeaderClient();

        $client->request('GET', '/kontrollpanel/vikar/opprett/4');

        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }

    public function testCreate()
    {
        $subCountBefore = $this->countTableRows('/kontrollpanel/vikar');

        $client = self::createTeamLeaderClient();
        $client->request('POST', '/kontrollpanel/vikar/opprett/4');

        $subCountAfter = $this->countTableRows('/kontrollpanel/vikar');

        $this->assertEquals(1, $subCountAfter - $subCountBefore);

        // Delete the created substitute after test
        $client->request('POST', '/kontrollpanel/vikar/slett/4');
    }

    public function testEdit()
    {
        // Team leader
        $client = self::createTeamLeaderClient();

        $crawler = $this->goTo('/kontrollpanel/vikar/rediger/1', $client);

        // Find the form
        $form = $crawler->selectButton('Oppdater')->form();

        // Fill in the form
        $form['modifySubstitute[user][phone]'] = '95999999';

        // Submit the form
        $client->submit($form);

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct page with the correct info (from the submitted form)
        $this->assertEquals(1, $crawler->filter('td:contains("95999999")')->count());
    }
}
