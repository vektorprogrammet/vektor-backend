<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\BaseWebTestCase;

class MailingListControllerTest extends BaseWebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testAddOneTeamMember()
    {
        self::ensureKernelShutdown();
        $client = self::createAdminClient();

        $countTeamOld = $this->countEmailAddresses($client, 'Team');

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

        $user = $this->em->getRepository(User::class)->find($userID);
        $this->assertNotNull($user);

        $countTeamNew = $this->countEmailAddresses($client, 'Team');

        // verify that the user is added to the team (count increased by 1)
        $this->assertSame($countTeamOld + 1, $countTeamNew);
    }

    public function testTeamAddAssistantIsAll()
    {
        self::ensureKernelShutdown();
        $client = self::createAdminClient();

        $countAssistants = $this->countEmailAddresses($client, 'Assistent');
        $countTeam = $this->countEmailAddresses($client, 'Team');
        $countAll = $this->countEmailAddresses($client, 'Alle');

        $this->assertGreaterThan(0, $countAssistants);
        $this->assertGreaterThan(0, $countTeam);
        $this->assertGreaterThan($countAssistants, $countAll);
        $this->assertGreaterThan($countTeam, $countAll);
    }

    private function countEmailAddresses($client, string $type): int
    {
        /**
         * Function does the following:
         * (1) Generate email list for type
         *     $type can be among these strings: [Assistent, Team, Alle]
         * (2) Count number of "@"s (implicit: number of email addresses)
         * (3) Return number.
         */

        // Generate email list
        $crawler = $this->goTo('/kontrollpanel/epostlister', $client);
        $form = $crawler->selectButton('Generer')->form();
        $form['generate_mailing_list[semester]'] = 1;
        $form['generate_mailing_list[type]'] = $type;
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());

        // Count "@"s and return number
        return mb_substr_count($crawler->filter('pre')->text(), '@');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }
}
