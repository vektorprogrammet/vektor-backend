<?php

namespace App\Service;

use App\Entity\Department;
use Monolog\Logger;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Component\Notifier\NotifierInterface;

class SlackMessenger
{
    /**
     * SlackMessenger constructor.
     */
    public function __construct(private readonly NotifierInterface $slackClient, private readonly string $notificationChannel, private readonly string $logChannel, private readonly bool $disableDelivery, private readonly Logger $logger)
    {
    }

    public function notify(string $messageBody)
    {
    }

    public function log(string $messageBody, array $attachmentData = [])
    {
        /*
        $message = $this->slackClient->createMessage();
        $message->to($this->logChannel);
        $attachment = $this->createAttachment($attachmentData);

        if (empty($attachmentData) || $attachment === null) {
            $message->setText($messageBody);
        } else {
            $message->setAttachments([$attachment]);
        }

        $this->send($message);
        */
    }

    public function messageDepartment(string $messageBody, Department $department)
    {
        /*
        if (!$department->getSlackChannel()) {
            return;
        }

        $message = $this->slackClient->createMessage();

        $message
            ->to($department->getSlackChannel())
            ->setText($messageBody);

        $this->send($message);
        */
    }

    public function send(object $message)
    {
        /*
        if ($message->getChannel() === null) {
            $message->setChannel($this->logChannel);
        }

        if (!$this->disableDelivery) {
            try {
                $this->slackClient->sendMessage($message);
            } catch (Exception $e) {
                $this->logger->error("Sending message to Slack failed! {$e->getMessage()}");
            }
        }

        $this->logger->info("Slack message sent to {$message->getChannel()}: {$message->getText()}");
        */
    }

    public function createMessage(): object // previously imported Message
    {
        /*
        return $this->slackClient->createMessage();
        */
        return new Object_();
    }
}
