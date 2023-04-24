<?php

namespace App\Tests\Controller;

use App\Entity\Interview;
use App\Tests\BaseWebTestCase;

class AdmissionAdminControllerTest extends BaseWebTestCase
{
    /**
     * Test the functions on /intervju/code.
     */
    public function testAcceptInterview()
    {
        $this->helperTestStatus('Akseptert', 'Godta', 'Intervjuet ble akseptert.');
    }

    // 24.04.23: disabled because test fails if more than one of these three tests are run at the same time
    // Possibly because it is not being reset to the original state after each test
    // And hence fails. Commented out now, to be rewritten as API test when API is implemented.

//    public function testNewTimeInterview()
//    {
//        $this->helperTestStatus('Ny tid ønskes', 'Be om ny tid', 'Forespørsel har blitt sendt.');
//    }
//    public function testCancelInterview()
//    {
//        $this->helperTestStatus('Kansellert', 'Kanseller', 'Intervjuet ble kansellert.');
//    }

    /**
     * Test the status functionality on /intervju/code.
     *
     * Start at kontrollpanel/opptak/fordelt and count occurrences of $status and "Ingen
     * svar". Then, set up an interview and arrange for an email to be sent to the candidate.
     * Examine the contents of the email and extract the unique response code. Proceed to the
     * schedule response page with our special code and click the button corresponding to
     * $button_text. If this is a cancellation or a request for new time, we verify that an email
     * is sent to the interviewer. If this is a cancellation, we go through the cancel confirmation page.
     * Afterwards, verify that we get the correct flash message after the redirect. Finally,
     * go back to assigned page and check that the number of elements containing $status has
     * increased and that the number of elements containing "Ingen svar" har decreased.
     */
    private function helperTestStatus(string $status, string $button_text, string $flash_text)
    {
        $form = [];
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak/fordelt');

        // We store these values, because we expect them to change soon
        $count_no_setup = $crawler->filter('td:contains("Ikke satt opp")')->count();
        $count_no_answer = $crawler->filter('td:contains("Ingen svar")')->count();
        $count_status = $crawler->filter('td:contains(' . $status . ')')->count();

        // We need an admin client who is able to schedule an interview
        self::ensureKernelShutdown();
        $client = self::createAdminClient();

        // We need to schedule an interview, and catch the unique code in the email which is sent
        $crawler = $this->goTo('/kontrollpanel/intervju/settopp/6', $client);

        // At this point we are about to send the email
        $form['scheduleInterview[datetime]'] = '10.08.2015 15:00';
        $form = $crawler->selectButton('Send invitasjon på sms og e-post')->form();
        $client->enableProfiler();
        $client->submit($form);

        $response_code = $this->getResponseCodeFromEmail($client);

        $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($count_no_answer + 1, $crawler->filter('td:contains("Ingen svar")')->count());

        self::ensureKernelShutdown();
        $client = self::createAnonymousClient();
        $crawler = $this->goTo('/intervju/' . $response_code, $client);

        // Clicking a button on this page should trigger the mentioned change.
        $statusButton = $crawler->selectButton($button_text);
        $this->assertNotNull($statusButton);
        $form = $statusButton->form();
        $this->assertNotNull($form);
        $wantEmail = ($status === 'Ny tid ønskes' || $status === 'Kansellert');
        if ($wantEmail) {
            $client->enableProfiler();
        }
        $client->submit($form);

        if ($status === 'Kansellert') {
            $client = $this->helperTestCancelConfirm($client, $response_code);
        } elseif ($status === 'Ny tid ønskes') {
            $crawler = $this->goTo('/intervju/nytid/' . $response_code, $client);
            $form = $crawler->selectButton('Be om nytt tidspunkt')->form();
            $form['InterviewNewTime[newTimeMessage]'] = 'Test answer';
            $client->enableProfiler();
            $client->submit($form);
        }

        if ($wantEmail) {
            $this->assertEmailCount(1);
        }

        $client->followRedirect();
        $this->assertTrue($client->getResponse()->isSuccessful());

        self:self::ensureKernelShutdown();
        $crawler = $this->teamMemberGoTo('/kontrollpanel/opptak/fordelt');

        // Verify that a change has taken place.
        $this->assertEquals($count_no_setup - 1, $crawler->filter('td:contains("Ikke satt opp")')->count());
        $this->assertEquals($count_status + 1, $crawler->filter('td:contains(' . $status . ')')->count());
    }

    private function getResponseCodeFromEmail($client): string
    {
        $this->assertEmailCount(1);
        $message = $this->getMailerMessage();
        $body = $message->getHtmlBody();
        $start = mb_strpos((string) $body, 'intervju/') + 9;
        $messageStartingWithCode = mb_substr((string) $body, $start);
        $end = mb_strpos($messageStartingWithCode, '"');

        return mb_substr((string) $body, $start, $end);
    }

    private function helperTestCancelConfirm($client, string $response_code)
    {
        $crawler = $this->goTo('/intervju/kanseller/tilbakemelding/' . $response_code, $client);
        $form = $crawler->selectButton('Kanseller')->form();
        $form['CancelInterviewConfirmation[message]'] = 'Test answer';
        $client->enableProfiler();
        $client->submit($form);

        $kernel = $this->createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $interview = $em->getRepository(Interview::class)->findByResponseCode($response_code);
        $this->assertEquals('Test answer', $interview->getCancelMessage());

        return $client;
    }
}
