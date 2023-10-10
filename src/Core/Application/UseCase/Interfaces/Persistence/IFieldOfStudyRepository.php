<?php
namespace App\Core\Application\UseCase\Interfaces\Persistence;

use App\Core\Domain\Entity\FieldOfStudy;

interface IFieldOfStudyRepository {
    public function findByDepartmentId(int $departmentId): array;
    public function save(FieldOfStudy $fieldOfStudy): void;
    public function delete(FieldOfStudy $fieldOfStudy): void;
}