<?php

namespace App\Service;

use App\Mailer\MailerInterface;
use Nexy\Slack\Attachment;

class SlackMailer implements MailerInterface
{
    /**
     * SlackMailer constructor.
     */
    public function __construct(private readonly SlackMessenger $messenger)
    {
    }

    public function send(\Swift_Message $message, bool $disableLogging = false)
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
