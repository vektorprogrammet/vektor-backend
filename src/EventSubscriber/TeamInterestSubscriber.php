<?php

namespace App\EventSubscriber;

use App\Event\TeamInterestCreatedEvent;
use App\Mailer\MailingInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class TeamInterestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailingInterface $mailer,
        private readonly Environment $twig,
        private readonly RequestStack $requestStack)
    {
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

        $receipt = (new TemplatedEmail())
            ->subject('Teaminteresse i Vektorprogrammet')
            ->from([$fromEmail => "Vektorprogrammet $department"])
            ->replyTo($fromEmail)
            ->to($teamInterest->getEmail())
            ->htmlTemplate('team_interest/team_interest_receipt.html.twig')
            ->context([
                'teamInterest' => $teamInterest,
            ]);

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
