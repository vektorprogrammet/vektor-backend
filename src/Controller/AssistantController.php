<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\Department;
use App\Entity\Team;
use App\Event\ApplicationCreatedEvent;
use App\Form\Type\ApplicationType;
use App\Service\ApplicationAdmission;
use App\Service\FilterService;
use App\Service\GeoLocation;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class AssistantController extends BaseController
{
    public function __construct(private readonly ApplicationAdmission $applicationAdmission, private readonly GeoLocation $geoLocation, private readonly FilterService $filterService, private readonly KernelInterface $kernel, private readonly EventDispatcherInterface $eventDispatcher, private readonly ManagerRegistry $doctrine)
    {
    }

    /**
     * @deprecated This resource is only here to serve old urls (e.g. in old emails)
     *
     * @Route("/opptak/{shortName}",
     *     requirements={"shortName"="(NTNU|NMBU|UiB|UIB|UiO|UIO)"})
     * @Route("/avdeling/{shortName}",
     *     requirements={"shortName"="(NTNU|NMBU|UiB|UIB|UiO|UIO)"})
     * @Route("/opptak/avdeling/{id}",
     *     requirements={"id"="\d+"},
     *     methods={"GET", "POST"}
     *     )
     */
    public function admissionByShortName(Request $request, Department $department): Response
    {
        return $this->index($request, $department);
    }

    public function admissionCaseInsensitive(Request $request, $city): Response
    {
        $city = str_replace(['æ', 'ø', 'å'], ['Æ', 'Ø', 'Å'], (string) $city); // Make sqlite happy
        $department = $this->doctrine
                ->getRepository(Department::class)
                ->findOneByCityCaseInsensitive($city);
        if ($department !== null) {
            return $this->index($request, $department);
        }
        throw $this->createNotFoundException("Fant ingen avdeling $city.");
    }

    public function admission(Request $request, Department $department = null): Response
    {
        return $this->index($request, $department);
    }

    public function index(
        Request $request,
        Department $specificDepartment = null,
        bool $scrollToAdmissionForm = false
    ): Response {
        $admissionManager = $this->applicationAdmission;
        $em = $this->doctrine->getManager();

        $departments = $em->getRepository(Department::class)->findActive();
        $departments = $this->geoLocation->sortDepartmentsByDistanceFromClient($departments);
        $departmentsWithActiveAdmission = $this->filterService->filterDepartmentsByActiveAdmission($departments, true);

        $departmentInUrl = $specificDepartment !== null;
        if (!$departmentInUrl) {
            $specificDepartment = $departments[0];
        }

        $teams = $em->getRepository(Team::class)->findByOpenApplicationAndDepartment($specificDepartment);

        $application = new Application();

        $formViews = [];

        foreach ($departments as $department) {
            $form = $this->get('form.factory')->createNamedBuilder('application_' . $department->getId(), ApplicationType::class, $application, [
                'validation_groups' => ['admission'],
                'departmentId' => $department->getId(),
                'environment' => $this->kernel->getEnvironment(),
            ])->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $scrollToAdmissionForm = true;
                $specificDepartment = $department;
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $admissionManager->setCorrectUser($application);

                if ($application->getUser()->hasBeenAssistant()) {
                    return $this->redirectToRoute('admission_existing_user');
                }

                $admissionPeriod = $em->getRepository(AdmissionPeriod::class)->findOneWithActiveAdmissionByDepartment($department);

                // If no active admission period is found
                if (!$admissionPeriod) {
                    $this->addFlash('danger', $department . ' sitt opptak er dessverre stengt.');

                    return $this->redirectToRoute('assistants');
                }
                $application->setAdmissionPeriod($admissionPeriod);
                $em->persist($application);
                $em->flush();

                $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);

                return $this->redirectToRoute('application_confirmation');
            }

            $formViews[$department->getCity()] = $form->createView();
        }

        return $this->render('assistant/assistants.html.twig', [
            'specific_department' => $specificDepartment,
            'department_in_url' => $departmentInUrl,
            'departments' => $departments,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'teams' => $teams,
            'forms' => $formViews,
            'scroll_to_admission_form' => $scrollToAdmissionForm,
        ]);
    }

    public function confirmation(): Response
    {
        return $this->render('admission/application_confirmation.html.twig');
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function subscribePage(Request $request, Department $department): Response
    {
        if (!$department->activeAdmission()) {
            return $this->index($request, $department);
        }
        $admissionManager = $this->get(ApplicationAdmission::class);
        $em = $this->doctrine->getManager();
        $application = new Application();

        $form = $this->get('form.factory')->createNamedBuilder('application_' . $department->getId(), ApplicationType::class, $application, [
            'validation_groups' => ['admission'],
            'departmentId' => $department->getId(),
            'environment' => $this->kernel->getEnvironment(),
        ])->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $admissionManager->setCorrectUser($application);

            if ($application->getUser()->hasBeenAssistant()) {
                $this->addFlash('warning', $application->getUser()->getEmail() . ' har vært assistent før. Logg inn med brukeren din for å søke igjen.');

                return $this->redirectToRoute('application_stand_form', ['shortName' => $department->getShortName()]);
            }

            $admissionPeriod = $em->getRepository(AdmissionPeriod::class)->findOneWithActiveAdmissionByDepartment($department);
            $application->setAdmissionPeriod($admissionPeriod);
            $em->persist($application);
            $em->flush();

            $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);

            $this->addFlash('success', $application->getUser()->getEmail() . ' har blitt registrert. Du vil få en e-post med kvittering på søknaden.');

            return $this->redirectToRoute('application_stand_form', ['shortName' => $department->getShortName()]);
        }

        return $this->render('admission/application_page.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }
}
