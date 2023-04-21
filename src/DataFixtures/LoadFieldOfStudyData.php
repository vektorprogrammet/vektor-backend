<?php

namespace App\DataFixtures;

use App\Entity\FieldOfStudy;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadFieldOfStudyData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        //  [reference, fos_name, fos_short_name, department_reference]
        $fieldOfStudyData = [
            ['fos-1', 'Bachelor i informatikk',               'BIT',  'dep-1'],
            ['fos-2', 'Datateknologi',                        'MIDT', 'dep-1'],
            ['fos-3', 'Bachelor i økonomi og administrasjon', 'BITA', 'dep-2'],
            ['fos-4', 'Miljøfysikk og fornybar energi',       'MFE',  'dep-3'],
            ['fos-5', 'Matematikk og økonomi (bachelor)',     'MAEC', 'dep-4'],
        ];

        // Create field-of-studies from array
        foreach ($fieldOfStudyData as $data) {
            $fos = new FieldOfStudy();
            $fos->setName($data[1]);
            $fos->setShortName($data[2]);
            $fos->setDepartment($this->getReference($data[3]));
            $manager->persist($fos);
            $this->addReference($data[0], $fos);
        }

        $manager->flush();

    }

    public function getOrder(): int
    {
        return 3;
    }
}
