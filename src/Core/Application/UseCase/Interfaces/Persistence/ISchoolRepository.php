<?php

namespace App\Core\Application\UseCase\Interfaces\Persistence;

interface ISchoolRepository
{

    public function findByDepartmentId(int $departmentId): array;

}