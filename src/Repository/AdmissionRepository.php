<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class AdmissionRepository extends EntityRepository
{
    /**
     * Finds all applications that have a conducted interview.
     *
     * @param null $department
     * @param null $semester
     *
     * @return array
     */
    public function findInterviewedApplicants($department = null, $semester = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->join('a.statistic', 'stat')
            ->join('stat.semester', 'sem')
            ->join('sem.department', 'd')
            ->join('a.interview', 'i')
            ->where('i.interviewed = 1');

        if (null !== $department) {
            $qb->andWhere('d = :department')
                ->setParameter('department', $department);
        }

        if (null !== $semester) {
            $qb->andWhere('sem = :semester')
                ->setParameter('semester', $semester);
        }
        $qb->orderBy('a.userCreated', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Finds all applications that have been assigned an interview that has not yet been conducted.
     *
     * @param null $department
     * @param null $semester
     *
     * @return array
     */
    public function findAssignedApplicants($department = null, $semester = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->join('a.statistic', 'stat')
            ->join('stat.semester', 'sem')
            ->join('sem.department', 'd')
            ->join('a.interview', 'i')
            ->where('i.interviewed = 0');

        if (null !== $department) {
            $qb->andWhere('d = :department')
                ->setParameter('department', $department);
        }

        if (null !== $semester) {
            $qb->andWhere('sem = :semester')
                ->setParameter('semester', $semester);
        }

        return $qb->getQuery()->getResult();
    }

    public function numOfApplications()
    {
        return $this->createQueryBuilder('ApplicationStatistic')
            ->select('count(ApplicationStatistic.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function numOfGender($gender)
    {
        return $this->createQueryBuilder('ApplicationStatistic')
            ->select('count(ApplicationStatistic.gender)')
            ->where('ApplicationStatistic.gender = :gender')
            ->setParameter('gender', $gender)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function numOfPreviousParticipation($participated)
    {
        return $this->createQueryBuilder('ApplicationStatistic')
            ->select('count(ApplicationStatistic.gender)')
            ->where('ApplicationStatistic.previousParticipation = :participated')
            ->setParameter('participated', $participated)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
