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
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

class InterviewManager
{
    private const MAX_NUM_ACCEPT_INTERVIEW_REMINDERS_SENT = 3;

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly Mailer $mailer,
        private readonly Environment $twig,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $em,
        private readonly RouterInterface $router,
        private readonly SmsSenderInterface $smsSender
    ) {
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

        $existingQuestions = array_map(fn (InterviewAnswer $interviewAnswer) => $interviewAnswer->getInterviewQuestion(), $existingAnswers);

        foreach ($interview->getInterviewSchema()->getInterviewQuestions() as $interviewQuestion) {
            $interviewAlreadyHasQuestion = in_array($interviewQuestion, $existingQuestions, true);
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

    public function assignInterviewerToApplication(User $interviewer, Application $application): void
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

    public function sendScheduleEmail(Interview $interview, array $data): void
    {
        $message = (new TemplatedEmail())
            ->subject('Intervju for vektorprogrammet')
            ->to($data['to'])
            ->replyTo($data['from'])
            ->from(new Address('vektorbot@vektorprogrammet.no', 'Vektorprogrammet'))
            ->htmlTemplate('interview/email.html.twig')
            ->context(
                [
                    'message' => $data['message'],
                    'datetime' => $data['datetime'],
                    'room' => $data['room'],
                    'campus' => $data['campus'],
                    'mapLink' => $data['mapLink'],
                    'fromName' => $interview->getInterviewer()->getFirstName() . ' ' . $interview->getInterviewer()->getLastName(),
                    'fromMail' => $data['from'],
                    'fromPhone' => $interview->getInterviewer()->getPhone(),
                    'responseCode' => $interview->getResponseCode(),
                ]
            );
        $this->mailer->send($message);
    }

    public function sendRescheduleEmail(Interview $interview): void
    {
        $application = $this->em->getRepository(Application::class)->findOneBy(['interview' => $interview]);
        $user = $interview->getUser();
        $interviewers = [];
        $interviewers[] = $interview->getInterviewer();
        if (!is_null($interview->getCoInterviewer())) {
            $interviewers[] = $interview->getCoInterviewer();
        }

        foreach ($interviewers as $interviewer) {
            $message = (new TemplatedEmail())
                ->subject("[$user] Intervju: Ønske om ny tid")
                ->to($interviewer->getEmail())
                ->from(new Address('vektorbot@vektorprogrammet.no', 'Vektorprogrammet'))
                ->htmlTemplate('interview/reschedule_email.html.twig')
                ->context([
                    'interview' => $interview,
                    'application' => $application,
                ]);

            $this->mailer->send($message);
        }
    }

    public function sendCancelEmail(Interview $interview): void
    {
        $user = $interview->getUser();

        $interviewers = [];
        $interviewers[] = $interview->getInterviewer();
        if (!is_null($interview->getCoInterviewer())) {
            $interviewers[] = $interview->getCoInterviewer();
        }

        // Send mail to interviewer and co-interviewer
        foreach ($interviewers as $interviewer) {
            $message = (new TemplatedEmail())
                ->subject("[$user] Intervju: Kansellert")
                ->to($interviewer->getEmail())
                ->from(new Address('vektorbot@vektorprogrammet.no', 'Vektorprogrammet'))
                ->htmlTemplate('interview/cancel_email.html.twig')
                ->context([
                    'interview' => $interview,
                ]);

            $this->mailer->send($message);
        }
    }

    public function sendInterviewScheduleToInterviewer(User $interviewer): void
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

        $message = (new TemplatedEmail())
            ->subject('Dine intervjuer dette semesteret')
            ->to($interviewer->getEmail())
            ->from(new Address('vektorbot@vektorprogrammet.no', 'Vektorprogrammet'))
            ->htmlTemplate('interview/schedule_of_interviews_email.html.twig')
            ->context([
                'interviews' => $interviews,
                'interviewer' => $interviewer,
            ]);

        $this->mailer->send($message);
    }

    public function sendAcceptInterviewReminders(): void
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

    private function sendAcceptInterviewReminderToInterviewee(Interview $interview): void
    {
        $message = (new TemplatedEmail())
            ->subject('Påminnelse om intervju med Vektorprogrammet')
            ->to($interview->getUser()->getEmail())
            ->from('ikkesvar@vektorprogrammet.no')
            ->htmlTemplate('interview/accept_interview_reminder_email.html.twig')
            ->context([
                'interview' => $interview,
            ]);

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
