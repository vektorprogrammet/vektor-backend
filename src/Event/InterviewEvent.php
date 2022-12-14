<?php

namespace App\Event;

use App\Entity\Interview;
use Symfony\Contracts\EventDispatcher\Event;

class InterviewEvent extends Event
{
    public const SCHEDULE = 'interview.schedule';
    public const COASSIGN = 'interview.coassign';

    private Interview $interview;
    private array $data;

    /**
     * ReceiptEvent constructor.
     */
    public function __construct(Interview $interview, $data = [])
    {
        $this->interview = $interview;
        $this->data = $data;
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
