<?php

namespace App\Event;

use App\Entity\TeamMembership;
use Symfony\Contracts\EventDispatcher\Event;

class TeamMembershipEvent extends Event
{
    const CREATED = 'team_membership.created';
    const EDITED = 'team_membership.edited';
    const DELETED = 'team_membership.deleted';
    const EXPIRED = 'team_membership.expired';

    private $teamMembership;

    /**
     * @param TeamMembership $teamMembership
     */
    public function __construct(TeamMembership $teamMembership)
    {
        $this->teamMembership = $teamMembership;
    }

    /**
     * @return TeamMembership
     */
    public function getTeamMembership(): TeamMembership
    {
        return $this->teamMembership;
    }
}
