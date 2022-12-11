<?php

namespace App\EventSubscriber;

use App\Event\TeamMembershipEvent;
use App\Service\RoleManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TeamMembershipSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private RoleManager $roleManager;
    private EntityManagerInterface $em;
    private RequestStack $requestStack;

    public function __construct(
        LoggerInterface $logger,
        RoleManager $roleManager,
        EntityManagerInterface $em,
        RequestStack $requestStack
    ) {
        $this->logger = $logger;
        $this->roleManager = $roleManager;
        $this->em = $em;
        $this->requestStack = $requestStack;
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
                array('updateUserRole', 5),
                array('activateTeamMembership', 2),
                array('addCreatedFlashMessage', -1),
            ),
            TeamMembershipEvent::EDITED  => array(
                array('updateUserRole', 5),
                array('activateTeamMembership', 2),
                array('addUpdatedFlashMessage', -1),
            ),
            TeamMembershipEvent::DELETED => array(
                array('logDeletedEvent', 10),
                array('updateUserRole', 5),
            ),
        );
    }

    public function addCreatedFlashMessage(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();

        $team = $teamMembership->getTeam();
        $user = $teamMembership->getUser();
        $position = $teamMembership->getPosition();

        $this->requestStack
            ->getSession()
            ->getFlashBag()
            ->add('success', "$user har blitt lagt til i $team som $position.");
    }

    public function addUpdatedFlashMessage(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();

        $team = $teamMembership->getTeam();
        $user = $teamMembership->getUser();
        $position = $teamMembership->getPosition();

        $this->requestStack
            ->getSession()
            ->getFlashBag()
            ->add('success', "$user i $team med stilling $position har blitt oppdatert.");
    }

    public function logDeletedEvent(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();

        $user = $teamMembership->getUser();
        $position = $teamMembership->getPosition();
        $team = $teamMembership->getTeam();
        $department = $team->getDepartment();

        $startSemester = $teamMembership->getStartSemester()->getName();
        $endSemester = $teamMembership->getEndSemester();

        $endStr = $endSemester !== null ? 'to '.$endSemester->getName() : '';

        $this->logger->info(
            "TeamMembership deleted: $user (position: $position), " .
            "active from $startSemester $endStr, was deleted from $team ($department)"
        );
    }

    public function activateTeamMembership(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();
        $now = new DateTime();
        if ($teamMembership->getEndSemester() === null || $teamMembership->getEndSemester()->getEndDate() > $now) {
            $teamMembership->setIsSuspended(false);
        }
        $this->em->persist($teamMembership);
        $this->em->flush();
    }

    public function updateUserRole(TeamMembershipEvent $event)
    {
        $user = $event->getTeamMembership()->getUser();

        $this->roleManager->updateUserRole($user);
    }
}
