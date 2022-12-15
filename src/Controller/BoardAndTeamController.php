<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\ExecutiveBoard;
use App\Entity\User;
use App\Service\GeoLocation;
use Symfony\Component\HttpFoundation\Response;

class BoardAndTeamController extends BaseController
{
    public function __construct(private readonly GeoLocation $geoLocation)
    {
    }

    public function show(): Response
    {
        // Find all departments
        $departments = $this->getDoctrine()->getRepository(Department::class)->findActive();
        $departments = $this->geoLocation->sortDepartmentsByDistanceFromClient($departments);
        $board = $this->getDoctrine()->getRepository(ExecutiveBoard::class)->findBoard();

        $numberOfTeams = 0;
        foreach ($departments as $department) {
            $numberOfTeams += $department->getTeams()->count();
        }

        $departmentStats = [];
        foreach ($departments as $department) {
            $currentSemester = $this->getCurrentSemester();
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $departmentStats[$department->getCity()] = [
                'numTeamMembers' => sizeof($userRepository->findUsersInDepartmentWithTeamMembershipInSemester($department, $currentSemester)),
                'numAssistants' => sizeof($userRepository->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $currentSemester)),
            ];
        }

        return $this->render('team/board_and_team.html.twig', [
            'departments' => $departments,
            'board' => $board,
            'numberOfTeams' => $numberOfTeams,
            'departmentStats' => $departmentStats,
        ]);
    }
}
