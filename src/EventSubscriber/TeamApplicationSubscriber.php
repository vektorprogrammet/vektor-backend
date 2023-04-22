<?php

namespace App\EventSubscriber;

use App\Event\TeamApplicationCreatedEvent;
use App\Mailer\MailingInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class TeamApplicationSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailingInterface $mailer, private readonly Environment $twig, private readonly RequestStack $requestStack)
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
            TeamApplicationCreatedEvent::NAME => [
                ['sendConfirmationMail', 0],
                ['sendApplicationToTeamMail', 0],
                ['addFlashMessage', -1],
            ],
        ];
    }

    public function sendConfirmationMail(TeamApplicationCreatedEvent $event)
    {
        $application = $event->getTeamApplication();
        $team = $application->getTeam();

        if (null === $email = $team->getEmail()) {
            $email = $team->getDepartment()->getEmail();
        }

        $receipt = (new \Swift_Message())
            ->setSubject('Søknad til ' . $team->getName() . ' mottatt')
            ->setFrom([$email => $team->getName()])
            ->setReplyTo($email)
            ->setTo($application->getEmail())
            ->setBody($this->twig->render('team/receipt.html.twig', [
                'team' => $team,
            ]));
        $this->mailer->send($receipt);
    }

    public function sendApplicationToTeamMail(TeamApplicationCreatedEvent $event)
    {
        $application = $event->getTeamApplication();
        $team = $application->getTeam();

        if (null === $email = $team->getEmail()) {
            $email = $team->getDepartment()->getEmail();
        }

        $receipt = (new \Swift_Message())
            ->setSubject('Ny søker til ' . $team->getName())
            ->setFrom(['vektorprogrammet@vektorprogrammet.no' => 'Vektorprogrammet'])
            ->setReplyTo($application->getEmail())
            ->setTo($email)
            ->setBody($this->twig->render('team/application_email.html.twig', [
                'application' => $application,
            ]));
        $this->mailer->send($receipt);
    }

    public function addFlashMessage()
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', 'Søknaden er mottatt.');
    }
}
