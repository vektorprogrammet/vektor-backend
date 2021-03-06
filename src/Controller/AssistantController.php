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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class AssistantController extends BaseController
{
    private $applicationAdmission;
    private $geoLocation;
    private $filterService;
    private $kernel;
    private $eventDispatcher;

    public function __construct(ApplicationAdmission $applicationAdmission,
                                GeoLocation $geoLocation,
                                FilterService $filterService,
                                KernelInterface $kernel,
                                EventDispatcherInterface $eventDispatcher)
    {
        $this->applicationAdmission = $applicationAdmission;
        $this->geoLocation = $geoLocation;
        $this->filterService = $filterService;
        $this->kernel = $kernel;
        $this->eventDispatcher = $eventDispatcher;
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
     *
     * @param Request $request
     * @param Department $department
     *
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function admissionByShortName(Request $request, Department $department)
    {
        return $this->index($request, $department);
    }
    
    /**
     * @Route("/opptak/{city}", name="admission_show_by_city_case_insensitive")
     * @Route("/avdeling/{city}", name="admission_show_specific_department_by_city_case_insensitive")
     *
     * @param Request $request
     * @param $city
     *
     * @return Response
     */
    public function admissionCaseInsensitive(Request $request, $city)
    {
        $city = str_replace(array('??', '??','??'), array('??','??','??'), $city); // Make sqlite happy
        $department = $this->getDoctrine()
                ->getRepository(Department::class)
                ->findOneByCityCaseInsensitive($city);
        if ($department !== null) {
            return $this->index($request, $department);
        } else {
            throw $this->createNotFoundException("Fant ingen avdeling $city.");
        }
    }

    /**
     * @Route("/opptak", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Department|null $department
     *
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function admission(Request $request, Department $department = null)
    {
        return $this->index($request, $department);
    }

    /**
     * @param Request $request
     * @param Department|null $specificDepartment
     * @param bool $scrollToAdmissionForm
     *
     * @return Response
     */
    public function index(Request $request, Department $specificDepartment = null, $scrollToAdmissionForm = false)
    {
        $admissionManager = $this->applicationAdmission;
        $em = $this->getDoctrine()->getManager();

        $departments = $em->getRepository(Department::class)->findActive();
        $departments = $this->geoLocation->sortDepartmentsByDistanceFromClient($departments);
        $departmentsWithActiveAdmission = $this->filterService->filterDepartmentsByActiveAdmission($departments, true);

        $departmentInUrl = $specificDepartment !== null;
        if (!$departmentInUrl) {
            $specificDepartment = $departments[0];
        }

        $teams = $em->getRepository(Team::class)->findByOpenApplicationAndDepartment($specificDepartment);

        $application = new Application();

        $formViews = array();

        /** @var Department $department */
        foreach ($departments as $department) {
            $form = $this->get('form.factory')->createNamedBuilder('application_'.$department->getId(), ApplicationType::class, $application, array(
                'validation_groups' => array('admission'),
                'departmentId' => $department->getId(),
                'environment' => $this->kernel->getEnvironment(),
            ))->getForm();

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

                //If no active admission period is found
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

        return $this->render('assistant/assistants.html.twig', array(
            'specific_department' => $specificDepartment,
            'department_in_url' => $departmentInUrl,
            'departments' => $departments,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'teams' => $teams,
            'forms' => $formViews,
            'scroll_to_admission_form' => $scrollToAdmissionForm,
        ));
    }

    /**
     * @Route("/assistenter/opptak/bekreftelse", name="application_confirmation")
     * @return Response
     */
    public function confirmation()
    {
        return $this->render('admission/application_confirmation.html.twig');
    }

    /**
     * @Route("/stand/opptak/{shortName}",
     *     name="application_stand_form",
     *     requirements={"shortName"="\w+"})
     *
     * @param Request $request
     * @param Department $department
     *
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function subscribePage(Request $request, Department $department)
    {
        if (!$department->activeAdmission()) {
            return $this->index($request, $department);
        }
        $admissionManager = $this->get(ApplicationAdmission::class);
        $em = $this->getDoctrine()->getManager();
        $application = new Application();

        $form = $this->get('form.factory')->createNamedBuilder('application_'.$department->getId(), ApplicationType::class, $application, array(
            'validation_groups' => array('admission'),
            'departmentId' => $department->getId(),
            'environment' => $this->kernel->getEnvironment(),
        ))->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $admissionManager->setCorrectUser($application);

            if ($application->getUser()->hasBeenAssistant()) {
                $this->addFlash('warning', $application->getUser()->getEmail().' har v??rt assistent f??r. Logg inn med brukeren din for ?? s??ke igjen.');
                return $this->redirectToRoute('application_stand_form', ['shortName' => $department->getShortName()]);
            }

            $admissionPeriod = $em->getRepository(AdmissionPeriod::class)->findOneWithActiveAdmissionByDepartment($department);
            $application->setAdmissionPeriod($admissionPeriod);
            $em->persist($application);
            $em->flush();

            $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);

            $this->addFlash('success', $application->getUser()->getEmail().' har blitt registrert. Du vil f?? en e-post med kvittering p?? s??knaden.');
            return $this->redirectToRoute('application_stand_form', ['shortName' => $department->getShortName()]);
        }

        return $this->render('admission/application_page.html.twig', [
            'department' => $department,
            'form' => $form->createView()
        ]);
    }
}
