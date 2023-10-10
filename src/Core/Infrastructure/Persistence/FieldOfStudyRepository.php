<?php

namespace App\Core\Infrastructure\Persistence;

use App\Core\Application\UseCase\Interfaces\Persistence\IFieldOfStudyRepository;
use App\Core\Domain\Entity\FieldOfStudy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class FieldOfStudyRepository extends ServiceEntityRepository implements IFieldOfStudyRepository
{

    public function __construct(\Doctrine\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, FieldOfStudy::class);
    }


    public function findByDepartmentId(int $departmentId): array
    {
        $result = $this->createQueryBuilder('fieldOfStudy')
            ->select('fieldOfStudy')
            ->where('fieldOfStudy.department = :department')
            ->setParameter('department', $departmentId)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findById(int $id)
    {
        return $this->find($id);
    }

    public function save(FieldOfStudy $fieldOfStudy): void
    {
        $this->getEntityManager()->persist($fieldOfStudy);
        $this->getEntityManager()->flush();
    }

    public function delete(FieldOfStudy $fieldOfStudy): void
    {
        $this->getEntityManager()->remove($fieldOfStudy);
        $this->getEntityManager()->flush();
    }
}
