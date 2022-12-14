<?php

namespace App\Event;

use App\Entity\Application;
use Symfony\Contracts\EventDispatcher\Event;

class InterviewConductedEvent extends Event
{
    public const NAME = 'interview.conducted';

    private Application $application;

    /**
     * InterviewConductedEvent constructor.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }
}
