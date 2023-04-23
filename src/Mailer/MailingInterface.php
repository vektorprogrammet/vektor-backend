<?php

namespace App\Mailer;

interface MailingInterface
{
    public function send(\Swift_Message $message, bool $disableLogging = false);
}
