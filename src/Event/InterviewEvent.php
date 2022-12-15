<?php

namespace App\Event;

use App\Entity\Interview;
use Symfony\Contracts\EventDispatcher\Event;

class InterviewEvent extends Event
{
    final public const SCHEDULE = 'interview.schedule';
    final public const COASSIGN = 'interview.coassign';

    /**
     * ReceiptEvent constructor.
     */
    public function __construct(private readonly Interview $interview, private $data = [])
    {
    }

    public function getInterview(): Interview
    {
        return $this->interview;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
