<?php

namespace App\Event;

use App\Entity\TeamInterest;
use Symfony\Contracts\EventDispatcher\Event;

class TeamInterestCreatedEvent extends Event
{
    final public const NAME = 'team_interest.created';

    public function __construct(private readonly TeamInterest $teamInterest)
    {
    }

    /**
     * @return TeamInterest
     */
    public function getTeamInterest()
    {
        return $this->teamInterest;
    }
}
