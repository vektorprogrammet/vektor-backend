<?php

namespace App\Event;

use App\Entity\TeamApplication;
use Symfony\Contracts\EventDispatcher\Event;

class TeamApplicationCreatedEvent extends Event
{
    final public const NAME = 'team_application.created';

    public function __construct(private readonly TeamApplication $teamApplication)
    {
    }

    public function getTeamApplication(): TeamApplication
    {
        return $this->teamApplication;
    }
}
