<?php

namespace App\Tests\Controller;

use App\Entity\Receipt;
use App\Tests\BaseWebTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ReceiptControllerTest extends BaseWebTestCase
{
    private array $imagePaths;

    public function setUp(): void
    {
        // Keep track of all the initial files in the image folder
        $this->imagePaths = [];
        $finder = new Finder();

        if (!file_exists('images/receipts')) {
            return;
        }

        $finder->files()->in('images/receipts');
        foreach ($finder as $file) {
            array_push($this->imagePaths, $file->getRealPath());
        }
    }

    public function testCreate()
    {
        // Assistant creates a receipt
        $client = $this->createAssistantClient();

        $receiptsBefore = $this->countTableRows('/utlegg', $client);

        // Use image 'kvittering.jpg' as the new image
        $photo = new UploadedFile(
            __DIR__ . '/../Fixtures/kvittering.jpg',
            'kvittering.jpg'
        );

        $crawler = $client->request('GET', '/utlegg');
        $form = $crawler->selectButton('Be om refusjon')->form();

        $form['receipt[description]'] = 'En flott beskrivelse';
        $form['receipt[sum]'] = 123;
        $form['receipt[user][account_number]'] = '1234.56.78903';
        $form['receipt[picturePath]'] = $photo;

        $client->submit($form);

        $receiptsAfter = $this->countTableRows('/utlegg', $client);
        $this->assertEquals(1, $receiptsAfter - $receiptsBefore);

        // Teamleader can see it in the receipt table
        self::ensureKernelShutdown();
        $crawler = $this->teamLeaderGoTo('/kontrollpanel/utlegg');
        $this->assertEquals(1, $crawler->filter('td:contains("assistant@gmail.com")')->count());
    }

    public function testRefunded()
    {
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/kontrollpanel/utlegg/status/2', ['status' => Receipt::STATUS_REFUNDED]);
        $this->assertEquals(302, $client->getResponse()->getStatusCode()); // Successful if redirect
        $this->assertEquals(2, $client->followRedirect()->filter('span.clickable:contains("Refundert")')->count());
    }

    public function testRejected()
    {
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/kontrollpanel/utlegg/status/2', ['status' => Receipt::STATUS_REJECTED]);
        $this->assertEquals(302, $client->getResponse()->getStatusCode()); // Successful if redirect
        $this->assertEquals(1, $client->followRedirect()->filter('span.clickable:contains("Refusjon avvist")')->count());
    }

    public function testPending()
    {
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/kontrollpanel/utlegg/status/2', ['status' => Receipt::STATUS_PENDING]);
        $this->assertEquals(302, $client->getResponse()->getStatusCode()); // Successful if redirect
        $this->assertEquals(2, $client->followRedirect()->filter('span.clickable:contains("Venter behandling...")')->count());
    }

    public function testEdit()
    {
        // Teamleader edits
        $client = $this->createTeamLeaderClient();

        // Use image 'kvittering.jpg' as the new image
        $photo = new UploadedFile(
            __DIR__ . '/../Fixtures/kvittering.jpg',
            'kvittering.jpg'
        );

        $crawler = $client->request('GET', '/kontrollpanel/utlegg/rediger/2');
        $form = $crawler->selectButton('Lagre')->form();

        $form['receipt[description]'] = 'foo bar';
        $form['receipt[sum]'] = 999;
        $form['receipt[picturePath]'] = $photo; // We have to upload a photo otherwise bad stuff happens

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('td:contains("foo bar")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("999,00 kr")')->count());
    }

    public function testDelete()
    {
        // Teamleader deletes
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/utlegg/slett/2');
        $this->assertEquals(302, $client->getResponse()->getStatusCode()); // Successful if redirected
        $client->request('POST', '/utlegg/slett/2'); // Try to delete same receipt again
        $this->assertEquals(404, $client->getResponse()->getStatusCode()); // Doesn't exist
    }

    public function testAnonymousPermission()
    {
        // Anonymous has no access
        $client = $this->createAnonymousClient();
        $client->request('GET', '/utlegg');
        $crawler = $client->followRedirect();
        $this->assertGreaterThanOrEqual(1, $crawler->filter('button:contains("Logg inn")')->count());
    }

    public function testAssistantPermissions()
    {
        // Allowed to edit
        $this->assistantGoTo('/utlegg/rediger/5');

        // Not allowed to edit other people's receipts
        $client = $this->createAssistantClient();
        $client->request('GET', '/utlegg/rediger/1');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Make team leader refund the receipt
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/kontrollpanel/utlegg/status/5', ['status' => 'refunded']);

        // Not allowed to edit refunded receipt
        $client = $this->createAssistantClient();
        $client->request('GET', '/utlegg/rediger/5');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Not allowed to delete refunded receipt
        $client = $this->createAssistantClient();
        $client->request('POST', '/utlegg/slett/5');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testTeamMemberPermissions()
    {
        // Allowed to edit own receipt
        $this->teamMemberGoTo('/utlegg/rediger/6');

        // Not allowed to edit other people's receipts
        $client = $this->createTeamMemberClient();
        $client->request('GET', '/utlegg/rediger/1');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Not allowed to refund
        $client = $this->createTeamMemberClient();
        $client->request('POST', '/kontrollpanel/utlegg/status/6', ['status' => 'refunded']);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Make team leader refund the receipt
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/kontrollpanel/utlegg/status/6', ['status' => 'refunded']);

        // Not allowed to edit refunded receipt
        $client = $this->createTeamMemberClient();
        $client->request('GET', '/utlegg/rediger/6');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        // Not allowed to delete refunded receipt
        $client = $this->createTeamMemberClient();
        $client->request('POST', '/utlegg/slett/6');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testTeamLeaderPermissions()
    {
        // Allowed to edit own receipt
        $this->teamLeaderGoTo('/utlegg/rediger/7');

        // Allowed to edit other people's receipts
        $this->teamLeaderGoTo('/kontrollpanel/utlegg/rediger/1');

        // Allowed to refund
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/kontrollpanel/utlegg/status/7', ['status' => 'refunded']);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Allowed to edit refunded receipt
        $this->teamLeaderGoTo('/kontrollpanel/utlegg/rediger/7');

        // Allowed to delete refunded receipt
        $client = $this->createTeamLeaderClient();
        $client->request('POST', '/utlegg/slett/6');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $directoryExists = file_exists('images/receipts');
        if (!$directoryExists) {
            return;
        }

        // Delete all new files
        $finder = new Finder();
        $finder->files()->in('images/receipts');
        foreach ($finder as $file) {
            $fileIsNew = !in_array($file->getRealPath(), $this->imagePaths, true);
            if ($fileIsNew) {
                unlink($file);
            }
        }

        $receiptDirectoryIsEmpty = (is_countable(glob('images/receipts/*')) ? count(glob('images/receipts/*')) : 0) === 0;
        if ($receiptDirectoryIsEmpty) {
            rmdir('images/receipts');
        }
        $imageDirectoryIsEmpty = (is_countable(glob('images/*')) ? count(glob('images/*')) : 0) === 0;
        if ($imageDirectoryIsEmpty) {
            rmdir('images');
        }
    }
}
