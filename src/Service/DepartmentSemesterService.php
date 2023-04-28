<?php

namespace App\Service;

use App\Entity\Department;
use App\Entity\Semester;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DepartmentSemesterService
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    /**
     * Tries to get department from the Request and opts to the user's department if none is found.
     * Returns 404 if none can be found this way.
     */
    public function getDepartmentOrThrow404(Request $request, User $user = null): ?Department
    {
        $department = null;
        $departmentId = $request->query->get('department');
        if ($departmentId === null) {
            if ($user !== null) {
                $department = $user->getDepartment();
            }
        } else {
            $department = $this->doctrine->getRepository(Department::class)->find($departmentId);
        }
        if ($department === null) {
            throw new NotFoundHttpException();
        }

        return $department;
    }

    public function getCurrentSemester(): Semester
    {
        return $this->doctrine->getRepository(Semester::class)->findOrCreateCurrentSemester();
    }

    /**
     * Tries to get semester from the Request and opts to the current if none is found.
     * Returns null if the given ID has no corresponding semester.
     */
    public function getSemesterOrThrow404(Request $request): ?Semester
    {
        $semesterId = $request->query->get('semester');
        if ($semesterId === null) {
            $semester = $this->getCurrentSemester();
        } else {
            $semester = $this->doctrine->getRepository(Semester::class)->find($semesterId);
        }

        if ($semester === null) {
            throw new NotFoundHttpException();
        }

        return $semester;
    }
}