<?php

namespace App\Core\Infrastructure\Persistence;

use App\Core\Domain\Entity\AdmissionPeriod;
use App\Core\Application\UseCase\Interfaces\Persistence\IAdmissionPeriodRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AdmissionPeriodRepository extends ServiceEntityRepository implements IAdmissionPeriodRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdmissionPeriod::class);
    }

    public function findById(int $id): ?AdmissionPeriod
    {
        return $this->find($id);
    }

    public function findActiveByDepartmentId(int $departmentId): ?AdmissionPeriod
    {
        $now = new \DateTime();
        //Find one by where start date is less than now and end date is greater than now
        return $this->createQueryBuilder('admissionPeriod')
            ->andWhere('admissionPeriod.department = :departmentId')
            ->andWhere('admissionPeriod.startDate <= :now')
            ->andWhere('admissionPeriod.endDate >= :now')
            ->setParameter('departmentId', $departmentId)
            ->setParameter('now', $now)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(AdmissionPeriod $admissionPeriod): void
    {
        $this->getEntityManager()->persist($admissionPeriod);
        $this->getEntityManager()->flush();
    }

    public function delete(AdmissionPeriod $admissionPeriod): void
    {
        $this->getEntityManager()->remove($admissionPeriod);
        $this->getEntityManager()->flush();
    }
}
