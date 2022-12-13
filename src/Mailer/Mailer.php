<?php

namespace App\Mailer;

use App\Google\Gmail;
use App\Service\SlackMailer;

class Mailer implements MailerInterface
{
    private $mailer;

    public function __construct(string $env, Gmail $gmail, \Swift_Mailer $swiftMailer, SlackMailer $slackMailer)
    {
        if ('prod' === $env) {
            $this->mailer = $gmail;
        } elseif ('staging' === $env) {
            $this->mailer = $slackMailer;
        } else {
            $this->mailer = $swiftMailer;
        }
    }

    public function send(\Swift_Message $message, bool $disableLogging = false)
    {
        if ($this->mailer instanceof Gmail) {
            $this->mailer->send($message, $disableLogging);
        } else {
            $this->mailer->send($message);
        }
    }
}
