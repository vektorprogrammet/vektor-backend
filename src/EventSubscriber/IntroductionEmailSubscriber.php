<?php

namespace App\EventSubscriber;

use App\Event\TeamMembershipEvent;
use App\Mailer\MailerInterface;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class IntroductionEmailSubscriber implements EventSubscriberInterface
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            TeamMembershipEvent::CREATED => array(
                array('sendWelcomeToTeamEmail', -1),
                array('sendGoogleEmail', -2),
            ),
        );
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

        $message = (new Swift_Message())
            ->setSubject('Velkommen til '.$team->getName())
            ->setFrom('vektorbot@vektorprogrammet.no')
            ->setTo($user->getEmail())
            ->setBody($this->twig->render('team_admin/welcome_team_membership_mail.html.twig', array(
                'name' => $user->getFirstName(),
                'team' => $team->getName(),
                'position' => $position,
                'companyEmail' => $user->getCompanyEmail()
            )))
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

        $message = (new Swift_Message())
            ->setSubject('Fullfør oppsettet med din Vektor-epost')
            ->setFrom('vektorbot@vektorprogrammet.no')
            ->setTo($user->getCompanyEmail())
            ->setBody($this->twig->render('team_admin/welcome_google_mail.html.twig', array(
                'name' => $user->getFirstName(),
            )))
            ->setContentType('text/html');
        $this->mailer->send($message);
    }
}
