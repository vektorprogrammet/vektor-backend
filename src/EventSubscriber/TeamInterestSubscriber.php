<?php

namespace App\EventSubscriber;

use App\Event\TeamInterestCreatedEvent;
use App\Mailer\MailerInterface;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class TeamInterestSubscriber implements EventSubscriberInterface
{
    private MailerInterface $mailer;
    private Environment $twig;
    private RequestStack $requestStack;

    /**
     * TeamInterestSubscriber constructor.
     */
    public function __construct(MailerInterface $mailer, Environment $twig, RequestStack $requestStack)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [TeamInterestCreatedEvent::NAME => [
            ['sendConfirmationMail', 0],
            ['addFlashMessage', -1],
        ]];
    }

    public function sendConfirmationMail(TeamInterestCreatedEvent $event)
    {
        $teamInterest = $event->getTeamInterest();
        $department = $teamInterest->getDepartment();
        $fromEmail = $department->getEmail();

        $receipt = (new Swift_Message())
            ->setSubject("Teaminteresse i Vektorprogrammet")
            ->setFrom([$fromEmail => "Vektorprogrammet $department"])
            ->setReplyTo($fromEmail)
            ->setTo($teamInterest->getEmail())
            ->setBody($this->twig->render("team_interest/team_interest_receipt.html.twig", [
                'teamInterest' => $teamInterest,
            ]))
            ->setContentType('text/html');
        $this->mailer->send($receipt);
    }

    public function addFlashMessage()
    {
        $this->requestStack
            ->getSession()
            ->getFlashBag()
            ->add('success', 'Takk! Vi kontakter deg så fort som mulig.');
    }
}
