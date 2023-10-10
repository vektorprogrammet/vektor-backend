<?php
namespace App\Core\Application\UseCase\Interfaces\Persistence;

use App\Core\Domain\Entity\Department;

interface IDepartmentRepository {
    public function findById(int $id): ?Department;
    public function findAll();
    public function save(Department $department): void;
    public function delete(Department $department): void;
}