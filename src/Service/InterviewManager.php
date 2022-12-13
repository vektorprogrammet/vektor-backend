<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\Interview;
use App\Entity\InterviewAnswer;
use App\Entity\InterviewStatusType;
use App\Entity\User;
use App\Mailer\Mailer;
use App\Role\Roles;
use App\Sms\Sms;
use App\Sms\SmsSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

class InterviewManager
{
    private TokenStorageInterface $tokenStorage;
    private AuthorizationCheckerInterface $authorizationChecker;
    private Mailer $mailer;
    private Environment $twig;
    private LoggerInterface $logger;
    private EntityManagerInterface $em;
    private RouterInterface $router;
    private SmsSenderInterface $smsSender;

    private const MAX_NUM_ACCEPT_INTERVIEW_REMINDERS_SENT = 3;

    /**
     * InterviewManager constructor.
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        Mailer $mailer,
        Environment $twig,
        LoggerInterface $logger,
        EntityManagerInterface $em,
        RouterInterface $router,
        SmsSenderInterface $smsSender
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->em = $em;
        $this->router = $router;
        $this->smsSender = $smsSender;
    }

    /**
     * Only team leader and above, or the assigned interviewer should be able to see the interview.
     */
    public function loggedInUserCanSeeInterview(Interview $interview): bool
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return $this->authorizationChecker->isGranted(Roles::TEAM_LEADER) ||
               $interview->isInterviewer($user) ||
               $interview->isCoInterviewer($user);
    }

    public function initializeInterviewAnswers(Interview $interview): Interview
    {
        $existingAnswers = $interview->getInterviewAnswers();
        if (!is_array($existingAnswers)) {
            $existingAnswers = $existingAnswers->toArray();
        }

        $existingQuestions = array_map(function (InterviewAnswer $interviewAnswer) {
            return $interviewAnswer->getInterviewQuestion();
        }, $existingAnswers);

        foreach ($interview->getInterviewSchema()->getInterviewQuestions() as $interviewQuestion) {
            $interviewAlreadyHasQuestion = array_search($interviewQuestion, $existingQuestions, true) !== false;
            if ($interviewAlreadyHasQuestion) {
                continue;
            }

            $answer = new InterviewAnswer();
            $answer->setInterview($interview);
            $answer->setInterviewQuestion($interviewQuestion);

            $interview->addInterviewAnswer($answer);
        }

        return $interview;
    }

    public function assignInterviewerToApplication(User $interviewer, Application $application)
    {
        $interview = $application->getInterview();
        if (!$interview) {
            $interview = new Interview();
            $application->setInterview($interview);
        }
        $interview->setInterviewed(false);
        $interview->setUser($application->getUser());
        $interview->setInterviewer($interviewer);
    }

    public function sendScheduleEmail(Interview $interview, array $data)
    {
        $message = (new \Swift_Message())
            ->setSubject('Intervju for vektorprogrammet')
            ->setTo($data['to'])
            ->setReplyTo($data['from'])
            ->setBody(
                $this->twig->render(
                    'interview/email.html.twig',
                    ['message' => $data['message'],
                        'datetime' => $data['datetime'],
                        'room' => $data['room'],
                        'campus' => $data['campus'],
                        'mapLink' => $data['mapLink'],
                        'fromName' => $interview->getInterviewer()->getFirstName() . ' ' . $interview->getInterviewer()->getLastName(),
                        'fromMail' => $data['from'],
                        'fromPhone' => $interview->getInterviewer()->getPhone(),
                        'responseCode' => $interview->getResponseCode(),
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }

    public function sendRescheduleEmail(Interview $interview)
    {
        $application = $this->em->getRepository(Application::class)->findOneBy(['interview' => $interview]);
        $user = $interview->getUser();
        $interviewers = [];
        $interviewers[] = $interview->getInterviewer();
        if (!is_null($interview->getCoInterviewer())) {
            $interviewers[] = $interview->getCoInterviewer();
        }

        foreach ($interviewers as $interviewer) {
            $message = (new \Swift_Message())
                ->setSubject("[$user] Intervju: Ønske om ny tid")
                ->setTo($interviewer->getEmail())
                ->setBody(
                    $this->twig->render(
                        'interview/reschedule_email.html.twig',
                        ['interview' => $interview,
                            'application' => $application,
                        ]
                    ),
                    'text/html'
                );

            $this->mailer->send($message);
        }
    }

    public function sendCancelEmail(Interview $interview)
    {
        $user = $interview->getUser();

        $interviewers = [];
        $interviewers[] = $interview->getInterviewer();
        if (!is_null($interview->getCoInterviewer())) {
            $interviewers[] = $interview->getCoInterviewer();
        }

        // Send mail to interviewer and co-interviewer
        foreach ($interviewers as $interviewer) {
            $message = (new \Swift_Message())
                ->setSubject("[$user] Intervju: Kansellert")
                ->setTo($interviewer->getEmail())
                ->setBody(
                    $this->twig->render(
                        'interview/cancel_email.html.twig',
                        ['interview' => $interview,
                        ]
                    ),
                    'text/html'
                );

            $this->mailer->send($message);
        }
    }

    public function sendInterviewScheduleToInterviewer(User $interviewer)
    {
        $interviews = $this->em->getRepository(Interview::class)->findUncompletedInterviewsByInterviewerInCurrentSemester($interviewer);

        $nothingMoreToDo = true;
        foreach ($interviews as $interview) {
            $status = $interview->getInterviewStatus();

            if ($status === InterviewStatusType::NO_CONTACT ||
                 $status === InterviewStatusType::PENDING ||
                 $status === InterviewStatusType::REQUEST_NEW_TIME ||
                 $status === InterviewStatusType::ACCEPTED
            ) {
                $nothingMoreToDo = false;
                break;
            }
        }

        if ($nothingMoreToDo) {
            return;
        }

        $message = (new \Swift_Message())
             ->setSubject('Dine intervjuer dette semesteret')
             ->setTo($interviewer->getEmail())
             ->setBody(
                 $this->twig->render(
                     'interview/schedule_of_interviews_email.html.twig',
                     [
                         'interviews' => $interviews,
                         'interviewer' => $interviewer,
                     ]
                 ),
                 'text/html'
             );

        $this->mailer->send($message);
    }

    public function sendAcceptInterviewReminders()
    {
        $interviews = $this->em->getRepository(Interview::class)->findAcceptInterviewNotificationRecipients(new \DateTime());
        /** @var Interview $interview */
        foreach ($interviews as $interview) {
            $oneDay = new \DateInterval('P1D');
            $now = new \DateTime();
            $moreThan24HoursSinceScheduled = $now->sub($oneDay) > $interview->getLastScheduleChanged();
            if ($interview->getNumAcceptInterviewRemindersSent() < self::MAX_NUM_ACCEPT_INTERVIEW_REMINDERS_SENT && $moreThan24HoursSinceScheduled) {
                $this->sendAcceptInterviewReminderToInterviewee($interview);
            }
        }
    }

    private function sendAcceptInterviewReminderToInterviewee(Interview $interview)
    {
        $message = (new \Swift_Message())
            ->setSubject('Påminnelse om intervju med Vektorprogrammet')
            ->setTo($interview->getUser()->getEmail())
            ->setBody(
                $this->twig->render(
                    'interview/accept_interview_reminder_email.html.twig',
                    [
                        'interview' => $interview,
                    ]
                ),
                'text/html'
            );

        $this->mailer->send($message);

        $interview->incrementNumAcceptInterviewRemindersSent();
        $this->em->persist($interview);
        $this->em->flush();

        $maxNum = self::MAX_NUM_ACCEPT_INTERVIEW_REMINDERS_SENT;
        $this->logger->info("
            Accept interview reminder sent to {$interview->getUser()}.
            ({$interview->getNumAcceptInterviewRemindersSent()}/$maxNum reminders sent)");

        $oneDay = new \DateInterval('P1D');
        $now = new \DateTime();
        $lessThan24HoursUntilInterview = $now->add($oneDay) > $interview->getScheduled();
        if ($lessThan24HoursUntilInterview) {
            $smsMessage =
                "Hei {$interview->getUser()->getFirstName()}\n" .
                "Vi har satt opp intervjutid for deg og trenger å vite om den passer.\n\n" .
                "Gå til https://vektorprogrammet.no{$this->router->generate('interview_response', ['responseCode' => $interview->getResponseCode()])} for å se intervjutiden og svare.\n" .
                'Mvh Vektorprogrammet.';
            $sms = new Sms();
            $sms->setMessage($smsMessage);
            $sms->setSender('Vektor');
            $sms->setRecipients([$interview->getUser()->getPhone()]);

            $this->smsSender->send($sms);
        }
    }

    public function getDefaultScheduleFormData(Interview $interview): array
    {
        $previousScheduledInterview = $this->em->getRepository(Interview::class)
            ->findLastScheduledByUserInAdmissionPeriod($interview->getInterviewer(), $interview->getApplication()->getAdmissionPeriod());
        $room = null;
        $campus = null;
        $mapLink = null;
        if ($previousScheduledInterview) {
            $room = $previousScheduledInterview->getRoom();
            $campus = $previousScheduledInterview->getCampus();
            $mapLink = $previousScheduledInterview->getMapLink();
        }

        $message = "Hei, {$interview->getUser()->getFirstName()}!
         
Vi har satt opp et intervju for deg angående opptak til vektorprogrammet. 
Vennligst gi beskjed til meg hvis tidspunktet ikke passer.";

        return [
            'datetime' => $interview->getScheduled(),
            'room' => $interview->getRoom() ?: $room,
            'campus' => $interview->getCampus() ?: $campus,
            'mapLink' => $interview->getMapLink() ?: $mapLink,
            'message' => $message,
            'from' => $interview->getInterviewer()->getEmail(),
            'to' => $interview->getUser()->getEmail(),
        ];
    }
}
