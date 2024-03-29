<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\ExecutiveBoard;
use App\Entity\ExecutiveBoardMembership;
use App\Form\Type\CreateExecutiveBoardMembershipType;
use App\Form\Type\CreateExecutiveBoardType;
use App\Service\RoleManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExecutiveBoardController extends AbstractController
{
    public function __construct(
        private readonly RoleManager $roleManager,
        private readonly ManagerRegistry $doctrine
    ) {
    }

    public function show(): Response
    {
        $board = $this->doctrine->getRepository(ExecutiveBoard::class)->findBoard();

        return $this->render('team/team_page.html.twig', [
            'team' => $board,
        ]);
    }

    public function showAdmin(): Response
    {
        $board = $this->doctrine
            ->getRepository(ExecutiveBoard::class)
            ->findBoard();

        $members = $this->doctrine
            ->getRepository(ExecutiveBoardMembership::class)
            ->findAll();

        $activeMembers = [];
        $inactiveMembers = [];
        foreach ($members as $member) {
            if ($member->isActive()) {
                $activeMembers[] = $member;
            } else {
                $inactiveMembers[] = $member;
            }
        }

        return $this->render('executive_board/index.html.twig', [
            'board_name' => $board->getName(),
            'active_members' => $activeMembers,
            'inactive_members' => $inactiveMembers,
        ]);
    }

    public function addUserToBoard(Request $request, Department $department)
    {
        $board = $this->doctrine
            ->getRepository(ExecutiveBoard::class)
            ->findBoard();

        // Create a new TeamMembership entity
        $member = new ExecutiveBoardMembership();
        $member->setUser($this->getUser());

        // Create a new formType with the needed variables
        $form = $this->createForm(CreateExecutiveBoardMembershipType::class, $member, [
            'departmentId' => $department,
        ]);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $member->setBoard($board);

            // Persist the board to the database
            $em = $this->doctrine->getManager();
            $em->persist($member);
            $em->flush();

            $this->roleManager->updateUserRole($member->getUser());

            return $this->redirect($this->generateUrl('executive_board_show'));
        }

        $city = $department->getCity();

        return $this->render('executive_board/member.html.twig', [
            'heading' => "Legg til hovedstyremedlem fra avdeling $city",
            'form' => $form->createView(),
        ]);
    }

    public function removeUserFromBoardById(ExecutiveBoardMembership $member): RedirectResponse
    {
        $em = $this->doctrine->getManager();
        $em->remove($member);
        $em->flush();

        $this->roleManager->updateUserRole($member->getUser());

        return $this->redirect($this->generateUrl('executive_board_show'));
    }

    public function updateBoard(Request $request): RedirectResponse|Response
    {
        $board = $this->doctrine
            ->getRepository(ExecutiveBoard::class)
            ->findBoard();

        // Create the form
        $form = $this->createForm(CreateExecutiveBoardType::class, $board);

        // Handle the form
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Don't persist if the preview button was clicked
            if (!$form->get('preview')->isClicked()) {
                // Persist the board to the database
                $em = $this->doctrine->getManager();
                $em->persist($board);
                $em->flush();

                return $this->redirect($this->generateUrl('executive_board_show'));
            }

            // Render the boardpage as a preview
            return $this->render('team/team_page.html.twig', [
                'team' => $board,
                'teamMemberships' => $board->getBoardMemberships(),
            ]);
        }

        return $this->render('executive_board/update_executive_board.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editMemberHistory(Request $request, ExecutiveBoardMembership $member): Response
    {
        $user = $member->getUser(); // Store the $user object before the form touches our $member object with spooky user data
        $form = $this->createForm(CreateExecutiveBoardMembershipType::class, $member, [
            'departmentId' => $user->getDepartment(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->persist($member);
            $em->flush();

            return $this->redirectToRoute('executive_board_show');
        }

        $memberName = $user->getFullName();

        return $this->render('executive_board/member.html.twig', [
            'heading' => "Rediger medlemshistorikken til $memberName",
            'form' => $form->createView(),
        ]);
    }
}
