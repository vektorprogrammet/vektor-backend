<?php

namespace App\Event;

use App\Entity\Application;
use Symfony\Contracts\EventDispatcher\Event;

class ApplicationCreatedEvent extends Event
{
    final public const NAME = 'application.admission';

    /**
     * ApplicationAdmissionEvent constructor.
     */
    public function __construct(private readonly Application $application)
    {
    }

    public function getApplication(): Application
    {
        return $this->application;
    }
}
