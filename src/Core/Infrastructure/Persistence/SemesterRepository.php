<?php

namespace App\Core\Infrastructure\Persistence;

use App\Core\Domain\Entity\Semester;
use App\Core\Application\UseCase\Interfaces\Persistence\ISemesterRepository;
use App\Core\Application\Util\SemesterUtil;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SemesterRepository extends ServiceEntityRepository implements ISemesterRepository {

    public function __construct(ManagerRegistry $registry, private SemesterUtil $semesterUtil)
    {
        parent::__construct($registry, Semester::class);
    }

    public function findById(int $id): ?Semester
    {
        return $this->find($id);
    }

    public function findSemesterByDate(DateTime $date): ?Semester
    {
        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.year = :year')
            ->andWhere('Semester.semesterTime = :semesterTime')
            ->setParameters([
                'year' => SemesterUtil::timeToYear($date),
                'semesterTime' => SemesterUtil::timeToSemesterTime($date),
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(Semester $semester): void
    {
        $this->getEntityManager()->persist($semester);
        $this->getEntityManager()->flush();
    }

    public function delete(Semester $semester): void
    {
        $this->getEntityManager()->remove($semester);
        $this->getEntityManager()->flush();
    }

}