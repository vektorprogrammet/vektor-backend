<?php

namespace App\DataFixtures;

use App\Entity\AdmissionSubscriber;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadAdmissionSubscriberData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 365; ++$i) {
            $date = new \DateTime("-$i days");
            for ($j = 0; $j < random_int(1, 10); ++$j) {
                $subscriber = new AdmissionSubscriber();
                $subscriber->setDepartment($this->getReference('dep-1'));
                $subscriber->setTimestamp($date);
                $subscriber->setEmail("sub$i.$j@vektorprogrammet.no");
                $manager->persist($subscriber);
            }
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 5;
    }
}
