<?php

namespace App\DataFixtures;

use App\Entity\SchoolCapacity;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadSchoolCapacityData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; ++$i) {
            $randomArr = [true, false, false, false, false];
            shuffle($randomArr);
            $schoolCapacity = new SchoolCapacity();
            $schoolCapacity->setSchool($this->getReference('school-0' . $i));
            $schoolCapacity->setSemester($this->getReference('semester-current'));
            $schoolCapacity->setDepartment($this->getReference('dep-1'));
            $schoolCapacity->setMonday($randomArr[0] || random_int(0, 100) < 30 ? 2 : 0);
            $schoolCapacity->setTuesday($randomArr[1] || random_int(0, 100) < 30 ? 2 : 0);
            $schoolCapacity->setWednesday($randomArr[2] || random_int(0, 100) < 30 ? 2 : 0);
            $schoolCapacity->setThursday($randomArr[3] || random_int(0, 100) < 30 ? 2 : 0);
            $schoolCapacity->setFriday($randomArr[4] || random_int(0, 100) < 30 ? 2 : 0);

            $manager->persist($schoolCapacity);
            $this->addReference('school-capacity-' . $i, $schoolCapacity);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 4;
    }
}
