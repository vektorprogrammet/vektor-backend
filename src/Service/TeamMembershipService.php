<?php

namespace App\Service;

use App\Entity\Semester;
use App\Entity\TeamMembership;
use App\Event\TeamMembershipEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TeamMembershipService
{
    /**
     * TeamMembershipService constructor.
     */
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public function updateTeamMemberships(): array
    {
        $teamMemberships = $this->em->getRepository(TeamMembership::class)->findBy(['isSuspended' => false]);
        $currentSemesterStartDate = $this->em->getRepository(Semester::class)->findOrCreateCurrentSemester()->getStartDate();
        foreach ($teamMemberships as $teamMembership) {
            $endSemester = $teamMembership->getEndSemester();
            if ($endSemester) {
                if ($endSemester->getEndDate() <= $currentSemesterStartDate) {
                    $teamMembership->setIsSuspended(true);
                    $this->dispatcher->dispatch(TeamMembershipEvent::EXPIRED, new TeamMembershipEvent($teamMembership));
                }
            }
        }
        $this->em->flush();

        return $teamMemberships;
    }
}
