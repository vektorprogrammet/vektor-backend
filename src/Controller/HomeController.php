<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\User;
use App\Service\GeoLocation;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    public function show(GeoLocation $geoLocation): Response
    {
        $assistantsCount = \count($this->getDoctrine()->getRepository(User::class)->findAssistants());
        $teamMembersCount = \count($this->getDoctrine()->getRepository(User::class)->findTeamMembers());

        $departments = $this->getDoctrine()->getRepository(Department::class)->findAll();
        $departmentsWithActiveAdmission = $this->getDoctrine()->getRepository(Department::class)->findAllWithActiveAdmission();
        $departmentsWithActiveAdmission = $geoLocation->sortDepartmentsByDistanceFromClient($departmentsWithActiveAdmission);
        $closestDepartment = $geoLocation->findNearestDepartment($departments);
        $ipWasLocated = $geoLocation->findCoordinatesOfCurrentRequest();

        return $this->render('home/index.html.twig', [
            'assistantCount' => $assistantsCount + 600, // + Estimated number of assistants not registered in website
            'teamMemberCount' => $teamMembersCount + 160, // + Estimated number of team members not registered in website
            'ipWasLocated' => $ipWasLocated,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'closestDepartment' => $closestDepartment,
        ]);
    }
}
