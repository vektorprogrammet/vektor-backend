<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Semester;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseController extends AbstractController
{
    /**
     * Tries to get department from the Request and opts to the user's department if none is found.
     * Returns null if none can be found this way.
     */
    public function getDepartment(Request $request): ?Department
    {
        $department = null;
        $departmentId = $request->query->get('department');
        if ($departmentId === null) {
            if ($this->getUser() !== null) {
                $department = $this->getUser()->getDepartment();
            }
        } else {
            $department = $this->getDoctrine()->getRepository(Department::class)->find($departmentId);
        }

        return $department;
    }

    /**
     * Tries to get semester from the Request and opts to the current if none is found.
     * Returns null if the given ID has no corresponding semester.
     */
    public function getSemester(Request $request): ?Semester
    {
        $semesterId = $request->query->get('semester');
        if ($semesterId === null) {
            $semester = $this->getCurrentSemester();
        } else {
            $semester = $this->getDoctrine()->getRepository(Semester::class)->find($semesterId);
        }

        return $semester;
    }

    /**
     * 404's if department is null in the request and for the user, or if a wrong department ID is given.
     */
    public function getDepartmentOrThrow404(Request $request): Department
    {
        $department = $this->getDepartment($request);
        if ($department === null) {
            throw new NotFoundHttpException();
        }

        return $department;
    }

    public function getSemesterOrThrow404(Request $request): Semester
    {
        $semester = $this->getSemester($request);
        if ($semester === null) {
            throw new NotFoundHttpException();
        }

        return $semester;
    }

    public function getCurrentSemester(): Semester
    {
        return $this->getDoctrine()->getRepository(Semester::class)->findOrCreateCurrentSemester();
    }
}
