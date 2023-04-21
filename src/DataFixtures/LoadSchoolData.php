<?php

namespace App\DataFixtures;

use App\Entity\School;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadSchoolData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        //  Array of schools to be created
        //  [reference, school_name, contact_person, email, phone, active, international]
        $schools_arr = [
            ['school-0', 'Blussuvoll', 'Kari Johansen',  'kari@mail.com',   '22386722', true, true],
            ['school-1', 'Gimse',      'Per Olsen',      'Per@mail.com',    '99887722', true, false],
            ['school-2', 'Selsbakk',   'Vibeke Hansen',  'Vibeke@mail.com', '22386722', true, true],
            ['school-3', 'Strinda',    'Peter Andersen', 'ped@mail.com',    '22386722', true, false],
            ['school-4', 'Katta',      'Jon Støvneng',   'jas@mail.com',    '13424567', false, false],
        ];

        // Create schools from array
        foreach ($schools_arr as $school) {
            $school_obj = new School();
            $school_obj->setName($school[1]);
            $school_obj->setContactPerson($school[2]);
            $school_obj->setEmail($school[3]);
            $school_obj->setPhone($school[4]);
            $school_obj->setActive($school[5]);
            $school_obj->setInternational($school[6]);
            $manager->persist($school_obj);
            $this->addReference($school[0], $school_obj);
        }

        // Create 10 schools with random names
        for ($i = 0; $i < 10; ++$i) {
            $school = new School();
            $school->setName('Skole ' . $i);
            $school->setContactPerson('Kontaktperson ' . $i);
            $school->setEmail('skole@mail.com');
            $school->setPhone('12345678');
            $school->setActive(true);
            $school->setInternational(false);
            $manager->persist($school);
            $this->addReference('school-0' . $i, $school);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
