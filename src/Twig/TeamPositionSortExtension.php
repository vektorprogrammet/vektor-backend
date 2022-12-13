<?php

namespace App\Twig;

use App\Entity\TeamInterface;
use App\Entity\User;
use App\Service\FilterService;
use App\Service\Sorter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TeamPositionSortExtension extends AbstractExtension
{
    private $sorter;
    private $filterService;
    public function __construct(Sorter $sorter, FilterService $filterService)
    {
        $this->sorter = $sorter;
        $this->filterService = $filterService;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('team_position_sort', [$this, 'teamPositionSortFilter']),
        ];
    }

    /**
     * Sorts a list of users by their positions in the given TeamInterface $team,
     * ordered as follows: "leder" < "nestleder" < "aaa" < "zzz" < "".
     * For users having multiple positions within $team, their list of positions
     * is also sorted in the same fashion.
     *
     * Note: Any memberships to other teams are filtered out,
     * i.e removed from the $user object!
     *
     * @param User[] $users
     * @param TeamInterface $team
     *
     * @return User[]
     */
    public function teamPositionSortFilter($users, TeamInterface $team)
    {
        // Filter out any other team memberships and sort them by importance
        foreach ($users as $user) {
            $memberships = $this->filterService->filterTeamMembershipsByTeam($user->getActiveMemberships(), $team);
            $this->sorter->sortTeamMembershipsByPosition($memberships);
            $user->setMemberships($memberships);
        }

        $this->sorter->sortUsersByActivePositions($users);

        return $users;
    }
}
