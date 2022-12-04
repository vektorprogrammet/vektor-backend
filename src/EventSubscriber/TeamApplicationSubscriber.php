<?php

namespace AppBundle\EventSubscriber;

use App\Event\TeamApplicationCreatedEvent;
use App\Mailer\MailerInterface;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

class TeamApplicationSubscriber implements EventSubscriberInterface
{
    private MailerInterface $mailer;
    private Environment $twig;
    private SessionInterface $session;

    /**
     * ApplicationAdmissionSubscriber constructor.
     */
    public function __construct(MailerInterface $mailer, Environment $twig, SessionInterface $session)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->session = $session;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            TeamApplicationCreatedEvent::NAME => array(
                array('sendConfirmationMail', 0),
                array('sendApplicationToTeamMail', 0),
                array('addFlashMessage', -1),
            ),
        );
    }

    public function sendConfirmationMail(TeamApplicationCreatedEvent $event)
    {
        $application = $event->getTeamApplication();
        $team = $application->getTeam();

        if (null === $email = $team->getEmail()) {
            $email = $team->getDepartment()->getEmail();
        }

        $receipt = (new Swift_Message())
            ->setSubject('Søknad til '.$team->getName().' mottatt')
            ->setFrom(array($email => $team->getName()))
            ->setReplyTo($email)
            ->setTo($application->getEmail())
            ->setBody($this->twig->render('team/receipt.html.twig', array(
                'team' => $team,
            )));
        $this->mailer->send($receipt);
    }

    public function sendApplicationToTeamMail(TeamApplicationCreatedEvent $event)
    {
        $application = $event->getTeamApplication();
        $team = $application->getTeam();

        if (null === $email = $team->getEmail()) {
            $email = $team->getDepartment()->getEmail();
        }

        $receipt = (new Swift_Message())
            ->setSubject('Ny søker til '.$team->getName())
            ->setFrom(array('vektorprogrammet@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setReplyTo($application->getEmail())
            ->setTo($email)
            ->setBody($this->twig->render('team/application_email.html.twig', array(
                'application' => $application,
            )));
        $this->mailer->send($receipt);
    }

    public function addFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Søknaden er mottatt.');
    }
}
