<?php

namespace App\Repository;

use App\Entity\AdmissionPeriod;
use App\Entity\Department;
use App\Entity\Semester;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * AdmissionPeriodRepository.
 */
class AdmissionPeriodRepository extends EntityRepository
{
    /**
     * @return AdmissionPeriod[]
     */
    public function findByDepartmentOrderedByTime(Department $department): array
    {
        return $this->createQueryBuilder('ap')
            ->where('ap.department = :department')
            ->join('ap.semester', 's')
            ->addOrderBy('s.year', 'DESC')
            ->addOrderBy('s.semesterTime', 'ASC')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return AdmissionPeriod[]
     */
    public function findByDepartmentAndTime(Department $department, string $time, string $year): array
    {
        return $this->createQueryBuilder('dss')
            ->where('dss.department = :department')
            ->join('semester', 's')
            ->andWhere('s.semesterTime = :time')
            ->andWhere('s.year = :year')
            ->setParameter('department', $department)
            ->setParameter('time', $time)
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByDepartmentAndSemester(Department $department, Semester $semester): ?AdmissionPeriod
    {
        return $this->createQueryBuilder('admissionPeriod')
            ->where('admissionPeriod.department = :department')
            ->andWhere('admissionPeriod.semester = :semester')
            ->setParameter('department', $department)
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneWithActiveAdmissionByDepartment(Department $department, \DateTime $time = null): ?AdmissionPeriod
    {
        if ($time === null) {
            $time = new \DateTime();
        }

        return $this->createQueryBuilder('admissionPeriod')
            ->select('admissionPeriod')
            ->where('admissionPeriod.department = ?1')
            ->andWhere('admissionPeriod.startDate <= :time')
            ->andWhere('admissionPeriod.endDate >= :time')
            ->setParameter(1, $department)
            ->setParameter('time', $time)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
