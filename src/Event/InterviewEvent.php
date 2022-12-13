<?php

namespace App\Event;

use App\Entity\Interview;
use Symfony\Contracts\EventDispatcher\Event;

class InterviewEvent extends Event
{
    public const SCHEDULE = 'interview.schedule';
    public const COASSIGN = 'interview.coassign';

    private $interview;
    private $data;

    /**
     * ReceiptEvent constructor.
     */
    public function __construct(Interview $interview, $data = [])
    {
        $this->interview = $interview;
        $this->data = $data;
    }

    /**
     * @return Interview
     */
    public function getInterview()
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
