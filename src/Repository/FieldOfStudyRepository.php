<?php

namespace App\Repository;

use App\Entity\Department;
use App\Entity\FieldOfStudy;
use Doctrine\ORM\EntityRepository;

class FieldOfStudyRepository extends EntityRepository
{
    /**
     * @return FieldOfStudy[]
     */
    public function findByDepartment(Department $department)
    {
        return $this->createQueryBuilder('fieldOfStudy')
            ->select('fieldOfStudy')
            ->where('fieldOfStudy.department = :department')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }
}
