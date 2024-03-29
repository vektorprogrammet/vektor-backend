<?php

namespace App\DataFixtures;

use App\Entity\AssistantHistory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadAssistantHistoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $ah1 = new AssistantHistory();
        $ah1->setUser($this->getReference('user-1'));
        $ah1->setSchool($this->getReference('school-1'));
        $ah1->setSemester($this->getReference('semester-current'));
        $ah1->setDepartment($this->getReference('dep-1'));
        $ah1->setWorkdays(4);
        $ah1->setBolk('Bolk 2');
        $ah1->setDay('Onsdag');
        $manager->persist($ah1);

        $ah2 = new AssistantHistory();
        $ah2->setUser($this->getReference('user-assistant'));
        $ah2->setSchool($this->getReference('school-2'));
        $ah2->setSemester($this->getReference('semester-current'));
        $ah2->setDepartment($this->getReference('dep-1'));
        $ah2->setWorkdays(8);
        $ah2->setBolk('Bolk 1, Bolk 2');
        $ah2->setDay('Mandag');
        $manager->persist($ah2);

        $ah3 = new AssistantHistory();
        $ah3->setUser($this->getReference('user-assistant'));
        $ah3->setSchool($this->getReference('school-1'));
        $ah3->setSemester($this->getReference('semester-1'));
        $ah3->setDepartment($this->getReference('dep-1'));
        $ah3->setWorkdays(4);
        $ah3->setBolk('Bolk 1');
        $ah3->setDay('Fredag');
        $manager->persist($ah3);

        $ah4 = new AssistantHistory();
        $ah4->setUser($this->getReference('user-3'));
        $ah4->setSchool($this->getReference('school-3'));
        $ah4->setSemester($this->getReference('semester-2'));
        $ah4->setDepartment($this->getReference('dep-2'));
        $ah4->setWorkdays(4);
        $ah4->setBolk('Bolk 1');
        $ah4->setDay('Fredag');
        $manager->persist($ah4);

        $manager->flush();

        $this->addReference('ah-1', $ah1);
    }

    public function getOrder(): int
    {
        return 5;
    }
}
