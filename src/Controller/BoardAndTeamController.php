<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\ExecutiveBoard;
use App\Entity\Semester;
use App\Entity\User;
use App\Service\GeoLocation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class BoardAndTeamController extends BaseController
{
    private GeoLocation $geoLocation;
    protected ManagerRegistry $doctrine;

    public function __construct(GeoLocation $geoLocation, ManagerRegistry $doctrine)
    {
        parent::__construct($doctrine);
        $this->geoLocation = $geoLocation;
        $this->doctrine = $doctrine;
    }

    public function show(): Response
    {
        // Find all departments
        $departments = $this->getDoctrine()->getRepository(Department::class)->findActive();
        $departments = $this->geoLocation->sortDepartmentsByDistanceFromClient($departments);
        $board = $this->doctrine->getRepository(ExecutiveBoard::class)->findBoard();

        $numberOfTeams = 0;
        foreach ($departments as $department) {
            $numberOfTeams += $department->getTeams()->count();
        }

        $departmentStats = array();
        foreach ($departments as $department) {
            $currentSemester = $this->getCurrentSemester();
            $userRepository = $this->doctrine->getRepository(User::class);
            $departmentStats[$department->getCity()] = array(
                'numTeamMembers' => sizeof($userRepository->findUsersInDepartmentWithTeamMembershipInSemester($department, $currentSemester)),
                'numAssistants' => sizeof($userRepository->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $currentSemester)),
            );
        }

        return $this->render('team/board_and_team.html.twig', array(
            'departments' => $departments,
            'board' => $board,
            'numberOfTeams' => $numberOfTeams,
            'departmentStats' => $departmentStats,
        ));
    }
}
