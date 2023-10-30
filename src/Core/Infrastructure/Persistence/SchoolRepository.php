<?php

namespace App\Core\Infrastructure\Persistence;

use App\Core\Application\UseCase\Interfaces\Persistence\ISchoolRepository;
use App\Core\Domain\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SchoolRepository extends ServiceEntityRepository implements ISchoolRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    public function findByDepartmentId(int $departmentId): array
    {
        $result = $this->createQueryBuilder('school')
            ->select('school')
            ->where('school.department = :department')
            ->setParameter('department', $departmentId)
            ->getQuery()
            ->getResult();

        return $result;
    }
}
