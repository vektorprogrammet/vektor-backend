<?php

namespace App\DataFixtures;

use App\Entity\AccessRule;
use App\Role\Roles;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadAccessRuleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $rule = new AccessRule();
        $rule->setName("All departments");
        $rule->setResource("all_departments");
        $rule->setMethod('GET');
        $rule->setForExecutiveBoard(true);


        $manager->persist($rule);

        $rule = new AccessRule();
        $rule->setName("Survey Admin");
        $rule->setResource("survey_admin");
        $rule->setMethod('GET');
        $rule->setRoles(array(Roles::ADMIN));


        $manager->persist($rule);


        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
