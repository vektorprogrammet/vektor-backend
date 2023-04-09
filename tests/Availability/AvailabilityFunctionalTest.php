<?php

namespace App\Tests\Availability;

use App\Tests\BaseWebTestCase;

class AvailabilityFunctionalTest extends BaseWebTestCase
{
    /**
     * @dataProvider publicUrlProvider
     */
    public function testPublicPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider assistantUrlProvider
     */
    public function testAssistantPageIsSuccessful($url)
    {
        $client = $this->createAssistantClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider assistantUrlProvider
     */
    public function testAssistantPageIsDenied($url)
    {
        // Check if anonymous users gets denied
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider teamMemberUrlProvider
     */
    public function testTeamMemberPageIsSuccessful($url)
    {
        $client = $this->createTeamMemberClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider teamMemberUrlProvider
     */
    public function testTeamMemberPageIsDenied($url)
    {
        // Check if assistants gets denied
        $client = $this->createAssistantClient();
        $client->request('GET', $url);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider teamLeaderUrlProvider
     */
    public function testTeamLeaderPageIsSuccessful($url)
    {
        $client = $this->createTeamLeaderClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider teamLeaderUrlProvider
     */
    public function testTeamLeaderPageIsDenied($url)
    {
        // Check if team member gets denied
        $client = $this->createTeamMemberClient();
        $client->request('GET', $url);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider adminUrlProvider
     */
    public function testAdminPageIsSuccessful($url)
    {
        $client = $this->createAdminClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider adminUrlProvider
     */
    public function testAdminPageIsDenied($url)
    {
        // Check if team leader gets denied
        $client = $this->createTeamLeaderClient();
        $client->request('GET', $url);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function publicUrlProvider(): array
    {
        return [
            // ['/'],
            ['/assistenter'],
            ['/team'],
            ['/laerere'],
            ['/omvektor'],
            ['/kontakt'],

            ['/profile/1'],

            ['/opptak'],
            ['/opptak/NTNU'],
            ['/opptak/avdeling/1'],
            ['/opptak/Bergen'],
            ['/opptak/ås'],

            ['/avdeling/Trondheim'],
            ['/avdeling/NTNU'],
            ['/avdeling/ås'],
        ];
    }

    public function assistantUrlProvider(): array
    {
        return [
            ['/profile'],
            ['/profil/rediger/passord/'],
            ['/min-side'],
            ['/utlegg'],
        ];
    }

    public function teamMemberUrlProvider(): array
    {
        return [
            ['/kontrollpanel'],
            ['/kontrollpanel/opptaksperiode'],

            ['/kontrollpanel/opptak/nye'],
            ['/kontrollpanel/opptak/nye?department=1&semester=1'],
            ['/kontrollpanel/opptak/gamle'],
            ['/kontrollpanel/opptak/gamle?department=1&semester=1'],
            ['/kontrollpanel/opptak/fordelt'],
            ['/kontrollpanel/opptak/fordelt?department=1&semester=1'],
            ['/kontrollpanel/opptak/intervjuet'],
            ['/kontrollpanel/opptak/intervjuet?department=1&semester=1'],

            ['/kontrollpanel/intervju/skjema'],
            ['/kontrollpanel/intervju/skjema/1'],

            ['/kontrollpanel/stand'],
            ['/kontrollpanel/stand?department=1&semester=1'],

            ['/kontrollpanel/statistikk/opptak'],
            ['/kontrollpanel/statistikk/opptak?department=1&semester=1'],

            ['/kontrollpanel/deltakerhistorikk'],
            ['/kontrollpanel/deltakerhistorikk?department=1&semester=1'],

            ['/kontrollpanel/vikar'],
            ['/kontrollpanel/vikar?department=1&semester=1'],

            ['/kontrollpanel/team/avdeling'],
            ['/kontrollpanel/teamadmin/team/1'],

            ['/kontrollpanel/opprettsoker'],
            ['/kontrollpanel/brukeradmin/opprett'],

            ['/kontrollpanel/vikar'],
            ['/kontrollpanel/vikar?department=1&semester=1'],

            ['/kontrollpanel/team/avdeling'],
            ['/kontrollpanel/teamadmin/team/1'],

            ['/kontrollpanel/brukeradmin'],
            ['/kontrollpanel/epostlister'],
            ['/kontrollpanel/sponsorer'],

            ['/kontrollpanel/utlegg'],
            ['/kontrollpanel/utlegg/2'],

            ['/kontrollpanel/avdelingadmin'],

            ['/kontrollpanel/skoleadmin'],
            ['/kontrollpanel/skoleadmin/brukere'],
            ['/kontrollpanel/skoleadmin/tildel/skole/1'],
        ];
    }

    public function teamLeaderUrlProvider(): array
    {
        return [
            ['/kontrollpanel/intervju/settopp/6'],
            ['/kontrollpanel/intervju/conduct/6'],
            ['/kontrollpanel/intervju/vis/4'],
            ['/kontrollpanel/skole/timeplan/'],

            ['/kontrollpanel/teamadmin/stillinger'],
            ['/kontrollpanel/teamadmin/opprett/stilling'],
            ['/kontrollpanel/teamadmin/rediger/stilling/1'],
            ['/kontrollpanel/teamadmin/avdeling/opprett/1'],
            ['/kontrollpanel/teamadmin/update/1'],
            ['/kontrollpanel/teamadmin/team/nytt_medlem/1'],
            ['/kontrollpanel/teamadmin/oppdater/teamhistorie/1'],
            ['/kontrollpanel/team/avdeling/2'],

            ['/kontrollpanel/hovedstyret'],
            ['/kontrollpanel/hovedstyret/nytt_medlem/1'],
            ['/kontrollpanel/hovedstyret/rediger_medlem/1'],
            ['/kontrollpanel/hovedstyret/oppdater'],

            ['/kontrollpanel/opptakadmin/teaminteresse'],
            ['/kontrollpanel/opptakadmin/teaminteresse?department=1&semester=1'],

            ['/kontrollpanel/brukeradmin/avdeling/2'],
            ['/kontrollpanel/brukeradmin/opprett/2'],

            ['/kontrollpanel/avdelingadmin/update/1'],

            ['/kontrollpanel/skoleadmin/opprett/1'],
            ['/kontrollpanel/skoleadmin/oppdater/1'],
            ['/kontrollpanel/skoleadmin/avdeling/2'],

            ['/kontrollpanel/linjer'],
            ['/kontrollpanel/linje/1'],
            ['/kontrollpanel/linje'],
        ];
    }

    public function adminUrlProvider(): array
    {
        return [
            ['/kontrollpanel/avdelingadmin/opprett'],
            ['/kontrollpanel/bruker/vekorepost/endre/1'],
            ['/kontrollpanel/semesteradmin'],
            ['/kontrollpanel/semesteradmin/opprett'],
        ];
    }
}
