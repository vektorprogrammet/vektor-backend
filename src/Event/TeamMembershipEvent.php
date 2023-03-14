<?php

namespace App\Event;

use App\Entity\TeamMembership;
use Symfony\Contracts\EventDispatcher\Event;

class TeamMembershipEvent extends Event
{
    final public const CREATED = 'team_membership.created';
    final public const EDITED = 'team_membership.edited';
    final public const DELETED = 'team_membership.deleted';
    final public const EXPIRED = 'team_membership.expired';

    public function __construct(private readonly TeamMembership $teamMembership)
    {
    }

    public function getTeamMembership(): TeamMembership
    {
        return $this->teamMembership;
    }
}
