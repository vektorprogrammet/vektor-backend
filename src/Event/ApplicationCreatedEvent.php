<?php

namespace App\Event;

use App\Entity\Application;
use Symfony\Contracts\EventDispatcher\Event;

class ApplicationCreatedEvent extends Event
{
    public const NAME = 'application.admission';

    private $application;


    /**
     * ApplicationAdmissionEvent constructor.
     *
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     */
    public function getApplication(): Application
    {
        return $this->application;
    }
}
