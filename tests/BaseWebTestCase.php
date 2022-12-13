<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class BaseWebTestCase extends WebTestCase
{
    private static $anonymousClient;
    private static $assistantClient;
    private static $teamMemberClient;
    private static $teamLeaderClient;
    private static $adminClient;

    protected static function createAnonymousClient(): KernelBrowser
    {
        if (null === self::$anonymousClient) {
            self::$anonymousClient = self::createClient();
        }

        return self::$anonymousClient;
    }

    protected static function createAssistantClient(): KernelBrowser
    {
        if (null === self::$assistantClient) {
            self::$assistantClient = self::createClient([], [
                'PHP_AUTH_USER' => 'assistent',
                'PHP_AUTH_PW' => '1234',
            ]);
        }

        return self::$assistantClient;
    }

    protected static function createTeamMemberClient(): KernelBrowser
    {
        if (null === self::$teamMemberClient) {
            self::$teamMemberClient = self::createClient([], [
                'PHP_AUTH_USER' => 'teammember',
                'PHP_AUTH_PW' => '1234',
            ]);
        }

        return self::$teamMemberClient;
    }

    protected static function createTeamLeaderClient(): KernelBrowser
    {
        if (null === self::$teamLeaderClient) {
            self::$teamLeaderClient = self::createClient([], [
                'PHP_AUTH_USER' => 'teamleader',
                'PHP_AUTH_PW' => '1234',
            ]);
        }

        return self::$teamLeaderClient;
    }

    protected static function createAdminClient(): KernelBrowser
    {
        if (null === self::$adminClient) {
            self::$adminClient = self::createClient([], [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => '1234',
            ]);
        }

        return self::$adminClient;
    }

    protected function goTo(string $path, KernelBrowser $client = null): Crawler
    {
        if (null === $client) {
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
        if (null === $client) {
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
