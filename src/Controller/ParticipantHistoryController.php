<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Entity\TeamMembership;
use App\Role\Roles;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ParticipantHistoryController extends BaseController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    public function show(Request $request): ?Response
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $department !== $this->getUser()->getDepartment()) {
            throw $this->createAccessDeniedException();
        }

        // Find all team memberships by department
        $teamMemberships = $this->doctrine->getRepository(TeamMembership::class)->findTeamMembershipsByDepartment($department);

        // Find all assistantHistories by department
        $assistantHistories = $this->doctrine->getRepository(AssistantHistory::class)->findByDepartmentAndSemester($department, $semester);

        return $this->render('participant_history/index.html.twig', [
            'teamMemberships' => $teamMemberships,
            'assistantHistories' => $assistantHistories,
            'semester' => $semester,
            'department' => $department,
        ]);
    }
}
