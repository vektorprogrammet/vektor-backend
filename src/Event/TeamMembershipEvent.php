<?php

namespace App\Event;

use App\Entity\TeamMembership;
use Symfony\Contracts\EventDispatcher\Event;

class TeamMembershipEvent extends Event
{
    public const CREATED = 'team_membership.created';
    public const EDITED = 'team_membership.edited';
    public const DELETED = 'team_membership.deleted';
    public const EXPIRED = 'team_membership.expired';

    private $teamMembership;

    /**
     */
    public function __construct(TeamMembership $teamMembership)
    {
        $this->teamMembership = $teamMembership;
    }

    /**
     */
    public function getTeamMembership(): TeamMembership
    {
        return $this->teamMembership;
    }
}
