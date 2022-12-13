<?php

namespace App\EventSubscriber;

use App\Event\InterviewConductedEvent;
use App\Event\InterviewEvent;
use App\Mailer\MailerInterface;
use App\Service\InterviewManager;
use App\Service\InterviewNotificationManager;
use App\Service\SbsData;
use App\Sms\Sms;
use App\Sms\SmsSenderInterface;
use Psr\Log\LoggerInterface;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class InterviewSubscriber implements EventSubscriberInterface
{
    private MailerInterface $mailer;
    private Environment $twig;
    private LoggerInterface $logger;
    private SbsData $sbsData;
    private InterviewNotificationManager $notificationManager;
    private InterviewManager $interviewManager;
    private SmsSenderInterface $smsSender;
    private RouterInterface $router;
    private RequestStack $requestStack;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        LoggerInterface $logger,
        SbsData $sbsData,
        InterviewNotificationManager $notificationManager,
        InterviewManager $interviewManager,
        SmsSenderInterface $smsSender,
        RouterInterface $router,
        RequestStack $requestStack
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->sbsData = $sbsData;
        $this->notificationManager = $notificationManager;
        $this->interviewManager = $interviewManager;
        $this->smsSender = $smsSender;
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            InterviewConductedEvent::NAME => [
                ['logEvent', 2],
                ['sendSlackNotifications', 1],
                ['sendInterviewReceipt', 0],
                ['addFlashMessage', -1],
            ],
            InterviewEvent::SCHEDULE => [
                ['sendScheduleEmail', 0],
                ['sendScheduleSms', 0],
            ],
            InterviewEvent::COASSIGN => [
                ['sendCoAssignedEmail', 0]
            ]
        ];
    }

    public function sendInterviewReceipt(InterviewConductedEvent $event)
    {
        $application = $event->getApplication();
        $interviewer = $application->getInterview()->getInterviewer();

        // Send email to the interviewee with a summary of the interview
        $emailMessage = (new Swift_Message())
            ->setSubject('Vektorprogrammet intervju')
            ->setReplyTo([$interviewer->getDepartment()->getEmail() => 'Vektorprogrammet'])
            ->setTo($application->getUser()->getEmail())
            ->setReplyTo($interviewer->getEmail())
            ->setBody($this->twig->render('interview/interview_summary_email.html.twig', [
                'application' => $application,
                'interviewer' => $interviewer,
            ]))
            ->setContentType('text/html');
        $this->mailer->send($emailMessage);
    }

    public function addFlashMessage(InterviewConductedEvent $event)
    {
        $user = $event->getApplication()->getUser();
        $message = "Intervjuet med $user ble lagret. En kvittering med et sammendrag av
                    praktisk informasjon fra intervjuet blir sendt til {$user->getEmail()}.";

        $this->requestStack->getSession()->getFlashBag()->add('success', $message);
    }

    public function logEvent(InterviewConductedEvent $event)
    {
        $application = $event->getApplication();

        $interviewee = $application->getUser();

        $department = $interviewee->getDepartment();

        $this->logger->info("$department: New interview with $interviewee registered");
    }

    public function sendSlackNotifications(InterviewConductedEvent $event)
    {
        $application = $event->getApplication();

        $department = $application->getDepartment();
        $semester = $application->getSemester();

        if (
            $this->sbsData->getInterviewedAssistantsCount() === 10 ||
            $this->sbsData->getInterviewedAssistantsCount() % 25 === 0
        ) {
            $this->notificationManager->sendApplicationCountNotification($department, $semester);
        }

        if (
            $this->sbsData->applicantsNotYetInterviewedCount() <= 0 &&
            $this->sbsData->getStep() >= 4
        ) {
            $this->notificationManager->sendInterviewsCompletedNotification($department, $semester);
        }
    }

    public function sendScheduleEmail(InterviewEvent $event)
    {
        $this->interviewManager->sendScheduleEmail($event->getInterview(), $event->getData());
    }

    public function sendScheduleSms(InterviewEvent $event)
    {
        $interview = $event->getInterview();
        $data = $event->getData();
        $user = $interview->getUser();
        $phoneNumber = $user->getPhone();
        $interviewer = $interview->getInterviewer();

        $validNumber = $this->smsSender->validatePhoneNumber($phoneNumber);
        if (!$validNumber) {
            $this->logger->alert("Kunne ikke sende schedule sms til *$user*\n Tlf.nr.: *$phoneNumber*");
            return;
        }

        $campus = empty($data['campus']) ? "" : ("\nCampus: " . $data['campus']);

        $message =
            $data['message'] .
            "\n\n" .
            "Tid: ".$data['datetime']->format('d.m.Y - H:i') .
            "\n" .
            "Rom: ".$data['room'] .
            $campus .
            "\n\n" .
            "Vennligst følg linken under for å godkjenne tidspunktet eller be om ny tid:\n" .
            $this->router->generate(
                'interview_response',
                ['responseCode' => $interview->getResponseCode()],
                RouterInterface::ABSOLUTE_URL
            ) .
            "\n\n" .
            "Mvh $interviewer, Vektorprogrammet\n" .
            $interviewer->getEmail() .
            "\n" .
            $interviewer->getPhone();

        $sms = new Sms();
        $sms->setMessage($message);
        $sms->setSender("Vektor");
        $sms->setRecipients([$phoneNumber]);

        $this->smsSender->send($sms);
    }

    public function sendCoAssignedEmail(InterviewEvent $event)
    {
        $interview = $event->getInterview();
        $emailMessage = (new Swift_Message())
            ->setSubject('Vektorprogrammet intervju')
            ->setFrom(['vektorbot@vektorprogrammet.no' => 'Vektorprogrammet'])
            ->setTo($interview->getInterviewer()->getEmail())
            ->setReplyTo($interview->getCoInterviewer()->getEmail())
            ->setBody($this->twig->render('interview/co_interviewer_email.html.twig', [
                'interview' => $interview
            ]));
        $this->mailer->send($emailMessage);
    }
}
