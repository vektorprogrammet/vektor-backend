<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\GenerateMailingListType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MailingListController extends BaseController
{
    public function show(Request $request): Response
    {
        $form = $this->createForm(GenerateMailingListType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $type = $data['type'];
            $semesterID = $data['semester']->getId();
            $departmentID = $data['department']->getId();

            switch ($type) {
                case 'Assistent':
                    return $this->redirectToRoute('generate_assistant_mail_list', [
                        'department' => $departmentID,
                        'semester' => $semesterID,
                    ]);
                case 'Team':
                    return $this->redirectToRoute('generate_team_mail_list', [
                        'department' => $departmentID,
                        'semester' => $semesterID,
                    ]);
                case 'Alle':
                    return $this->redirectToRoute('generate_all_mail_list', [
                        'department' => $departmentID,
                        'semester' => $semesterID,
                    ]);
                default:
                    throw new BadRequestHttpException('type can only be "Assistent", "Team" or "Alle". Was: ' . $type);
            }
        }

        return $this->render('mailing_list/generate_mail_list.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function showAssistants(Request $request): Response
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $users = $this->getDoctrine()->getRepository(User::class)
            ->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester);

        return $this->render('mailing_list/mailinglist_show.html.twig', [
            'users' => $users,
        ]);
    }

    public function showTeam(Request $request): Response
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $users = $this->getDoctrine()->getRepository(User::class)
            ->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester);

        return $this->render('mailing_list/mailinglist_show.html.twig', [
            'users' => $users,
        ]);
    }

    public function showAll(Request $request): Response
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $assistantUsers = $this->getDoctrine()->getRepository(User::class)
            ->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester);
        $teamUsers = $this->getDoctrine()->getRepository(User::class)
            ->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester);
        $users = array_unique(array_merge($assistantUsers, $teamUsers));

        return $this->render('mailing_list/mailinglist_show.html.twig', [
            'users' => $users,
        ]);
    }
}
