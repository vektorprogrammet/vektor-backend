<?php

namespace App\Controller;

use App\Entity\Semester;
use App\Entity\Team;
use App\Event\ApplicationCreatedEvent;
use App\Form\Type\ApplicationExistingUserType;
use App\Service\ApplicationAdmission;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExistingUserAdmissionController extends BaseController
{
    private EventDispatcherInterface $eventDispatcher;
    private ApplicationAdmission $applicationAdmission;
    private ManagerRegistry $doctrine;

    public function __construct(EventDispatcherInterface $eventDispatcher, 
                                ApplicationAdmission $applicationAdmission, 
                                ManagerRegistry $doctrine)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->applicationAdmission = $applicationAdmission;
        $this->doctrine = $doctrine;
    }

    /**
     * @param Request $request
     *
     * @return null|RedirectResponse|Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function show(Request $request)
    {
        $user = $this->getUser();
        $em = $this->doctrine->getManager();
        $admissionManager = $this->applicationAdmission;
        if ($res = $admissionManager->renderErrorPage($user)) {
            return $res;
        }

        $department = $user->getDepartment();
        $teams = $em->getRepository(Team::class)->findActiveByDepartment($department);

        $application = $admissionManager->createApplicationForExistingAssistant($user);

        $form = $this->createForm(ApplicationExistingUserType::class, $application, array(
            'validation_groups' => array('admission_existing'),
            'teams' => $teams,
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($application);
            $em->flush();

            $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);
            $this->addFlash("success", "Søknad mottatt!");

            return $this->redirectToRoute('my_page');
        }

        $semester = $this->getCurrentSemester();

        return $this->render(':admission:existingUser.html.twig', array(
            'form' => $form->createView(),
            'department' => $user->getDepartment(),
            'semester' => $semester,
            'user' => $user,
        ));
    }
}
