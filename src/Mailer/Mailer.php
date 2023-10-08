<?php

namespace App\Mailer;

use App\Google\Gmail;
use App\Service\SlackMailer;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer implements MailingInterface
{
    private Gmail|SlackMailer|MailerInterface|null $mailer = null;

    public function __construct(string $env,
        Gmail $gmail,
        MailerInterface $swiftMailer,
        SlackMailer $slackMailer)
    {
        if ($env === 'prod') {
            $this->mailer = $gmail;
        } elseif ($env === 'staging') {
            $this->mailer = $slackMailer;
        } else {
            $this->mailer = $swiftMailer;
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(Email $message, bool $disableLogging = false): void
    {
        if ($this->mailer instanceof Gmail) {
            $this->mailer->send($message, $disableLogging);
        } else {
            $this->mailer->send($message);
        }
    }
}
