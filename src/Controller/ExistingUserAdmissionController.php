<?php

namespace App\Controller;

use App\Entity\Team;
use App\Event\ApplicationCreatedEvent;
use App\Form\Type\ApplicationExistingUserType;
use App\Service\ApplicationAdmission;
use App\Service\DepartmentSemesterService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExistingUserAdmissionController extends AbstractController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ApplicationAdmission $applicationAdmission,
        private readonly ManagerRegistry $doctrine,
        private readonly DepartmentSemesterService $departmentSemesterService,
    ) {
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     *
     * @return RedirectResponse|Response|null
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
        $teams = $em
            ->getRepository(Team::class)
            ->findActiveByDepartment($department);

        $application = $admissionManager->createApplicationForExistingAssistant($user);

        $form = $this->createForm(ApplicationExistingUserType::class, $application, [
            'validation_groups' => ['admission_existing'],
            'teams' => $teams,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($application);
            $em->flush();

            $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);
            $this->addFlash('success', 'Søknad mottatt!');

            return $this->redirectToRoute('my_page');
        }

        $semester = $this->departmentSemesterService->getCurrentSemester();

        return $this->render('admission/existingUser.html.twig', [
            'form' => $form->createView(),
            'department' => $user->getDepartment(),
            'semester' => $semester,
            'user' => $user,
        ]);
    }
}
