<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class BaseWebTestCase extends WebTestCase
{
    private static ?KernelBrowser $anonymousClient = null;
    private static ?KernelBrowser $assistantClient = null;
    private static ?KernelBrowser $teamMemberClient = null;
    private static ?KernelBrowser $teamLeaderClient = null;
    private static ?KernelBrowser $adminClient = null;

    protected static function createAnonymousClient(): KernelBrowser
    {
        if (self::$anonymousClient === null) {
            self::$anonymousClient = self::createClient();
        }

        return self::$anonymousClient;
    }

    protected static function createAssistantClient(): KernelBrowser
    {
        if (self::$assistantClient === null) {
            self::$assistantClient = self::createClient([], [
                'PHP_AUTH_USER' => 'assistent',
                'PHP_AUTH_PW' => '1234',
            ]);
        }

        return self::$assistantClient;
    }

    protected static function createTeamMemberClient(): KernelBrowser
    {
        if (self::$teamMemberClient === null) {
            self::$teamMemberClient = self::createClient([], [
                'PHP_AUTH_USER' => 'teammember',
                'PHP_AUTH_PW' => '1234',
            ]);
        }

        return self::$teamMemberClient;
    }

    protected static function createTeamLeaderClient(): KernelBrowser
    {
        if (self::$teamLeaderClient === null) {
            self::$teamLeaderClient = self::createClient([], [
                'PHP_AUTH_USER' => 'teamleader',
                'PHP_AUTH_PW' => '1234',
            ]);
        }

        return self::$teamLeaderClient;
    }

    protected static function createAdminClient(): KernelBrowser
    {
        if (self::$adminClient === null) {
            self::$adminClient = self::createClient([], [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => '1234',
            ]);
        }

        return self::$adminClient;
    }

    protected function goTo(string $path, KernelBrowser $client = null): Crawler
    {
        if ($client === null) {
            $client = self::createAnonymousClient();
        }

        $crawler = $client->request('GET', $path);

        $this->assertTrue($client->getResponse()->isSuccessful());

        return $crawler;
    }

    protected function anonymousGoTo(string $path): Crawler
    {
        return $this->goTo($path, self::createAnonymousClient());
    }

    protected function assistantGoTo(string $path): Crawler
    {
        return $this->goTo($path, self::createAssistantClient());
    }

    protected function teamMemberGoTo(string $path): Crawler
    {
        return $this->goTo($path, self::createTeamMemberClient());
    }

    protected function teamLeaderGoTo(string $path): Crawler
    {
        return $this->goTo($path, self::createTeamLeaderClient());
    }

    protected function adminGoTo(string $path): Crawler
    {
        return $this->goTo($path, self::createAdminClient());
    }

    protected function countTableRows(string $path, KernelBrowser $client = null): int
    {
        if ($client === null) {
            $client = self::createAdminClient();
        }

        $crawler = $this->goTo($path, $client);

        return $crawler->filter('tr')->count();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
