<?php

namespace App\Mailer;

use Symfony\Component\Mime\Email;

interface MailingInterface
{
    public function send(Email $message, bool $disableLogging = false);
}
