<?php

namespace App\Service;

use App\Mailer\MailingInterface;
use Nexy\Slack\Attachment;
use Symfony\Component\Mime\Email;

class SlackMailer implements MailingInterface
{
    /**
     * SlackMailer constructor.
     */
    public function __construct(private readonly SlackMessenger $messenger)
    {
    }

    public function send(Email $message, bool $disableLogging = false): void
    {
        $slackMessage = $this->messenger->createMessage();
        $attachment = new Attachment();
        $attachment->setColor('#023874');
        $attachment->setAuthorName('To: ' . implode(', ', array_keys($message->getTo())));
        $attachment->setText('*' . $message->getSubject() . "*\n```\n" . $message->getBody() . "\n```");

        $from = $message->getFrom();
        $attachment->setFooter('From: ' . (!is_array($from) ? $from : current($from) . ' - ' . key($from)));

        $slackMessage->setText('Email sent');
        $slackMessage->setAttachments([$attachment]);

        $this->messenger->send($slackMessage);
    }
}
