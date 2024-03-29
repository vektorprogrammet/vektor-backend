<?php

namespace App\Repository;

use App\Entity\Department;
use App\Entity\Semester;
use App\Entity\Team;
use App\Entity\TeamMembership;
use App\Entity\User;
use App\Utils\SemesterUtil;
use Doctrine\ORM\EntityRepository;

class TeamMembershipRepository extends EntityRepository
{
    /**
     * @return TeamMembership[]
     */
    public function findByTeam(Team $team): array
    {
        return $this->createQueryBuilder('team_membership')
            ->where('team_membership.team = :team')
            ->setParameter('team', $team)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TeamMembership[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('team_membership')
            ->select('team_membership')
            ->where('team_membership.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TeamMembership[]
     */
    public function findActiveTeamMemberships(): array
    {
        $today = new \DateTime('now');

        $teamMemberships = $this->createQueryBuilder('tm')
            ->select('tm')
            ->join('tm.startSemester', 'startSemester')
            ->leftJoin('tm.endSemester', 'endSemester')
            ->where('startSemester.semesterStartDate < :today')
            ->andWhere('endSemester is null or endSemester.semesterEndDate > :today')
            ->setParameter('today', $today)
            ->getQuery()
            ->getResult();

        return $this->filterOutInactive($teamMemberships);
    }

    /**
     * @return TeamMembership[]
     */
    public function findActiveTeamMembershipsByTeam(Team $team): array
    {
        $teamMemberships = $this->createQueryBuilder('tm')
            ->select('tm')
            ->join('tm.startSemester', 'startSemester')
            ->leftJoin('tm.endSemester', 'endSemester')
            ->andWhere('tm.team = :team')
            ->setParameter('team', $team)
            ->getQuery()
            ->getResult();

        return $this->filterOutInactive($teamMemberships);
    }

    /**
     * @param TeamMembership[] $teamMemberships
     *
     * @return TeamMembership[]
     */
    private function filterOutInactive(array $teamMemberships): array
    {
        $today = new \DateTime('now');
        $currentSemester = (new Semester())
            ->setYear(SemesterUtil::timeToYear($today))
            ->setSemesterTime(SemesterUtil::timeToSemesterTime($today));

        return $this->filterNotInSemester($teamMemberships, $currentSemester);
    }

    /**
     * @param TeamMembership[] $teamMemberships
     *
     * @return TeamMembership[]
     */
    public function filterNotInSemester(array $teamMemberships, Semester $semester): array
    {
        return array_filter($teamMemberships, fn (TeamMembership $teamMembership) => $semester->isBetween($teamMembership->getStartSemester(), $teamMembership->getEndSemester()));
    }

    /**
     * @return TeamMembership[]
     */
    public function findActiveTeamMembershipsByTeamAndUser(Team $team, User $user): array
    {
        $teamMemberships = $this->createQueryBuilder('tm')
            ->select('tm')
            ->join('tm.startSemester', 'startSemester')
            ->leftJoin('tm.endSemester', 'endSemester')
            ->andWhere('tm.team = :team')
            ->andWhere('tm.user = :user')
            ->setParameter('team', $team)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $this->filterOutInactive($teamMemberships);
    }

    /**
     * @return TeamMembership[]
     */
    public function findInactiveTeamMembershipsByTeam(Team $team): array
    {
        $allMembers = $this->findByTeam($team);
        $activeMembers = $this->findActiveTeamMembershipsByTeam($team);

        return array_diff($allMembers, $activeMembers);
    }

    /**
     * @return TeamMembership[]
     */
    public function findActiveTeamMembershipsByUser(User $user): array
    {
        $teamMemberships = $this->createQueryBuilder('tm')
            ->select('tm')
            ->join('tm.startSemester', 'startSemester')
            ->leftJoin('tm.endSemester', 'endSemester')
            ->andWhere('tm.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $this->filterOutInactive($teamMemberships);
    }

    /**
     * @return TeamMembership[]
     */
    public function findTeamMembershipsByDepartment(Department $department): array
    {
        return $this->createQueryBuilder('wh')
            ->join('wh.team', 'team')
            ->where('team.department = :department')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }
}
