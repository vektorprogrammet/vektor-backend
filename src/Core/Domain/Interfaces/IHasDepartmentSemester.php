<?php

namespace App\Core\Domain\Interfaces;

use App\Core\Domain\Entity\Department;
use App\Core\Domain\Entity\Semester;

interface IHasDepartmentSemester
{
    public function getDepartment(): ?Department;

    public function getSemester(): ?Semester;
}