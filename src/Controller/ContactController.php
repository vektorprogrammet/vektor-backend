<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\ExecutiveBoard;
use App\Entity\SupportTicket;
use App\Event\SupportTicketCreatedEvent;
use App\Form\Type\SupportTicketType;
use App\Service\GeoLocation;
use App\Service\LogService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    public function __construct(
        private readonly GeoLocation $geoLocation,
        private readonly LogService $logService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ManagerRegistry $doctrine
    ) {
    }

    #[Route('/kontakt', name: 'contact', methods: ['GET', 'POST'])]
    #[Route('/kontakt/avdeling/{id}',
        name: 'contact_department',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST'])]
    public function index(Request $request, Department $department = null): Response
    {
        if ($department === null) {
            $department = $this->geoLocation
                ->findNearestDepartment($this->doctrine
                    ->getRepository(Department::class)
                    ->findAll());
        }

        $supportTicket = new SupportTicket();
        $supportTicket->setDepartment($department);
        $form = $this->createForm(SupportTicketType::class, $supportTicket, [
            'department_repository' => $this->doctrine->getRepository(Department::class),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $supportTicket->getDepartment() === null) {
            $this->logService->error("Could not send support ticket. Department was null.\n$supportTicket");
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventDispatcher
            ->dispatch(new SupportTicketCreatedEvent($supportTicket), SupportTicketCreatedEvent::NAME);

            return $this->redirectToRoute('contact_department', ['id' => $supportTicket->getDepartment()->getId()]);
        }

        $board = $this->doctrine
            ->getRepository(ExecutiveBoard::class)
            ->findBoard();

        $scrollToForm = $form->isSubmitted() && !$form->isValid();

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'specific_department' => $department,
            'board' => $board,
            'scrollToForm' => $scrollToForm,
        ]);
    }
}
