<?php

namespace App\Core\Infrastructure\Persistence;

use App\Core\Application\UseCase\Interfaces\Persistence\IDepartmentRepository;
use App\Core\Domain\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DepartmentRepository extends ServiceEntityRepository implements IDepartmentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    public function findById(int $id): ?Department
    {
        return $this->find($id);
    }

    public function save(Department $department): void
    {
        // Attach the department to the entity manager if it is not attached yet
        // This is necessary because Doctrine will add a new department instead of updating it
        if ($department->getId() !== null) {
            $attachedDepartment = $this->find($department->getId());
            $attachedDepartment->setName($department->getName());
            $attachedDepartment->setActive($department->isActive());
            $attachedDepartment->setAdmissionPeriods($department->getAdmissionPeriods());
            $department = $attachedDepartment;
        }

        $this->getEntityManager()->persist($department);
        $this->getEntityManager()->flush();
    }

    public function delete(Department $department): void
    {
        $this->getEntityManager()->remove($department);
        $this->getEntityManager()->flush();
    }
}
