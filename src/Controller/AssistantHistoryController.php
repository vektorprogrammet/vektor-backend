<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Role\Roles;
use App\Form\Type\CreateAssistantHistoryType;
use App\Service\LogService;
use Symfony\Component\HttpFoundation\Request;

class AssistantHistoryController extends BaseController
{
    private LogService $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function delete(AssistantHistory $assistantHistory)
    {
        if (!$this->isGranted(Roles::ADMIN) && $assistantHistory->getUser()->getDepartment() !== $this->getUser()->getDepartment()) {
            $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($assistantHistory);
        $em->flush();

        $this->logService->info(
            "{$this->getUser()} deleted {$assistantHistory->getUser()}'s assistant history on ".
            "{$assistantHistory->getSchool()->getName()} {$assistantHistory->getSemester()->getName()}"
        );

        return $this->redirectToRoute('participanthistory_show');
    }

    public function edit(Request $request, AssistantHistory $assistantHistory)
    {
        $em = $this->getDoctrine()->getManager();

        $department = $assistantHistory->getUser()->getDepartment();
        $form = $this->createForm(CreateAssistantHistoryType::class, $assistantHistory, [
            'department' => $department
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form -> isValid()) {
            $em->persist($assistantHistory);
            $em->flush();
            return $this->redirectToRoute('participanthistory_show');
        }
        return $this->render("participant_history/participant_history_edit.html.twig", array(
            "form"=>$form->createView()
        ));
    }
}
