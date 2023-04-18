<?php

namespace App\Entity;

/**
 * Entity having both department and semester.
 */
interface DepartmentSemesterInterface
{
    public function getDepartment(): ?Department;

    public function getSemester(): ?Semester;
}
