<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\TeamApplication;
use App\Entity\TeamMembership;
use App\Event\TeamApplicationCreatedEvent;
use App\Form\Type\TeamApplicationType;
use App\Role\Roles;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamApplicationController extends BaseController
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function showApplication(TeamApplication $application)
    {
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->getDoctrine()->getRepository(TeamMembership::class)->findActiveTeamMembershipsByTeamAndUser($application->getTeam(), $user);
        if (empty($activeUserHistoriesInTeam) && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw new AccessDeniedException();
        }

        return $this->render('team_admin/show_application.html.twig', array(
            'application' => $application,
        ));
    }

    public function showAllApplications(Team $team)
    {
        $applications = $this->getDoctrine()->getRepository(TeamApplication::class)->findByTeam($team);
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->getDoctrine()->getRepository(TeamMembership::class)->findActiveTeamMembershipsByTeamAndUser($team, $user);
        if (empty($activeUserHistoriesInTeam) && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw new AccessDeniedException();
        }

        return $this->render('team_admin/show_applications.html.twig', array(
            'applications' => $applications,
            'team' => $team,
        ));
    }

    public function deleteTeamApplicationById(TeamApplication $teamApplication)
    {
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($teamApplication);
        $manager->flush();

        return $this->redirectToRoute('team_application_show_all', array('id' => $teamApplication->getTeam()->getId()));
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

            return $this->redirectToRoute('team_application_confirmation', array(
                'team_name' => $team->getName(),
            ));
        }

        return $this->render('team/team_application.html.twig', array(
            'team' => $team,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/team/application/bekreftelse/{team_name}", name="team_application_confirmation")
     * @return Response
     */
    public function confirmation($team_name)
    {
        return $this->render('team/confirmation.html.twig', array(
            'team_name' => $team_name,
        ));
    }
}
