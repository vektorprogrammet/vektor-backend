<?php

namespace App\Controller;

use App\Entity\Team;
use App\Role\Roles;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamController extends BaseController
{
    public function show(Team $team): Response
    {
        if (!$team->isActive() && !$this->isGranted(Roles::TEAM_MEMBER)) {
            throw new NotFoundHttpException('Team not found');
        }

        return $this->render('team/team_page.html.twig', [
            'team' => $team,
        ]);
    }

    public function showByDepartmentAndTeam($departmentCity, $teamName): Response
    {
        $teams = $this->getDoctrine()->getRepository(Team::class)->findByCityAndName($departmentCity, $teamName);
        if ((is_countable($teams) ? count($teams) : 0) !== 1) {
            throw new NotFoundHttpException('Team not found');
        }

        return $this->show($teams[0]);
    }
}
