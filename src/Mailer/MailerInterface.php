<?php

namespace App\Mailer;

interface MailerInterface
{
    public function send(\Swift_Message $message, bool $disableLogging = false);
}
