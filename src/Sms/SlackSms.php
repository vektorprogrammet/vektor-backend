<?php

namespace App\Sms;

use App\Service\SlackMessenger;
use Nexy\Slack\Attachment;

class SlackSms implements SmsSenderInterface
{
    private SlackMessenger $slackMessenger;

    public function __construct(SlackMessenger $slackMessenger)
    {
        $this->slackMessenger = $slackMessenger;
    }

    public function send(Sms $sms)
    {
        return;
        // TODO: Reimplement SlackSMS, Nexy\Slack is deprecated
        // Code commented out below.
        $message = $this->slackMessenger->createMessage();

        $attachment = new Attachment();
        $attachment->setColor('#28a745');
        $attachment->setAuthorName('To: ' . $sms->getRecipientsString());
        $attachment->setText("```\n" . $sms->getMessage() . "\n```");

        $message->setText('Sms sent');
        $message->setAttachments([$attachment]);

        $this->slackMessenger->send($message);
    }

    public function validatePhoneNumber(string $number): bool
    {
        return true;
    }
}
