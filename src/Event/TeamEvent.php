<?php

namespace App\Event;

use App\Entity\Team;
use Symfony\Contracts\EventDispatcher\Event;

class TeamEvent extends Event
{
    public const CREATED = 'team.created';
    public const EDITED = 'team.edited';
    public const DELETED = 'team.deleted';

    private $team;
    private $oldTeamEmail;

    /**
     * @param string $oldTeamEmail
     */
    public function __construct(Team $team, $oldTeamEmail)
    {
        $this->team = $team;
        $this->oldTeamEmail = $oldTeamEmail;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @return string
     */
    public function getOldTeamEmail()
    {
        return $this->oldTeamEmail;
    }
}
