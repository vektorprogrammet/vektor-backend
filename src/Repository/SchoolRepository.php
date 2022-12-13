<?php

namespace App\Repository;

use App\Entity\Department;
use App\Entity\School;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SchoolRepository extends EntityRepository
{
    /**
     * @return School[]
     */
    public function findActiveSchoolsByDepartment(Department $department)
    {
        return $this->getSchoolsByDepartmentQueryBuilder($department)
            ->andWhere('school.active = true')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return School[]
     */
    public function findInactiveSchoolsByDepartment(Department $department)
    {
        return $this->getSchoolsByDepartmentQueryBuilder($department)
            ->andWhere('school.active = false')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function findActiveSchoolsWithoutCapacity(Department $department)
    {
        $qb = $this->_em->createQueryBuilder();
        $exclude = $qb
            ->select('IDENTITY(capacity.school)')
            ->from('App:SchoolCapacity', 'capacity')
            ->where('capacity.semester = :semester');

        return $this->getSchoolsByDepartmentQueryBuilder($department)
            ->andWhere('school.active = true')
            ->setParameter('semester', $department->getCurrentAdmissionPeriod()->getSemester())
            ->andWhere($qb->expr()->notIn('school.id', $exclude->getDQL()));
    }

    /**
     * @return QueryBuilder
     */
    private function getSchoolsByDepartmentQueryBuilder(Department $department)
    {
        return $this->createQueryBuilder('school')
            ->select('school')
            ->join('school.departments', 'departments')
            ->where('departments = :department')
            ->setParameter('department', $department);
    }
}
