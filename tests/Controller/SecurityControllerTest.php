<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class SecurityControllerTest extends BaseWebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertTrue($crawler->filter('html:contains("Brukernavn")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Passord")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Glemt passord?")')->count() > 0);
    }

    public function testLoginAdmin()
    {
        $client = $this->login('admin', '1234');

        $this->assertTrue($client->getResponse()->isRedirect('/kontrollpanel'));
    }

    public function testLoginTeamLeader()
    {
        $client = $this->login('teamleader', '1234');

        $this->assertTrue($client->getResponse()->isRedirect('/kontrollpanel'));
    }

    public function testLoginTeamMember()
    {
        $client = $this->login('teammember', '1234');

        $this->assertTrue($client->getResponse()->isRedirect('/kontrollpanel'));
    }

    public function testLoginAssistant()
    {
        $client = $this->login('assistent', '1234');

        $this->assertTrue($client->getResponse()->isRedirect('/min-side'));
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    private function login($username, $password)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Logg inn')->form();

        // Change the value of a field
        $form['_username'] = $username;
        $form['_password'] = $password;

        // submit the form
        $client->submit($form);

        // Redirection from login_check to login_redirect
        $this->assertTrue($client->getResponse()->isRedirection());
        $client->followRedirect();

        return $client;
    }
}
