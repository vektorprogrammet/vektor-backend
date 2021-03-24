<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\ExecutiveBoard;
use App\Entity\SupportTicket;
use App\Event\SupportTicketCreatedEvent;
use App\Form\Type\SupportTicketType;
use App\Service\GeoLocation;
use App\Service\LogService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends BaseController
{
    private $geoLocation;

    public function __construct(GeoLocation $geoLocation)
    {
        $this->geoLocation=$geoLocation;

    }

    /**
     * @Route("/kontakt/avdeling/{id}",
     *     name="contact_department",
     *     methods={"GET", "POST"})
     *
     * @Route("/kontakt",
     *     name="contact",
     *     methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Department|null $department
     *
     * @return Response
     */
    public function index(Request $request, Department $department = null)
    {
        if ($department === null) {
            $department = $this->geoLocation
                ->findNearestDepartment($this->getDoctrine()->getRepository(Department::class)->findAll());
        }

        $supportTicket = new SupportTicket();
        $supportTicket->setDepartment($department);
        $form = $this->createForm(SupportTicketType::class, $supportTicket, array(
            'department_repository' => $this->getDoctrine()->getRepository(Department::class),
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $supportTicket->getDepartment() === null) {
            $this->get(LogService::class)->error("Could not send support ticket. Department was null.\n$supportTicket");
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('event_dispatcher')
            ->dispatch(SupportTicketCreatedEvent::NAME, new SupportTicketCreatedEvent($supportTicket));

            return $this->redirectToRoute('contact_department', array('id' => $supportTicket->getDepartment()->getId()));
        }

        $board = $this->getDoctrine()->getRepository(ExecutiveBoard::class)->findBoard();
        $scrollToForm = $form->isSubmitted() && !$form->isValid();

        return $this->render('contact/index.html.twig', array(
            'form' => $form->createView(),
            'specific_department' => $department,
            'board' => $board,
            'scrollToForm' => $scrollToForm
        ));
    }
}
