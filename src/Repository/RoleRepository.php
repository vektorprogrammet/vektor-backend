<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RoleRepository extends EntityRepository
{
    /**
     * @param string $roleName
     *
     * @return string
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findByRoleName(string $roleName): Role
    {
        return $this->createQueryBuilder('role')
            ->select('role')
            ->where('role.role = :roleName')
            ->setParameter('roleName', $roleName)
            ->getQuery()
            ->getSingleResult();
    }
}
