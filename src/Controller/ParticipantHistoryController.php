<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Entity\TeamMembership;
use App\Role\Roles;
use App\Service\DepartmentSemesterService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantHistoryController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly DepartmentSemesterService $departmentSemesterService,
    ) {
    }

    #[Route('/kontrollpanel/deltakerhistorikk', name: 'participanthistory_show', methods: ['GET'])]
    public function show(Request $request): ?Response
    {
        $user = $this->getUser();
        $department = $this->departmentSemesterService->getDepartmentOrThrow404($request, $user);
        $semester = $this->departmentSemesterService->getSemesterOrThrow404($request);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $department !== $this->getUser()->getDepartment()) {
            throw $this->createAccessDeniedException();
        }

        // Find all team memberships by department
        $teamMemberships = $this->doctrine
            ->getRepository(TeamMembership::class)
            ->findTeamMembershipsByDepartment($department);

        // Find all assistantHistories by department
        $assistantHistories = $this->doctrine
            ->getRepository(AssistantHistory::class)
            ->findByDepartmentAndSemester($department, $semester);

        return $this->render('participant_history/index.html.twig', [
            'teamMemberships' => $teamMemberships,
            'assistantHistories' => $assistantHistories,
            'semester' => $semester,
            'department' => $department,
        ]);
    }
}
