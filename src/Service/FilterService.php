<?php

namespace App\Service;

use App\Entity\Department;
use App\Entity\TeamInterface;
use App\Entity\TeamMembershipInterface;

class FilterService
{
    /**
     * Returns only memberships in $team.
     *
     * @param TeamMembershipInterface[] $teamMemberships
     * @param TeamInterface             $team
     *
     * @return TeamMembershipInterface[]
     */
    public function filterTeamMembershipsByTeam($teamMemberships, $team): array
    {
        $filtered = [];
        foreach ($teamMemberships as $teamMembership) {
            if ($teamMembership->getTeam() === $team) {
                $filtered[] = $teamMembership;
            }
        }

        return $filtered;
    }

    /**
     * Returns only departments with active admission set to $hasActiveAdmission.
     *
     * @param Department[] $departments
     *
     * @return Department[]
     */
    public function filterDepartmentsByActiveAdmission(array $departments, bool $hasActiveAdmission): array
    {
        $filtered = [];
        foreach ($departments as $department) {
            $currentSemester = $department->getCurrentAdmissionPeriod();
            $departmentHasActiveAdmission = (null !== $currentSemester && $currentSemester->hasActiveAdmission());
            if ($departmentHasActiveAdmission === $hasActiveAdmission) {
                $filtered[] = $department;
            }
        }

        return $filtered;
    }
}
