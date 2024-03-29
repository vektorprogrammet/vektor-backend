<?php

namespace App\DataFixtures;

use App\Entity\ExecutiveBoardMembership;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadExecutiveBoardMembershipData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $boardMember = new ExecutiveBoardMembership();
        $boardMember->setBoard($this->getReference('board'));
        $boardMember->setPositionName('Leder');
        $boardMember->setUser($this->getReference('user-20'));
        $boardMember->setStartSemester($this->getReference('semester-1'));
        $boardMember->setEndSemester($this->getReference('semester-2'));
        $manager->persist($boardMember);

        $boardMember = new ExecutiveBoardMembership();
        $boardMember->setBoard($this->getReference('board'));
        $boardMember->setPositionName('Medlem');
        $boardMember->setUser($this->getReference('user-10'));
        $boardMember->setStartSemester($this->getReference('semester-3'));
        $manager->persist($boardMember);

        $boardMember = new ExecutiveBoardMembership();
        $boardMember->setBoard($this->getReference('board'));
        $boardMember->setPositionName('Medlem');
        $boardMember->setUser($this->getReference('user-angela'));
        $boardMember->setStartSemester($this->getReference('semester-previous'));
        $boardMember->setEndSemester($this->getReference('semester-current'));
        $manager->persist($boardMember);

        $boardMember = new ExecutiveBoardMembership();
        $boardMember->setBoard($this->getReference('board'));
        $boardMember->setPositionName('Medlem');
        $boardMember->setUser($this->getReference('userInTeam1'));
        $boardMember->setStartSemester($this->getReference('semester-previous'));
        $manager->persist($boardMember);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 5;
    }
}
