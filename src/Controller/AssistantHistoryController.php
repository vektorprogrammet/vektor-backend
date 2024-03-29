<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Form\Type\CreateAssistantHistoryType;
use App\Role\Roles;
use App\Service\LogService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssistantHistoryController extends AbstractController
{
    public function __construct(
        private readonly LogService $logService,
        private readonly ManagerRegistry $doctrine
    ) {
    }

    #[Route('/kontrollpanel/deltakerhistorikk/slett/{id}', name: 'assistant_history_delete', methods: ['POST'])]
    public function delete(AssistantHistory $assistantHistory): RedirectResponse
    {
        if (!$this->isGranted(Roles::ADMIN) && $assistantHistory->getUser()->getDepartment() !== $this->getUser()->getDepartment()) {
            $this->createAccessDeniedException();
        }

        $em = $this->doctrine->getManager();
        $em->remove($assistantHistory);
        $em->flush();

        $this->logService->info(
            "{$this->getUser()} deleted {$assistantHistory->getUser()}'s assistant history on " .
            "{$assistantHistory->getSchool()->getName()} {$assistantHistory->getSemester()->getName()}"
        );

        return $this->redirectToRoute('participanthistory_show');
    }

    #[Route('/kontrollpanel/deltakerhistorikk/rediger/{id}',
        name: 'assistant_history_edit',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST'])]
    public function edit(Request $request, AssistantHistory $assistantHistory)
    {
        $em = $this->doctrine->getManager();

        $department = $assistantHistory->getUser()->getDepartment();
        $form = $this->createForm(CreateAssistantHistoryType::class, $assistantHistory, [
            'department' => $department,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($assistantHistory);
            $em->flush();

            return $this->redirectToRoute('participanthistory_show');
        }

        return $this->render('participant_history/participant_history_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
