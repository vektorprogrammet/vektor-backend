<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Team;
use App\Entity\TeamMembership;
use App\Entity\Position;
use App\Event\TeamEvent;
use App\Event\TeamMembershipEvent;
use App\Form\Type\CreateTeamMembershipType;
use App\Form\Type\CreateTeamType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamAdminController extends BaseController
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Department|null $department
     *
     * @return Response
     */
    public function show(Department $department = null)
    {
        if ($department === null) {
            $department = $this->getUser()->getDepartment();
        }

        // Find teams that are connected to the department of the user
        $activeTeams   = $this->getDoctrine()->getRepository(Team::class)->findActiveByDepartment($department);
        $inactiveTeams = $this->getDoctrine()->getRepository(Team::class)->findInactiveByDepartment($department);

        // Return the view with suitable variables
        return $this->render('team_admin/index.html.twig', array(
            'active_teams'   => $activeTeams,
            'inactive_teams' => $inactiveTeams,
            'department'     => $department,
        ));
    }

    public function updateTeamMembership(Request $request, TeamMembership $teamMembership)
    {
        $department = $teamMembership->getTeam()->getDepartment();

        $form = $this->createForm(CreateTeamMembershipType::class, $teamMembership, [
            'department' => $department
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $teamMembership->setIsSuspended(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($teamMembership);
            $em->flush();

            $this->eventDispatcher->dispatch(new TeamMembershipEvent($teamMembership), TeamMembershipEvent::EDITED);

            return $this->redirect($this->generateUrl('teamadmin_show_specific_team', array( 'id' => $teamMembership->getTeam()->getId() )));
        }

        return $this->render('team_admin/create_team_membership.html.twig', array(
            'form' => $form->createView(),
            'team' => $teamMembership->getTeam(),
            'teamMembership' => $teamMembership
        ));
    }

    public function addUserToTeam(Request $request, Team $team)
    {
        // Find the department of the team
        $department = $team->getDepartment();

        // Create a new TeamMembership entity
        $teamMembership = new TeamMembership();
        $teamMembership->setUser($this->getUser());
        $teamMembership->setPosition($this->getDoctrine()->getRepository(Position::class)->findOneBy(array( 'name' => 'Medlem' )));

        // Create a new formType with the needed variables
        $form = $this->createForm(CreateTeamMembershipType::class, $teamMembership, [
            'department' => $department
        ]);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //set the team of the department
            $teamMembership->setTeam($team);

            // Persist the team to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($teamMembership);
            $em->flush();

            $this->eventDispatcher->dispatch(new TeamMembershipEvent($teamMembership), TeamMembershipEvent::CREATED);

            return $this->redirect($this->generateUrl('teamadmin_show_specific_team', array( 'id' => $team->getId() )));
        }

        return $this->render('team_admin/create_team_membership.html.twig', array(
            'form' => $form->createView(),
            'team' => $team
        ));
    }

    public function showSpecificTeam(Team $team)
    {
        // Find all TeamMembership entities based on team
        $activeTeamMemberships   = $this->getDoctrine()->getRepository(TeamMembership::class)->findActiveTeamMembershipsByTeam($team);
        $inActiveTeamMemberships = $this->getDoctrine()->getRepository(TeamMembership::class)->findInactiveTeamMembershipsByTeam($team);
        usort($activeTeamMemberships, array( $this, 'sortTeamMembershipsByEndDate' ));
        usort($inActiveTeamMemberships, array( $this, 'sortTeamMembershipsByEndDate' ));

        $user                      = $this->getUser();
        $currentUserTeamMembership = $this->getDoctrine()->getRepository(TeamMembership::class)->findActiveTeamMembershipsByUser($user);
        $isUserInTeam              = false;
        foreach ($currentUserTeamMembership as $wh) {
            if (in_array($wh, $activeTeamMemberships)) {
                $isUserInTeam = true;
            }
        }

        // Return the view with suitable variables
        return $this->render('team_admin/specific_team.html.twig', array(
            'team'                    => $team,
            'activeTeamMemberships'   => $activeTeamMemberships,
            'inActiveTeamMemberships' => $inActiveTeamMemberships,
            'isUserInTeam'            => $isUserInTeam,
        ));
    }

    /**
     * @param TeamMembership $a
     * @param TeamMembership $b
     *
     * @return bool
     */
    private function sortTeamMembershipsByEndDate($a, $b)
    {
        return $a->getStartSemester()->getStartDate() < $b->getStartSemester()->getStartDate();
    }

    public function updateTeam(Request $request, Team $team)
    {
        // Find the department of the team
        $department   = $team->getDepartment();
        $oldTeamEmail = $team->getEmail();

        // Create the form
        $form = $this->createForm(CreateTeamType::class, $team);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Don't persist if the preview button was clicked
            if (! $form->get('preview')->isClicked()) {
                // Persist the team to the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($team);
                $em->flush();

                $this->eventDispatcher->dispatch(new TeamEvent($team, $oldTeamEmail), TeamEvent::EDITED);

                return $this->redirect($this->generateUrl('teamadmin_show'));
            }
            $teamMemberships = $this->getDoctrine()->getRepository(TeamMembership::class)->findActiveTeamMembershipsByTeam($team);

            // Render the teampage as a preview
            return $this->render('team/team_page.html.twig', array(
                'team'            => $team,
                'teamMemberships' => $teamMemberships,
            ));
        }

        return $this->render('team_admin/create_team.html.twig', array(
            'team'       => $team,
            'department' => $department,
            'form'       => $form->createView(),
            'isUpdate'   => true,
        ));
    }

    public function showTeamsByDepartment(Department $department)
    {
        // Find teams that are connected to the department of the department ID sent in by the request
        $teams = $this->getDoctrine()->getRepository(Team::class)->findByDepartment($department);

        // Return the view with suitable variables
        return $this->render('team_admin/index.html.twig', array(
            'department' => $department,
            'teams'      => $teams,
        ));
    }

    public function createTeamForDepartment(Request $request, Department $department)
    {
        // Create a new Team entity
        $team = new Team();

        // Set the teams department to the department sent in by the request
        // Note: the team object is not valid without a department
        $team->setDepartment($department);

        // Create a new formType with the needed variables
        $form = $this->createForm(CreateTeamType::class, $team);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Don't persist if the preview button was clicked
            if (! $form->get('preview')->isClicked()) {
                // Persist the team to the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($team);
                $em->flush();

                $this->eventDispatcher->dispatch(new TeamEvent($team, $team->getEmail()), TeamEvent::CREATED);

                return $this->redirect($this->generateUrl('teamadmin_show'));
            }

            // Render the teampage as a preview
            return $this->render('team/team_page.html.twig', array(
                'team'            => $team,
                'teamMemberships' => [],
            ));
        }

        return $this->render('team_admin/create_team.html.twig', array(
            'form'       => $form->createView(),
            'department' => $department,
            'team' => $team,
            'isUpdate' => false
        ));
    }

    public function removeUserFromTeamById(TeamMembership $teamMembership)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($teamMembership);
        $em->flush();

        $this->eventDispatcher->dispatch(new TeamMembershipEvent($teamMembership), TeamMembershipEvent::DELETED);

        return $this->redirectToRoute('teamadmin_show_specific_team', [ 'id' => $teamMembership->getTeam()->getId() ]);
    }

    public function deleteTeamById(Team $team)
    {
        $em = $this->getDoctrine()->getManager();

        foreach ($team->getTeamMemberships() as $teamMembership) {
            $teamMembership->setDeletedTeamName($team->getName());
            $em->persist($teamMembership);
        }

        $em->remove($team);
        $em->flush();

        return $this->redirectToRoute("teamadmin_show", [ "id" => $team->getDepartment()->getId() ]);
    }
}
