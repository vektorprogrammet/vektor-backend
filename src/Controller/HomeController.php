<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\AssistantHistory;
use App\Entity\Department;
use App\Entity\User;
use App\Service\GeoLocation;

class HomeController extends BaseController
{
    public function showAction(GeoLocation $geoLocation)
    {
        $assistantsCount = count($this->getDoctrine()->getRepository(User::class)->findAssistants());
        $teamMembersCount = count($this->getDoctrine()->getRepository(User::class)->findTeamMembers());
        $articles = $this->getDoctrine()->getRepository(Article::class)->findStickyAndLatestArticles();

        $departments = $this->getDoctrine()->getRepository(Department::class)->findAll();
        $departmentsWithActiveAdmission = $this->getDoctrine()->getRepository(Department::class)->findAllWithActiveAdmission();
        $departmentsWithActiveAdmission = $geoLocation->sortDepartmentsByDistanceFromClient($departmentsWithActiveAdmission);
        $closestDepartment = $geoLocation->findNearestDepartment($departments);
        $ipWasLocated = $geoLocation->findCoordinatesOfCurrentRequest();

        $femaleAssistantCount = $this->getDoctrine()->getRepository(AssistantHistory::class)->numFemale();
        $maleAssistantCount = $this->getDoctrine()->getRepository(AssistantHistory::class)->numMale();

        return $this->render('home/index.html.twig', [
            'assistantCount' => $assistantsCount + 600, // + Estimated number of assistants not registered in website
            'teamMemberCount' => $teamMembersCount + 160, // + Estimated number of team members not registered in website
            'femaleAssistantCount' => $femaleAssistantCount,
            'maleAssistantCount' => $maleAssistantCount,
            'ipWasLocated' => $ipWasLocated,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'closestDepartment' => $closestDepartment,
            'news' => $articles,
        ]);
    }

    public function postAction()
    {
        return $this->redirect("https://www.youtube.com/watch?v=dQw4w9WgXcQ?autoplay=1", 301);
    }
}
