<?php

namespace App\EventSubscriber;

use App\Event\TeamMembershipEvent;
use App\Mailer\MailingInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class IntroductionEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailingInterface $mailer,
        private readonly Environment $twig
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
            TeamMembershipEvent::CREATED => [
                ['sendWelcomeToTeamEmail', -1],
                ['sendGoogleEmail', -2],
            ],
        ];
    }

    public function sendWelcomeToTeamEmail(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();

        $team = $teamMembership->getTeam();
        $user = $teamMembership->getUser();

        if (count($user->getTeamMemberships()) > 1) {
            return;
        }

        $position = $teamMembership->getPositionName();

        $message = (new \Swift_Message())
            ->setSubject('Velkommen til ' . $team->getName())
            ->setFrom('vektorbot@vektorprogrammet.no')
            ->setTo($user->getEmail())
            ->setBody($this->twig->render('team_admin/welcome_team_membership_mail.html.twig', [
                'name' => $user->getFirstName(),
                'team' => $team->getName(),
                'position' => $position,
                'companyEmail' => $user->getCompanyEmail(),
            ]))
            ->setContentType('text/html');
        $this->mailer->send($message);
    }

    public function sendGoogleEmail(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();
        $user = $teamMembership->getUser();

        if (count($user->getTeamMemberships()) > 1) {
            return;
        }

        $message = (new \Swift_Message())
            ->setSubject('Fullfør oppsettet med din Vektor-epost')
            ->setFrom('vektorbot@vektorprogrammet.no')
            ->setTo($user->getCompanyEmail())
            ->setBody($this->twig->render('team_admin/welcome_google_mail.html.twig', [
                'name' => $user->getFirstName(),
            ]))
            ->setContentType('text/html');
        $this->mailer->send($message);
    }
}
