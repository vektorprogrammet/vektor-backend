<?php

namespace App\EventSubscriber;

use App\Event\ApplicationCreatedEvent;
use App\Mailer\MailingInterface;
use App\Service\AdmissionNotifier;
use App\Service\UserRegistration;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class ApplicationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailingInterface $mailer,
        private readonly Environment $twig,
        private readonly AdmissionNotifier $admissionNotifier,
        private readonly UserRegistration $userRegistrationService)
    {
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ApplicationCreatedEvent::NAME => [
                ['sendConfirmationMail', 0],
                ['createAdmissionSubscriber', -2],
            ],
        ];
    }

    public function createAdmissionSubscriber(ApplicationCreatedEvent $event): void
    {
        $application = $event->getApplication();
        $department = $application->getUser()->getDepartment();
        $email = $application->getUser()->getEmail();
        try {
            $this->admissionNotifier->createSubscription($department, $email, true);
        } catch (\Exception) {
            // Ignore
        }
    }

    public function sendConfirmationMail(ApplicationCreatedEvent $event): void
    {
        $application = $event->getApplication();
        $user = $application->getUser();
        $newUserCode = null;
        if (!$user->getPassword()) {
            $newUserCode = $this->userRegistrationService->setNewUserCode($user);
        }

        $template = 'admission/admission_email.html.twig';
        if ($application->getUser()->hasBeenAssistant()) {
            $template = 'admission/admission_existing_email.html.twig';
        }

        // Send a confirmation email with a copy of the application
        $emailMessage = (new TemplatedEmail())
            ->subject('Søknad - Vektorassistent')
            ->replyTo($application->getDepartment()->getEmail())
            ->to($application->getUser()->getEmail())
            ->from('vektorbot@vektorprogrammet.no')
            ->htmlTemplate($template)
            ->context([
                'application' => $application,
                'newUserCode' => $newUserCode,
            ]);

        $this->mailer->send($emailMessage);
    }
}
