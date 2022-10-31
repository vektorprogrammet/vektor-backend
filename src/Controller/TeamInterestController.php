<?php

namespace App\Controller;

use App\Entity\Semester;
use App\Event\TeamInterestCreatedEvent;
use App\Entity\Department;
use App\Entity\TeamInterest;
use App\Form\Type\TeamInterestType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TeamInterestController extends BaseController
{
    private EventDispatcherInterface $eventDispatcher;
    private ManagerRegistry $doctrine;

    public function __construct(EventDispatcherInterface $eventDispatcher,
                                ManagerRegistry $doctrine)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->doctrine = $doctrine;
    }

    /**
     * @param Department|NULL $department
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function showTeamInterestForm(Department $department, Request $request)
    {
        $semester = $this->getCurrentSemester();
        if ($semester === null) {
            throw new BadRequestHttpException('No current semester');
        }

        $teamInterest = new TeamInterest();
        $teamInterest->setSemester($semester);
        $teamInterest->setDepartment($department);
        $form = $this->createForm(TeamInterestType::class, $teamInterest, array(
            'department' => $department,
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->doctrine->getManager();
            $manager->persist($teamInterest);
            $manager->flush();

            $this->eventDispatcher->dispatch(new TeamInterestCreatedEvent($teamInterest), TeamInterestCreatedEvent::NAME);

            return $this->redirectToRoute('team_interest_form', array(
                'id' => $department->getId(),
            ));
        }

        return $this->render('team_interest/team_interest.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
