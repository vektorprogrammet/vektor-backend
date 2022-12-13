<?php

namespace App\Event;

use App\Entity\TeamApplication;
use Symfony\Contracts\EventDispatcher\Event;

class TeamApplicationCreatedEvent extends Event
{
    public const NAME = 'team_application.created';
    /**
     * @var TeamApplication
     */
    private $teamApplication;

    /**
     * TeamApplicationCreatedEvent constructor.
     */
    public function __construct(TeamApplication $teamApplication)
    {
        $this->teamApplication = $teamApplication;
    }

    /**
     * @return TeamApplication
     */
    public function getTeamApplication()
    {
        return $this->teamApplication;
    }
}
