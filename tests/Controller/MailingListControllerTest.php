<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class MailingListControllerTest extends BaseWebTestCase
{
    public function testAddOneTeamMember()
    {
        $client = $this->createAdminClient();

        $lengthTeamOld = $this->generateListCountChars($client, 'Team');

        // Get a user email and add user to a team
        $userID = 23;

        $crawler = $this->goTo('/kontrollpanel/teamadmin/team/nytt_medlem/2', $client);
        $form = $crawler->selectButton('Legg til')->form();
        $form['createTeamMembership[user]'] = $userID;
        $form['createTeamMembership[position]'] = 2;
        $form['createTeamMembership[startSemester]'] = 2;
        $client->submit($form);
        $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());

		$em = self::getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository(User::class)->find($userID);
        $this->assertNotNull($user);
        $userEmailLength = strlen($user->getCompanyEmail());

        $lengthTeamNew = $this->generateListCountChars($client, 'Team');

        // Add 2 for comma and whitespace
        $this->assertEquals($lengthTeamOld + $userEmailLength + 2, $lengthTeamNew);
    }

    public function testTeamAddAssistantIsAll()
    {
        $client = $this->createAdminClient();

        $lengthAssistants = $this->generateListCountChars($client, 'Assistent');
        $lengthTeam = $this->generateListCountChars($client, 'Team');
        $lengthAll = $this->generateListCountChars($client, 'Alle');

        $this->assertGreaterThan(0, $lengthAssistants);
        $this->assertGreaterThan(0, $lengthTeam);
        $this->assertGreaterThan($lengthAssistants, $lengthAll);
        $this->assertGreaterThan($lengthTeam, $lengthAll);
    }

    /**
     * @param Client $client
     * @param string $type
     *
     * @return int
     */
    private function generateListCountChars(KernelBrowser $client, string $type)
    {
        $crawler = $this->goTo('/kontrollpanel/epostlister', $client);
        $form = $crawler->selectButton('Generer')->form();
        $form['generate_mailing_list[semester]'] = 1;
        $form['generate_mailing_list[type]'] = $type;
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());

        return strlen($crawler->filter('pre')->text());
    }
}
