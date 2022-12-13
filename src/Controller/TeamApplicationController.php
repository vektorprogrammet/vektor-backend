<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\TeamApplication;
use App\Entity\TeamMembership;
use App\Event\TeamApplicationCreatedEvent;
use App\Form\Type\TeamApplicationType;
use App\Role\Roles;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

class TeamApplicationController extends BaseController
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function showApplication(TeamApplication $application): Response
    {
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->getDoctrine()->getRepository(TeamMembership::class)->findActiveTeamMembershipsByTeamAndUser($application->getTeam(), $user);
        if (empty($activeUserHistoriesInTeam) && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw new AccessDeniedException();
        }

        return $this->render('team_admin/show_application.html.twig', [
            'application' => $application,
        ]);
    }

    public function showAllApplications(Team $team): Response
    {
        $applications = $this->getDoctrine()->getRepository(TeamApplication::class)->findByTeam($team);
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->getDoctrine()->getRepository(TeamMembership::class)->findActiveTeamMembershipsByTeamAndUser($team, $user);
        if (empty($activeUserHistoriesInTeam) && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw new AccessDeniedException();
        }

        return $this->render('team_admin/show_applications.html.twig', [
            'applications' => $applications,
            'team' => $team,
        ]);
    }

    public function deleteTeamApplicationById(TeamApplication $teamApplication): RedirectResponse
    {
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($teamApplication);
        $manager->flush();

        return $this->redirectToRoute('team_application_show_all', ['id' => $teamApplication->getTeam()->getId()]);
    }

    public function show(Team $team, Request $request)
    {
        if (!$team->getAcceptApplicationAndDeadline()) {
            throw new NotFoundHttpException();
        }
        $teamApplication = new TeamApplication();
        $form = $this->createForm(TeamApplicationType::class, $teamApplication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $team->getAcceptApplicationAndDeadline()) {
            $teamApplication->setTeam($team);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($teamApplication);
            $manager->flush();

            $this->eventDispatcher->dispatch(new TeamApplicationCreatedEvent($teamApplication), TeamApplicationCreatedEvent::NAME);

            return $this->redirectToRoute('team_application_confirmation', [
                'team_name' => $team->getName(),
            ]);
        }

        return $this->render('team/team_application.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $team_name
     * @return Response
     */
    public function confirmation($team_name): Response
    {
        return $this->render('team/confirmation.html.twig', [
            'team_name' => $team_name,
        ]);
    }
}
