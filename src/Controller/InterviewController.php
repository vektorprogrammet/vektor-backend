<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Interview;
use App\Entity\InterviewSchema;
use App\Entity\Team;
use App\Entity\User;
use App\Event\InterviewConductedEvent;
use App\Event\InterviewEvent;
use App\Form\Type\AddCoInterviewerType;
use App\Form\Type\ApplicationInterviewType;
use App\Form\Type\CancelInterviewConfirmationType;
use App\Form\Type\CreateInterviewType;
use App\Form\Type\InterviewNewTimeType;
use App\Form\Type\ScheduleInterviewType;
use App\Role\ReversedRoleHierarchy;
use App\Role\Roles;
use App\Service\ApplicationManager;
use App\Service\InterviewManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * InterviewController is the controller responsible for interview s,
 * such as showing, assigning and conducting interviews.
 */
class InterviewController extends BaseController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly InterviewManager $interviewManager,
        private readonly ReversedRoleHierarchy $reversedRoleHierarchy,
        private readonly ApplicationManager $applicationManager,
        private readonly ManagerRegistry $doctrine
    ) {
    }

    public function conduct(Request $request, Application $application): RedirectResponse|Response
    {
        if ($application->getInterview() === null) {
            throw $this->createNotFoundException();
        }
        $department = $this->getUser()->getDepartment();
        $teams = $this->doctrine
            ->getRepository(Team::class)
            ->findActiveByDepartment($department);

        if ($this->getUser() === $application->getUser()) {
            return $this->render('error/control_panel_error.html.twig', ['error' => 'Du kan ikke intervjue deg selv']);
        }

        // If the interview has not yet been conducted, create up-to-date answer objects for all questions in schema
        $interview = $this->interviewManager->initializeInterviewAnswers($application->getInterview());

        // Only admin and above, or the assigned interviewer, or the co interviewer should be able to conduct an interview
        if (!$this->interviewManager->loggedInUserCanSeeInterview($interview)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ApplicationInterviewType::class, $application, [
            'validation_groups' => ['interview'],
            'teams' => $teams,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isNewInterview = !$interview->getInterviewed();
            $interview->setCancelled(false);

            $em = $this->doctrine->getManager();
            $em->persist($interview);
            $em->flush();
            if ($isNewInterview && $form->get('saveAndSend')->isClicked()) {
                $interview->setInterviewed(true);
                $interview->setConducted(new \DateTime());
                $em->persist($interview);
                $em->flush();

                $this->eventDispatcher->dispatch(new InterviewConductedEvent($application), InterviewConductedEvent::NAME);
            }

            return $this->redirectToRoute('applications_show_interviewed', [
                'semester' => $application->getSemester()->getId(),
                'department' => $application->getAdmissionPeriod()->getDepartment()->getId(),
            ]);
        }

        return $this->render('interview/conduct.html.twig', [
            'application' => $application,
            'department' => $department,
            'teams' => $teams,
            'form' => $form->createView(),
        ]);
    }

    public function cancel(Interview $interview): RedirectResponse
    {
        $interview->setCancelled(true);
        $manager = $this->doctrine->getManager();
        $manager->persist($interview);
        $manager->flush();

        return $this->redirectToRoute('applications_show_assigned');
    }

    /**
     * Shows the given interview.
     */
    public function show(Application $application): Response
    {
        if (null === $interview = $application->getInterview()) {
            throw $this->createNotFoundException('Interview not found.');
        }

        // Only accessible for admin and above, or team members belonging to the same department as the interview
        if (!$this->interviewManager->loggedInUserCanSeeInterview($interview) ||
            $this->getUser() === $application->getUser()
        ) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('interview/show.html.twig', ['interview' => $interview,
            'application' => $application,
        ]);
    }

    /**
     * Deletes the given interview.
     */
    public function deleteInterview(Interview $interview, Request $request): RedirectResponse
    {
        $interview->getApplication()->setInterview(null);

        $em = $this->doctrine->getManager();
        $em->remove($interview);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Deletes a bulk of interviews.
     * Takes a list of application ids through a form POST request, and deletes the interviews associated with them.
     *
     * This method is intended to be called by an Ajax request.
     */
    public function bulkDeleteInterview(Request $request): JsonResponse
    {
        // Get the ids from the form
        $applicationIds = $request->request->get('application')['id'];

        // Get the application objects
        $em = $this->doctrine->getManager();
        $applications = $em
            ->getRepository(Application::class)
            ->findBy(['id' => $applicationIds]);

        // Delete the interviews
        foreach ($applications as $application) {
            $interview = $application->getInterview();
            if ($interview) {
                $em->remove($interview);
            }
            $application->setInterview(null);
        }
        $em->flush();

        // AJAX response
        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * Shows and handles the submission of the schedule interview form.
     * This method can also email the applicant with the info from the submitted form.
     */
    public function schedule(Request $request, Application $application): Response
    {
        if (null === $interview = $application->getInterview()) {
            throw $this->createNotFoundException('Interview not found.');
        }
        // Only admin and above, or the assigned interviewer should be able to book an interview
        if (!$this->interviewManager->loggedInUserCanSeeInterview($interview)) {
            throw $this->createAccessDeniedException();
        }

        // Set the default data for the form
        $defaultData = $this->interviewManager->getDefaultScheduleFormData($interview);

        $form = $this->createForm(ScheduleInterviewType::class, $defaultData);

        $form->handleRequest($request);

        $data = $form->getData();
        $mapLink = $data['mapLink'];
        if ($form->isSubmitted()) {
            if ($mapLink && !(mb_strpos((string) $mapLink, 'http') === 0)) {
                $mapLink = 'https://' . $mapLink;
            }
        }
        $invalidMapLink = $form->isSubmitted() && !empty($mapLink) && !$this->validateLink($mapLink);
        if ($invalidMapLink) {
            $this->addFlash('danger', 'Kartlinken er ikke gyldig');
        } elseif ($form->isSubmitted() && $form->isValid()) {
            if (!$interview->getResponseCode()) {
                $interview->generateAndSetResponseCode();
            }

            // Update the scheduled time for the interview
            $interview->setScheduled($data['datetime']);
            $interview->setRoom($data['room']);
            $interview->setCampus($data['campus']);

            $interview->setMapLink($mapLink);
            $interview->resetStatus();

            if ($form->get('preview')->isClicked()) {
                return $this->render('interview/preview.html.twig', [
                    'interview' => $interview,
                    'data' => $data,
                ]);
            }

            $em = $this->doctrine->getManager();
            $em->persist($interview);
            $em->flush();

            // Send email if the send button was clicked
            if ($form->get('saveAndSend')->isClicked()) {
                $this->eventDispatcher->dispatch(new InterviewEvent($interview, $data), InterviewEvent::SCHEDULE);
            }

            return $this->redirectToRoute('applications_show_assigned', ['department' => $application->getDepartment()->getId(), 'semester' => $application->getSemester()->getId()]);
        }

        return $this->render('interview/schedule.html.twig', [
            'form' => $form->createView(),
            'interview' => $interview,
            'application' => $application,
        ]);
    }

    private function validateLink($link): bool
    {
        if (empty($link)) {
            return false;
        }

        try {
            $headers = get_headers($link);
            $statusCode = intval(explode(' ', (string) $headers[0])[1]);
        } catch (\Exception) {
            return false;
        }

        return $statusCode < 400;
    }

    /**
     * Renders and handles the submission of the assign interview form.
     * This method is used to create a new interview, or update it, and assign it to the given application.
     * It sets the interviewer and interview schema according to the form.
     * This method is intended to be called by an Ajax request.
     */
    public function assign(Request $request, $id = null): JsonResponse
    {
        if ($id === null) {
            throw $this->createNotFoundException();
        }
        $em = $this->doctrine->getManager();
        $application = $em
            ->getRepository(Application::class)
            ->find($id);

        $user = $application->getUser();
        // Finds all the roles above admin in the hierarchy, used to populate dropdown menu with all admins
        $roles = $this->reversedRoleHierarchy->getParentRoles([Roles::TEAM_MEMBER]);

        $form = $this->createForm(CreateInterviewType::class, $application, [
            'roles' => $roles,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $application->getInterview()->setUser($user);
            $em->persist($application);
            $em->flush();

            return new JsonResponse(
                ['success' => true]
            );
        }

        return new JsonResponse(
            [
                'form' => $this->renderView('interview/assign_interview_form.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]
        );
    }

    /**
     * This method has the same purpose as assign, but assigns a bulk of applications at once.
     * It does not use the normal form validation routine, but manually updates each application.
     * This is because in addition to the standard form fields given by assignInterviewType, a list of application ids
     * are given by the bulk form checkboxes (see admission_admin twigs).
     *
     * This method is intended to be called by an Ajax request.
     */
    public function bulkAssign(Request $request): JsonResponse
    {
        // $roles = $this->reversedRoleHierarchy->getParentRoles([Roles::TEAM_MEMBER]);

        $form = $this->createForm(CreateInterviewType::class, null, [
        //    'roles' => $roles
        ]);
        if ($request->isMethod('POST')) {
            $em = $this->doctrine->getManager();
            // Get the info from the form
            $data = $request->request->all();
            // Get objects from database
            $interviewer = $em
                ->getRepository(User::class)
                ->findOneBy(['id' => $data['interview']['interviewer']]);

            $schema = $em
                ->getRepository(InterviewSchema::class)
                ->findOneBy(['id' => $data['interview']['interviewSchema']]);

            $applications = $em
                ->getRepository(Application::class)
                ->findBy(['id' => $data['application']['id']]);

            // Update or create new interviews for all the given applications
            foreach ($applications as $application) {
                $this->interviewManager->assignInterviewerToApplication($interviewer, $application);

                $application->getInterview()->setInterviewSchema($schema);
                $em->persist($application);
            }

            $em->flush();

            $this->addFlash('success', 'Søknadene ble fordelt til ' . $interviewer);

            return new JsonResponse([
                'success' => true,
                'request' => $request->request->all(),
            ]);
        }

        return new JsonResponse([
            'form' => $this->renderView('interview/assign_interview_form.html.twig', [
                'form' => $form->createView(),
            ]),
        ]);
    }

    public function acceptByResponseCode(Interview $interview): Response
    {
        $interview->acceptInterview();
        $manager = $this->doctrine->getManager();
        $manager->persist($interview);
        $manager->flush();

        $formattedDate = $interview->getScheduled()->format('d. M');
        $formattedTime = $interview->getScheduled()->format('H:i');
        $room = $interview->getRoom();

        $successMessage = "Takk for at du aksepterte intervjutiden. Da sees vi $formattedDate klokka $formattedTime i $room!";
        $this->addFlash('success', $successMessage);

        if ($interview->getUser() === $this->getUser()) {
            return $this->redirectToRoute('my_page');
        }

        return $this->redirectToRoute('interview_response', ['responseCode' => $interview->getResponseCode()]);
    }

    public function requestNewTime(Request $request, Interview $interview): Response
    {
        if (!$interview->isPending()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(InterviewNewTimeType::class, $interview, [
            'validation_groups' => ['newTimeRequest'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $interview->requestNewTime();
            $manager = $this->doctrine->getManager();
            $manager->persist($interview);
            $manager->flush();

            $this->interviewManager->sendRescheduleEmail($interview);
            $this->addFlash('success', 'Forspørsel om ny intervjutid er sendt. Vi tar kontakt med deg når vi har funnet en ny intervjutid.');

            if ($interview->getUser() === $this->getUser()) {
                return $this->redirectToRoute('my_page');
            }

            return $this->redirectToRoute('interview_response', ['responseCode' => $interview->getResponseCode()]);
        }

        return $this->render('interview/request_new_time.html.twig', [
            'interview' => $interview,
            'form' => $form->createView(),
        ]);
    }

    public function respond(Interview $interview): Response
    {
        $applicationStatus = $this->applicationManager->getApplicationStatus($interview->getApplication());

        return $this->render('interview/response.html.twig', [
            'interview' => $interview,
            'application_status' => $applicationStatus,
        ]);
    }

    public function cancelByResponseCode(Request $request, Interview $interview): Response
    {
        if (!$interview->isPending()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(CancelInterviewConfirmationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $interview->setCancelMessage($data['message']);
            $interview->cancel();
            $manager = $this->doctrine->getManager();
            $manager->persist($interview);
            $manager->flush();

            $this->interviewManager->sendCancelEmail($interview);
            $this->addFlash('success', 'Du har kansellert intervjuet ditt.');

            if ($interview->getUser() === $this->getUser()) {
                return $this->redirectToRoute('my_page');
            }

            return $this->redirectToRoute('interview_response', ['responseCode' => $interview->getResponseCode()]);
        }

        return $this->render('interview/response_confirm_cancel.html.twig', [
            'interview' => $interview,
            'form' => $form->createView(),
        ]);
    }

    public function editStatus(Request $request, Interview $interview): RedirectResponse
    {
        $status = intval($request->get('status'));
        try {
            $interview->setStatus($status);
        } catch (\InvalidArgumentException) {
            throw new BadRequestHttpException();
        }
        $em = $this->doctrine->getManager();
        $em->flush();

        return $this->redirectToRoute(
            'interview_schedule',
            ['id' => $interview->getApplication()->getId()]
        );
    }

    public function assignCoInterviewer(Interview $interview)
    {
        if ($interview->getUser() === $this->getUser()) {
            return $this->render('error/control_panel_error.html.twig', [
                'error' => 'Kan ikke legge til deg selv som medintervjuer på ditt eget intervju',
            ]);
        }

        if ($interview->getInterviewed()) {
            return $this->render('error/control_panel_error.html.twig', [
                'error' => 'Kan ikke legge til deg selv som medintervjuer etter intervjuet er gjennomført',
            ]);
        }

        if ($this->getUser() === $interview->getInterviewer()) {
            return $this->render('error/control_panel_error.html.twig', [
                'error' => 'Kan ikke legge til deg selv som medintervjuer når du allerede er intervjuer',
            ]);
        }

        $interview->setCoInterviewer($this->getUser());
        $em = $this->doctrine->getManager();
        $em->persist($interview);
        $em->flush();
        $this->eventDispatcher->dispatch(new InterviewEvent($interview), InterviewEvent::COASSIGN);

        return $this->redirectToRoute('applications_show_assigned');
    }

    public function adminAssignCoInterviewer(Request $request, Interview $interview)
    {
        $semester = $interview->getApplication()->getSemester();
        $department = $interview->getApplication()->getDepartment();
        $teamUsers = $this->doctrine
            ->getRepository(User::class)
            ->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester);

        $coInterviewers = array_merge(array_diff($teamUsers, [$interview->getInterviewer(), $interview->getCoInterviewer()]));
        $form = $this->createForm(AddCoInterviewerType::class, null, [
            'teamUsers' => $coInterviewers,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $data['user'];
            $interview->setCoInterviewer($user);
            $em = $this->doctrine->getManager();
            $em->persist($interview);
            $em->flush();

            if ($request->get('from') === 'schedule') {
                return $this->redirectToRoute('interview_schedule', ['id' => $interview->getApplication()->getId()]);
            }

            return $this->redirectToRoute('applications_show_assigned', [
                'department' => $department->getId(),
                'semester' => $semester->getId(),
            ]);
        }

        return $this->render('interview/assign_co_interview_form.html.twig', [
            'form' => $form->createView(),
            'interview' => $interview,
        ]);
    }

    public function clearCoInterviewer(Interview $interview): RedirectResponse
    {
        $interview->setCoInterviewer(null);
        $em = $this->doctrine->getManager();
        $em->persist($interview);
        $em->flush();

        return $this->redirectToRoute('applications_show_assigned');
    }
}
