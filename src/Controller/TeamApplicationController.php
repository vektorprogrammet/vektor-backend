<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\TeamApplication;
use App\Entity\TeamMembership;
use App\Event\TeamApplicationCreatedEvent;
use App\Form\Type\TeamApplicationType;
use App\Role\Roles;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TeamApplicationController extends AbstractController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ManagerRegistry $doctrine
    ) {
    }

    public function showApplication(TeamApplication $application): Response
    {
        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->doctrine
            ->getRepository(TeamMembership::class)
            ->findActiveTeamMembershipsByTeamAndUser($application->getTeam(), $user);

        if (empty($activeUserHistoriesInTeam) && !$this->isGranted(Roles::TEAM_LEADER)) {
            throw new AccessDeniedException();
        }

        return $this->render('team_admin/show_application.html.twig', [
            'application' => $application,
        ]);
    }

    public function showAllApplications(Team $team): Response
    {
        $applications = $this->doctrine
            ->getRepository(TeamApplication::class)
            ->findByTeam($team);

        $user = $this->getUser();
        $activeUserHistoriesInTeam = $this->doctrine
            ->getRepository(TeamMembership::class)
            ->findActiveTeamMembershipsByTeamAndUser($team, $user);

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
        $manager = $this->doctrine->getManager();

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

            $manager = $this->doctrine->getManager();
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

    public function confirmation($team_name): Response
    {
        return $this->render('team/confirmation.html.twig', [
            'team_name' => $team_name,
        ]);
    }
}
