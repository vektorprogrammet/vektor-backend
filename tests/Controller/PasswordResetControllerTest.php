<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTestCase;

class PasswordResetControllerTest extends BaseWebTestCase
{
    private const email = 'admin@gmail.com';
    private const username = 'admin';
    private const oldPass = '1234';
    private const newPass = '12345678';

    /**
     * @param string $password
     *
     * @return bool login successful
     */
    private function loginSuccessful($password)
    {
        self::ensureKernelShutdown();
        $client = self::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Logg inn')->form();
        $form['_username'] = self::username;
        $form['_password'] = $password;
        $form['_remember_me'] = false;
        $client->submit($form);

        $crawler = $client->request('GET', '/');

        return 0 === $crawler->filter('nav span:contains("Logg inn")')->count();
    }

    /**
     * Gets a valid unused reset link.
     *
     * @return bool|string reset link
     */
    private function getResetLink(string $email)
    {
        // Test that we're getting the right page
        self::ensureKernelShutdown();
        $client = self::createClient();
        $crawler = $client->request('GET', '/resetpassord');
        $this->assertSame(1, $crawler->filter('h1:contains("Tilbakestill passord")')->count());

        // Fill in form and
        $form = $crawler->selectButton('Tilbakestill passord')->form();
        $form['passwordReset[email]'] = $email;

        // $client = $this->createAnonymousClient();
        $client->enableProfiler();
        $client->submit($form);

        // Assert email sent
        $this->assertSame(302, $client->getResponse()->getStatusCode());
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount());
        $message = $mailCollector->getMessages()[0];
        $body = $message->getBody();

        // Get reset link from email
        $start = mb_strpos($body, '/resetpassord/');
        $messageStartingWithCode = mb_substr($body, $start);
        $end = mb_strpos($messageStartingWithCode, "\n");

        return mb_substr($body, $start, $end);
    }

    private function assertNoEmailSent($client)
    {
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(0, $mailCollector->getMessageCount());
    }

    public function testResetPasswordAction()
    {
        $resetLink = $this->getResetLink(self::email);

        // Reset password
        self::ensureKernelShutdown();
        $client = self::createClient();
        $crawler = $client->request('GET', $resetLink);

        $this->assertSame(1, $crawler->filter('h1:contains("Lag nytt passord")')->count());
        $form = $crawler->selectButton('Lagre nytt passord')->first()->form();
        $form['newPassword[password][first]'] = self::newPass;
        $form['newPassword[password][second]'] = self::newPass;

        $client->submit($form);
        $this->assertSame(302, $client->getResponse()->getStatusCode());

        // Assert new password is set
        $this->assertTrue($this->loginSuccessful(self::newPass));
        $this->assertFalse($this->loginSuccessful(self::oldPass));
    }

    public function testInvalidEmail()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/resetpassord');

        $form = $crawler->selectButton('Tilbakestill passord')->form();
        $form['passwordReset[email]'] = 'invalid@email.com';
        $client->enableProfiler();
        $crawler = $client->submit($form);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();

        $this->assertStringContainsString('Det finnes ingen brukere med denne e-postadressen', $content);
        $this->assertNoEmailSent($client);
    }

    public function testSameResetLinkTwice()
    {
        $resetLink = $this->getResetLink(self::email);

        // Reset password
        self::ensureKernelShutdown();
        $client = self::createClient();

        $crawler = $client->request('GET', $resetLink);
        $form = $crawler->selectButton('Lagre nytt passord')->first()->form();
        $form['newPassword[password][first]'] = self::newPass;
        $form['newPassword[password][second]'] = self::newPass;

        $client->submit($form);

        // Try to reset password again
        $crawler = $client->request('GET', $resetLink);

        $this->assertSame(1, $crawler->filter('html:contains("Ugyldig kode")')->count());
    }

    public function testInvalidLink()
    {
        $resetLink = '/resetpassord/'.bin2hex(openssl_random_pseudo_bytes(12));
        $crawler = $this->anonymousGoTo($resetLink);
        $this->assertSame(1, $crawler->filter('html:contains("Ugyldig kode")')->count());
    }

    public function testResetWithCompanyEmail()
    {
        $crawler = $this->anonymousGoTo('/resetpassord');
        $form = $crawler->selectButton('Tilbakestill passord')->form();
        $form['passwordReset[email]'] = 'petter@vektorprogrammet.no';
        $client = $this->createAnonymousClient();
        $client->enableProfiler();
        $crawler = $client->submit($form);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Prøv din private e-post")')->count());

        $this->assertNoEmailSent($client);
    }

    public function testResetWithInactiveUser()
    {
        $crawler = $this->anonymousGoTo('/resetpassord');
        $form = $crawler->selectButton('Tilbakestill passord')->form();
        $form['passwordReset[email]'] = 'inactive@mail.com';
        $client = $this->createAnonymousClient();
        $client->enableProfiler();
        $crawler = $client->submit($form);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Brukeren med denne e-postadressen er deaktivert")')->count());

        $this->assertNoEmailSent($client);
    }
}
