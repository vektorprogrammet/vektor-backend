<?php

namespace App\Event;

use App\Entity\TeamApplication;
use Symfony\Contracts\EventDispatcher\Event;

class TeamApplicationCreatedEvent extends Event
{
    public const NAME = 'team_application.created';
    private TeamApplication $teamApplication;

    /**
     * TeamApplicationCreatedEvent constructor.
     */
    public function __construct(TeamApplication $teamApplication)
    {
        $this->teamApplication = $teamApplication;
    }

    public function getTeamApplication(): TeamApplication
    {
        return $this->teamApplication;
    }
}
