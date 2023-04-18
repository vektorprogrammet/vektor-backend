<?php

namespace App\Repository;

use App\Entity\Team;
use App\Entity\TeamApplication;
use Doctrine\ORM\EntityRepository;

class TeamApplicationRepository extends EntityRepository
{
    /**
     * @return TeamApplication[]
     */
    public function findByTeam(Team $team)
    {
        return $this->createQueryBuilder('teamApplication')
            ->select('teamApplication')
            ->where('teamApplication.team = :team')
            ->setParameter('team', $team)
            ->getQuery()
            ->getResult();
    }
}
