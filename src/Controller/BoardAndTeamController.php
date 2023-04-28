<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\ExecutiveBoard;
use App\Entity\User;
use App\Service\DepartmentSemesterService;
use App\Service\GeoLocation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoardAndTeamController extends AbstractController
{
    public function __construct(
        private readonly GeoLocation $geoLocation,
        private readonly ManagerRegistry $doctrine,
        private readonly DepartmentSemesterService $departmentSemesterService,
    ) {
    }

    #[Route('/team', name: 'team', methods: ['GET'])]
    public function show(): Response
    {
        // Find all departments
        $departments = $this->doctrine
            ->getRepository(Department::class)
            ->findActive();

        $departments = $this->geoLocation->sortDepartmentsByDistanceFromClient($departments);
        $board = $this->doctrine
            ->getRepository(ExecutiveBoard::class)
            ->findBoard();

        $numberOfTeams = 0;
        foreach ($departments as $department) {
            $numberOfTeams += $department->getTeams()->count();
        }

        $departmentStats = [];
        foreach ($departments as $department) {
            $currentSemester = $this->departmentSemesterService->getCurrentSemester();
            $userRepository = $this->doctrine->getRepository(User::class);
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
