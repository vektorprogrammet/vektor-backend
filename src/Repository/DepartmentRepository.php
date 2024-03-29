<?php

namespace App\Repository;

use App\Entity\Department;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class DepartmentRepository extends EntityRepository
{
    public function findAllWithActiveAdmission()
    {
        return array_filter($this->findAll(), function (Department $department) {
            $semester = $department->getCurrentAdmissionPeriod();

            return $semester !== null && $semester->hasActiveAdmission();
        });
    }

    public function findDepartmentByShortName($shortName)
    {
        return $this->getEntityManager()->createQuery('
            SELECT d
            FROM App:Department d
            WHERE lower(d.shortName) = lower(:shortName)
        ')
            ->setParameter('shortName', $shortName)
            ->getOneOrNullResult();
    }

    /**
     * @return QueryBuilder
     */
    public function queryForActive()
    {
        return $this->createQueryBuilder('Department')
            ->select('Department')
            ->where('Department.active = true');
    }

    /**
     * @return Department[]
     */
    public function findActive()
    {
        return $this->queryForActive()->getQuery()->getResult();
    }

    public function findOneByCityCaseInsensitive($city)
    {
        return $this->createQueryBuilder('Department')
            ->select('Department')
            ->where('upper(Department.city) = upper(:city)')
            ->setParameter('city', $city)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
