<?php

namespace App\Sms;

class Sms
{
    private string $sender;
    private string $message;
    private array $recipients;

    public function setSender(string $sender)
    {
        $this->sender = $sender;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setRecipients(array $recipients)
    {
        $this->recipients = $recipients;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function getRecipientsString(): string
    {
        $recipientsString = '';
        for ($i = 0; $i < count($this->recipients); ++$i) {
            $recipientsString .= $this->recipients[$i];
            if ($i !== count($this->recipients) - 1) {
                $recipientsString .= ', ';
            }
        }

        return $recipientsString;
    }
}
