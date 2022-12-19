<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\Team;
use App\Entity\TeamInterest;
use App\Entity\User;
use App\Event\ApplicationCreatedEvent;
use App\Form\Type\ApplicationType;
use App\Role\Roles;
use App\Service\InterviewCounter;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * AdmissionAdminController is the controller responsible for administrative admission s,
 * such as showing and deleting applications.
 */
class AdmissionAdminController extends BaseController
{
    public function __construct(private readonly InterviewCounter $InterviewCounter, private readonly EventDispatcherInterface $eventDispatcher, private readonly ManagerRegistry $doctrine)
    {
    }

    /**
     * Shows the admission admin page. Shows only applications for the department of the logged in user.
     * This works as the restricted admission management method, only allowing users to manage applications within their department.
     *
     * @return Response
     */
    public function show(Request $request): ?Response
    {
        return $this->showNewApplications($request);
    }

    public function showNewApplications(Request $request): Response
    {
        $semester = $this->getSemesterOrThrow404($request);
        $department = $this->getDepartmentOrThrow404($request);

        $admissionPeriod = $this->doctrine
                ->getRepository(AdmissionPeriod::class)
                ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->doctrine
                ->getRepository(Application::class)
                ->findNewApplicationsByAdmissionPeriod($admissionPeriod);
        }

        return $this->render('admission_admin/new_applications_table.html.twig', [
            'applications' => $applications,
            'semester' => $semester,
            'department' => $department,
            'status' => 'new',
        ]);
    }

    /**
     * @return Response|null
     */
    public function showAssignedApplications(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->doctrine->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applicationRepo = $this->doctrine->getRepository(Application::class);

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

        return $this->render('admission_admin/assigned_applications_table.html.twig', [
            'status' => 'assigned',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
            'interviewDistributions' => $interviewDistributions,
            'cancelledApplications' => $cancelledApplications,
            'yourApplications' => $applicationsAssignedToUser,
        ]);
    }

    /**
     * @return Response|null
     */
    public function showInterviewedApplications(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->doctrine->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->doctrine
                ->getRepository(Application::class)
                ->findInterviewedApplicants($admissionPeriod);
        }

        $counter = $this->InterviewCounter;

        return $this->render('admission_admin/interviewed_applications_table.html.twig', [
            'status' => 'interviewed',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
            'yes' => $counter->count($applications, InterviewCounter::YES),
            'no' => $counter->count($applications, InterviewCounter::NO),
            'maybe' => $counter->count($applications, InterviewCounter::MAYBE),
        ]);
    }

    /**
     * @return Response|null
     */
    public function showExistingApplications(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->doctrine->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $this->getUser()->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }
        $applications = [];
        if ($admissionPeriod !== null) {
            $applications = $this->doctrine
                ->getRepository(Application::class)
                ->findExistingApplicants($admissionPeriod);
        }

        return $this->render('admission_admin/existing_assistants_applications_table.html.twig', [
            'status' => 'existing',
            'applications' => $applications,
            'department' => $department,
            'semester' => $semester,
        ]);
    }

    /**
     * Deletes the given application.
     * This method is intended to be called by an Ajax request.
     *
     * @return JsonResponse
     */
    public function deleteApplicationById(Application $application)
    {
        $em = $this->doctrine->getManager();

        $em->remove($application);
        $em->flush();

        return new JsonResponse([
            'success' => true,
        ]);
    }

    public function deleteApplicationExistingAssistant(Application $application)
    {
        $em = $this->doctrine->getManager();
        $em->remove($application);
        $em->flush();

        $this->addFlash('success', 'Søknaden ble slettet.');

        return $this->redirectToRoute('applications_show_existing', [
            'department' => $application->getDepartment(),
            'semester' => $application->getSemester()->getId(),
        ]);
    }

    /**
     * Deletes the applications submitted as a list of ids through a form POST request.
     * This method is intended to be called by an Ajax request.
     *
     * @return JsonResponse
     */
    public function bulkDeleteApplication(Request $request)
    {
        // Get the ids from the form
        $applicationIds = array_map('intval', $request->request->get('application')['id']);

        $em = $this->doctrine->getManager();

        // Delete the applications
        foreach ($applicationIds as $id) {
            $application = $this->doctrine->getRepository(Application::class)->find($id);

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
        $em = $this->doctrine->getManager();
        $department = $this->getUser()->getDepartment();
        $currentSemester = $this->getCurrentSemester();
        $admissionPeriod = $this->doctrine
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $currentSemester);
        if ($admissionPeriod === null) {
            throw new BadRequestHttpException();
        }

        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application, [
            'departmentId' => $department->getId(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $application->getUser()->getEmail()]);
            if ($user !== null) {
                $application->setUser($user);
            }
            $application->setAdmissionPeriod($admissionPeriod);
            $em->persist($application);
            $em->flush();

            $this->addFlash('admission-notice', 'Søknaden er registrert.');

            $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);

            return $this->redirectToRoute('register_applicant', ['id' => $department->getId()]);
        }

        return $this->render('admission_admin/create_application.html.twig', [
            'department' => $department,
            'semester' => $currentSemester,
            'form' => $form->createView(),
        ]);
    }

    public function showApplication(Application $application)
    {
        if (!$application->getPreviousParticipation()) {
            throw $this->createNotFoundException('Søknaden finnes ikke');
        }

        return $this->render('admission_admin/application.html.twig', [
            'application' => $application,
        ]);
    }

    /**
     * @return Response|null
     */
    public function showTeamInterest(Request $request)
    {
        $user = $this->getUser();
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->doctrine->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        if (!$this->isGranted(Roles::ADMIN) && $user->getDepartment() !== $department) {
            throw $this->createAccessDeniedException();
        }

        $applicationsWithTeamInterest = [];
        $teams = [];
        if ($admissionPeriod !== null) {
            $applicationsWithTeamInterest = $this->doctrine
                ->getRepository(Application::class)
                ->findApplicationByTeamInterestAndAdmissionPeriod($admissionPeriod);
            $teams = $this->doctrine->getRepository(Team::class)->findByTeamInterestAndAdmissionPeriod($admissionPeriod);
        }

        $possibleApplicants = $this->doctrine
            ->getRepository(TeamInterest::class)
            ->findBy(['semester' => $semester, 'department' => $department]);

        return $this->render('admission_admin/teamInterest.html.twig', [
            'applicationsWithTeamInterest' => $applicationsWithTeamInterest,
            'possibleApplicants' => $possibleApplicants,
            'department' => $department,
            'semester' => $semester,
            'teams' => $teams,
        ]);
    }
}
