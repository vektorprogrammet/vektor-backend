<?php

namespace AppBundle\EventSubscriber;

use App\Event\TeamInterestCreatedEvent;
use App\Mailer\MailerInterface;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

class TeamInterestSubscriber implements EventSubscriberInterface
{
    private MailerInterface $mailer;
    private Environment $twig;
    private SessionInterface $session;

    /**
     * TeamInterestSubscriber constructor.
     */
    public function __construct(MailerInterface $mailer, Environment $twig, SessionInterface $session)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return array(TeamInterestCreatedEvent::NAME => array(
            array('sendConfirmationMail', 0),
            array('addFlashMessage', -1),
        ));
    }

    public function sendConfirmationMail(TeamInterestCreatedEvent $event)
    {
        $teamInterest = $event->getTeamInterest();
        $department = $teamInterest->getDepartment();
        $fromEmail = $department->getEmail();

        $receipt = (new Swift_Message())
            ->setSubject("Teaminteresse i Vektorprogrammet")
            ->setFrom(array($fromEmail => "Vektorprogrammet $department"))
            ->setReplyTo($fromEmail)
            ->setTo($teamInterest->getEmail())
            ->setBody($this->twig->render(":team_interest:team_interest_receipt.html.twig", array(
                'teamInterest' => $teamInterest,
            )))
            ->setContentType('text/html');
        $this->mailer->send($receipt);
    }

    public function addFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Takk! Vi kontakter deg så fort som mulig.');
    }
}