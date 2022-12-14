<?php

namespace App\Event;

use App\Entity\Team;
use Symfony\Contracts\EventDispatcher\Event;

class TeamEvent extends Event
{
    public const CREATED = 'team.created';
    public const EDITED = 'team.edited';
    public const DELETED = 'team.deleted';

    private Team $team;
    private string $oldTeamEmail;

    public function __construct(Team $team, string $oldTeamEmail)
    {
        $this->team = $team;
        $this->oldTeamEmail = $oldTeamEmail;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getOldTeamEmail(): string
    {
        return $this->oldTeamEmail;
    }
}
