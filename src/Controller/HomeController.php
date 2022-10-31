<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Entity\Department;
use App\Entity\User;
use App\Service\GeoLocation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function show(GeoLocation $geoLocation): Response
    {
        $assistantsCount = count($this->doctrine->getRepository(User::class)->findAssistants());
        $teamMembersCount = count($this->doctrine->getRepository(User::class)->findTeamMembers());

        $departments = $this->doctrine->getRepository(Department::class)->findAll();
        $departmentsWithActiveAdmission = $this->doctrine->getRepository(Department::class)->findAllWithActiveAdmission();
        $departmentsWithActiveAdmission = $geoLocation->sortDepartmentsByDistanceFromClient($departmentsWithActiveAdmission);
        $closestDepartment = $geoLocation->findNearestDepartment($departments);
        $ipWasLocated = $geoLocation->findCoordinatesOfCurrentRequest();

        $femaleAssistantCount = $this->doctrine->getRepository(AssistantHistory::class)->numFemale();
        $maleAssistantCount = $this->doctrine->getRepository(AssistantHistory::class)->numMale();

        return $this->render('home/index.html.twig', [
            'assistantCount' => $assistantsCount + 600, // + Estimated number of assistants not registered in website
            'teamMemberCount' => $teamMembersCount + 160, // + Estimated number of team members not registered in website
            'femaleAssistantCount' => $femaleAssistantCount,
            'maleAssistantCount' => $maleAssistantCount,
            'ipWasLocated' => $ipWasLocated,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'closestDepartment' => $closestDepartment,
        ]);
    }

    public function post(): RedirectResponse
    {
        return $this->redirect("https://www.youtube.com/watch?v=dQw4w9WgXcQ?autoplay=1", 301);
    }
}
