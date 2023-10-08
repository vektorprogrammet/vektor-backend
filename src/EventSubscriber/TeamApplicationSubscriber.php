<?php

namespace App\EventSubscriber;

use App\Event\TeamApplicationCreatedEvent;
use App\Mailer\MailingInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class TeamApplicationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailingInterface $mailer,
        private readonly Environment $twig,
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TeamApplicationCreatedEvent::NAME => [
                ['sendConfirmationMail', 0],
                ['sendApplicationToTeamMail', 0],
                ['addFlashMessage', -1],
            ],
        ];
    }

    public function sendConfirmationMail(TeamApplicationCreatedEvent $event): void
    {
        $application = $event->getTeamApplication();
        $team = $application->getTeam();

        if (null === $email = $team->getEmail()) {
            $email = $team->getDepartment()->getEmail();
        }

        $receipt = (new TemplatedEmail())
            ->subject('Søknad til ' . $team->getName() . ' mottatt')
            ->from(new Address($email, $team->getName()))
            ->replyTo($email)
            ->to($application->getEmail())
            ->htmlTemplate('team/receipt.html.twig')
            ->context([
                'team' => $team,
            ]);
        $this->mailer->send($receipt);
    }

    public function sendApplicationToTeamMail(TeamApplicationCreatedEvent $event): void
    {
        $application = $event->getTeamApplication();
        $team = $application->getTeam();

        if (null === $email = $team->getEmail()) {
            $email = $team->getDepartment()->getEmail();
        }

        $receipt = (new TemplatedEmail())
            ->subject('Ny søker til ' . $team->getName())
            ->from(new Address('vektorprogrammet@vektorprogrammet.no', 'Vektorprogrammet'))
            ->replyTo($application->getEmail())
            ->to($email)
            ->htmlTemplate('team/application_email.html.twig')
            ->context([
                'application' => $application,
            ]);
        $this->mailer->send($receipt);
    }

    public function addFlashMessage(): void
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', 'Søknaden er mottatt.');
    }
}
