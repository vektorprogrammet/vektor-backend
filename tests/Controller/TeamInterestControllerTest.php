<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class TeamInterestControllerTest extends BaseWebTestCase
{
    public function testShowTeamInterestForm()
    {
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptakadmin/teaminteresse?department=1&semester=1');
        $rowsBefore = $crawler->filter('tr')->count();
        self::ensureKernelShutdown(); // throws LogicalException else

        $client = $this->createAnonymousClient();
        $crawler = $client->request('GET', '/teaminteresse/1');
        $form = $crawler->selectButton('Send')->form();
        $form['App_teaminterest[name]'] = 'Test Testesen';
        $form['App_teaminterest[email]'] = 'test@testmail.com';
        $form['App_teaminterest[potentialTeams]'][3]->tick();
        $this->createAnonymousClient()->submit($form);
        $this->assertSame(302, $client->getResponse()->getStatusCode()); // Assert request was redirected
        self::ensureKernelShutdown();

        $crawler = $this->teamLeaderGoTo('/kontrollpanel/opptakadmin/teaminteresse?department=1&semester=1');
        $rowsAfter = $crawler->filter('tr')->count();
        $this->assertSame($rowsBefore + 2, $rowsAfter);
    }
}
