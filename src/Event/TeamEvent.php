<?php

namespace App\Event;

use App\Entity\Team;
use Symfony\Contracts\EventDispatcher\Event;

class TeamEvent extends Event
{
    final public const CREATED = 'team.created';
    final public const EDITED = 'team.edited';
    final public const DELETED = 'team.deleted';

    public function __construct(private readonly Team $team, private readonly string $oldTeamEmail)
    {
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
