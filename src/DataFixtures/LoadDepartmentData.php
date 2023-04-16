<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadDepartmentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $department1 = new Department();
        $department1->setName('Norges teknisk-naturvitenskapelige universitet');
        $department1->setShortName('NTNU');
        $department1->setEmail('NTNU@mail.com');
        $department1->addSchool($this->getReference('school-1'));
        $department1->addSchool($this->getReference('school-4'));
        $department1->addSchool($this->getReference('school-00'));
        $department1->addSchool($this->getReference('school-01'));
        $department1->addSchool($this->getReference('school-02'));
        $department1->addSchool($this->getReference('school-03'));
        $department1->addSchool($this->getReference('school-04'));
        $department1->addSchool($this->getReference('school-05'));
        $department1->addSchool($this->getReference('school-06'));
        $department1->addSchool($this->getReference('school-07'));
        $department1->addSchool($this->getReference('school-08'));
        $department1->addSchool($this->getReference('school-09'));
        $department1->setAddress('Høgskoleringen 5 7491 Trondheim');
        $department1->setCity('Trondheim');
        $department1->setLatitude('63.416057');
        $department1->setLongitude('10.408514');
        $department1->setLogoPath('/images/department_images/ntnu.png');
        $manager->persist($department1);

        // Set school <=> department relation
        $this->getReference('school-0')->setDepartment($department1);
        $this->getReference('school-1')->setDepartment($department1);
        $this->getReference('school-00')->setDepartment($department1);
        $this->getReference('school-01')->setDepartment($department1);
        $this->getReference('school-02')->setDepartment($department1);
        $this->getReference('school-03')->setDepartment($department1);
        $this->getReference('school-04')->setDepartment($department1);
        $this->getReference('school-05')->setDepartment($department1);
        $this->getReference('school-06')->setDepartment($department1);
        $this->getReference('school-07')->setDepartment($department1);
        $this->getReference('school-08')->setDepartment($department1);
        $this->getReference('school-09')->setDepartment($department1);

        // DEPARTMENT 2
        $department2 = new Department();
        $department2->setName('Universitetet i Bergen');
        $department2->setShortName('UiB');
        $department2->setEmail('UiB@mail.com');
        $department2->addSchool($this->getReference('school-3'));
        $department2->setAddress('*UiB adresse*');
        $department2->setCity('Bergen');
        $department2->setLatitude('60.387639');
        $department2->setLongitude('5.321523');
        $department2->setLogoPath('/images/department_images/hist.png');
        $manager->persist($department2);

        // Set school <=> department relation
        $this->getReference('school-2')->setDepartment($department2);

        // DEPARTMENT 3
        $department3 = new Department();
        $department3->setName('Norges miljø- og biovitenskapelige universitet');
        $department3->setShortName('NMBU');
        $department3->setEmail('NMBU@mail.com');
        $department3->addSchool($this->getReference('school-1'));
        $department3->setAddress('*NMBU adresse*');
        $department3->setCity('Ås');
        $department3->setLatitude('59.666108');
        $department3->setLongitude('10.768452');
        $department3->setLogoPath('/images/department_images/nmbu.png');
        $manager->persist($department3);

        // Set school <=> department relation
        $this->getReference('school-3')->setDepartment($department3);

        // Department 4
        $department4 = new Department();
        $department4->setName('Universitetet i Oslo');
        $department4->setShortName('UiO');
        $department4->setEmail('UiO@mail.com');
        $department4->addSchool($this->getReference('school-2'));
        $department4->setAddress('*UiO adresse*');
        $department4->setCity('Oslo');
        $department4->setLatitude('59.939942');
        $department4->setLongitude('10.721170');
        $department4->setLogoPath('/images/department_images/uio.png');
        $manager->persist($department4);

        // Set school <=> department relation
        $this->getReference('school-4')->setDepartment($department4);

        $manager->flush();

        $this->addReference('dep-1', $department1);
        $this->addReference('dep-2', $department2);
        $this->addReference('dep-3', $department3);
        $this->addReference('dep-4', $department4);
    }

    public function getOrder(): int
    {
        return 2;
    }
}
