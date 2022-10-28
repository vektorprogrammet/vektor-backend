<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\Semester;
use App\Entity\Team;
use App\Entity\TeamInterest;
use App\Entity\User;
use App\Event\ApplicationCreatedEvent;
use App\Form\Type\ApplicationType;
use App\Role\Roles;
use App\Service\InterviewCounter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * AdmissionAdminController is the controller responsible for administrative admission s,
 * such as showing and deleting applications.
 */
class AdmissionAdminController extends BaseController
{
    private InterviewCounter $InterviewCounter;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(InterviewCounter $interviewCounter, EventDispatcherInterface $eventDispatcher)
    {
        $this->InterviewCounter = $interviewCounter;
        $this->eventDispatcher = $eventDispatcher;
    }
    /**
     * Shows the admission admin page. Shows only applications for the department of the logged in user.
     * This works as the restricted admission management method, only allowing users to manage applications within their department.
     * @param Request $request
     *
     * @return Response
     */
    public function show(Request $request): ?Response
    {
        return $this->showNewApplications($request);
    }


    /**
     * @param Request $request
     * @return Response
     */
    public function showNewApplications(Request $request): Response
    {
        $semester = $this->getSemesterOrThrow404($request);
        $department = $this->getDepartmentOrThrow404($request);

        $admissionPeriod = $this->getDoctrine()
                ->getRepository(AdmissionPeriod::class)
                ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->getDoctrine()
                ->getRepository(Application::class)
                ->findNewApplicationsByAdmissionPeriod($admissionPeriod);
        }

        return $this->render('admission_admin/new_applications_table.html.twig', array(
            'applications' => $applications,
            'semester' => $semester,
            'department' => $department,
            'status' => 'new',
        ));
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    public function showAssignedApplications(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->getDoctrine()->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applicationRepo = $this->getDoctrine()->getRepository(Application::class);

        $applications = [];
        $interviewDistributions = [];
        $cancelledApplications = [];
        $applicationsAssignedToUser = [];

        if ($admissionPeriod !== null) {
            $applications = $applicationRepo->findAssignedApplicants($admissionPeriod);
            $interviewDistributions = $this->InterviewCounter
                ->createInterviewDistributions($applications, $admissionPeriod);
            $cancelledApplications = $applicationRepo->findCancelledApplicants($admissionPeriod);
            $applicationsAssignedToUser = $applicationRepo->findAssignedByUserAndAdmissionPeriod($this->getUser(), $admissionPeriod);
        }

        return $this->render('admission_admin/assigned_applications_table.html.twig', array(
            'status' => 'assigned',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
            'interviewDistributions' => $interviewDistributions,
            'cancelledApplications' => $cancelledApplications,
            'yourApplications' => $applicationsAssignedToUser,
        ));
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    public function showInterviewedApplications(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->getDoctrine()->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->getDoctrine()
                ->getRepository(Application::class)
                ->findInterviewedApplicants($admissionPeriod);
        }

        $counter = $this->InterviewCounter;

        return $this->render('admission_admin/interviewed_applications_table.html.twig', array(
            'status' => 'interviewed',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
            'yes' => $counter->count($applications, InterviewCounter::YES),
            'no' => $counter->count($applications, InterviewCounter::NO),
            'maybe' => $counter->count($applications, InterviewCounter::MAYBE),
        ));
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    public function showExistingApplications(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->getDoctrine()->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }
        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->getDoctrine()
                ->getRepository(Application::class)
                ->findExistingApplicants($admissionPeriod);
        }

        return $this->render('admission_admin/existing_assistants_applications_table.html.twig', array(
            'status' => 'existing',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
        ));
    }

    /**
     * Deletes the given application.
     * This method is intended to be called by an Ajax request.
     *
     * @param Application $application
     *
     * @return JsonResponse
     */
    public function deleteApplicationById(Application $application)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($application);
        $em->flush();

        return new JsonResponse([
            'success' => true,
        ]);
    }

    public function deleteApplicationExistingAssistant(Application $application)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($application);
        $em->flush();

        $this->addFlash('success', 'Søknaden ble slettet.');

        return $this->redirectToRoute('applications_show_existing', array(
            'department' => $application->getDepartment(),
            'semester' => $application->getSemester()->getId()
        ));
    }

    /**
     * Deletes the applications submitted as a list of ids through a form POST request.
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function bulkDeleteApplication(Request $request)
    {
        // Get the ids from the form
        $applicationIds = array_map('intval', $request->request->get('application')['id']);


        $em = $this->getDoctrine()->getManager();

        // Delete the applications
        foreach ($applicationIds as $id) {
            $application = $this->getDoctrine()->getRepository(Application::class)->find($id);

            if ($application !== null) {
                $em->remove($application);
            }
        }

        $em->flush();

        $this->addFlash('success', 'Søknadene ble slettet.');

        return new JsonResponse([
            'success' => true,
        ]);
    }

    public function createApplication(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $department = $this->getUser()->getDepartment();
        $currentSemester = $this->getCurrentSemester();
        $admissionPeriod = $this->getDoctrine()
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $currentSemester);
        if ($admissionPeriod === null) {
            throw new BadRequestHttpException();
        }

        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application, array(
            'departmentId' => $department->getId(),
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $em->getRepository(User::class)->findOneBy(array('email' => $application->getUser()->getEmail()));
            if ($user !== null) {
                $application->setUser($user);
            }
            $application->setAdmissionPeriod($admissionPeriod);
            $em->persist($application);
            $em->flush();

            $this->addFlash('admission-notice', 'Søknaden er registrert.');

            $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);

            return $this->redirectToRoute('register_applicant', array('id' => $department->getId()));
        }

        return $this->render('admission_admin/create_application.html.twig', array(
            'department' => $department,
            'semester' => $currentSemester,
            'form' => $form->createView(),
        ));
    }

    public function showApplication(Application $application)
    {
        if (!$application->getPreviousParticipation()) {
            throw $this->createNotFoundException('Søknaden finnes ikke');
        }

        return $this->render('admission_admin/application.html.twig', array(
            'application' => $application,
        ));
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    public function showTeamInterest(Request $request)
    {
        $user = $this->getUser();
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->getDoctrine()->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::ADMIN) && $user->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applicationsWithTeamInterest = [];
        $teams = [];
        if ($admissionPeriod !== null) {
            $applicationsWithTeamInterest = $this->getDoctrine()
                ->getRepository(Application::class)
                ->findApplicationByTeamInterestAndAdmissionPeriod($admissionPeriod);
            $teams = $this->getDoctrine()->getRepository(Team::class)->findByTeamInterestAndAdmissionPeriod($admissionPeriod);
        }

        $possibleApplicants = $this->getDoctrine()
            ->getRepository(TeamInterest::class)
            ->findBy(array('semester' => $semester, 'department' => $department));

        return $this->render('admission_admin/teamInterest.html.twig', array(
            'applicationsWithTeamInterest' => $applicationsWithTeamInterest,
            'possibleApplicants' => $possibleApplicants,
            'department' => $department,
            'semester' => $semester,
            'teams' => $teams,
        ));
    }
}
