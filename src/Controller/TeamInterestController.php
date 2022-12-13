<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\TeamInterest;
use App\Event\TeamInterestCreatedEvent;
use App\Form\Type\TeamInterestType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TeamInterestController extends BaseController
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Department|null $department
     *
     * @return RedirectResponse|Response
     */
    public function showTeamInterestForm(Department $department, Request $request)
    {
        $semester = $this->getCurrentSemester();
        if (null === $semester) {
            throw new BadRequestHttpException('No current semester');
        }

        $teamInterest = new TeamInterest();
        $teamInterest->setSemester($semester);
        $teamInterest->setDepartment($department);
        $form = $this->createForm(TeamInterestType::class, $teamInterest, [
            'department' => $department,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($teamInterest);
            $manager->flush();

            $this->eventDispatcher->dispatch(new TeamInterestCreatedEvent($teamInterest), TeamInterestCreatedEvent::NAME);

            return $this->redirectToRoute('team_interest_form', [
                'id' => $department->getId(),
            ]);
        }

        return $this->render('team_interest/team_interest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
