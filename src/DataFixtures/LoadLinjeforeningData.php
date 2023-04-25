<?php

namespace App\DataFixtures;

use App\Entity\Linjeforening;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadLinjeforeningData extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $linjeforening1 = new Linjeforening();
        $linjeforening1->setName("Abakus");
        $linjeforening1->setFOS($this->getReference('fos-2'));
        $linjeforening1->setContactPerson("Ole Jakob Mellgren");
        $manager->persist($linjeforening1);
        $this->addReference('linjeforening-1', $linjeforening1);

        $linjeforening2 = new Linjeforening();
        $linjeforening2->setName("Online");
        $linjeforening2->setFOS($this->getReference('fos-1'));
        $linjeforening2->setContactPerson("Erlend Golten Persen");
        $manager->persist($linjeforening2);
        $this->addReference('linjeforening-2', $linjeforening2);

        $manager->flush();
    }
    public function getOrder(): int
    {
        return 4;
    }
}
