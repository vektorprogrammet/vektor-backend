<?php

namespace App\Controller;

use App\Entity\Semester;
use App\Event\TeamInterestCreatedEvent;
use App\Entity\Department;
use App\Entity\TeamInterest;
use App\Form\Type\TeamInterestType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TeamInterestController extends BaseController
{

    /**
     * @Route(name="team_interest_form",
     *     path="/teaminteresse/{id}",
     *     requirements={"id"="\d+"},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Department|NULL $department
     * @param Request $request
     *
     * @return RedirectResponse|Response
     * @throws NoResultException
     * @throws NonUniqueResultException
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
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($teamInterest);
            $manager->flush();

            $this->get('event_dispatcher')->dispatch(TeamInterestCreatedEvent::NAME, new TeamInterestCreatedEvent($teamInterest));

            return $this->redirectToRoute('team_interest_form', array(
                'id' => $department->getId(),
            ));
        }

        return $this->render(':team_interest:team_interest.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
