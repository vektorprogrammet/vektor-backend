<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Role\Roles;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private ?ContainerInterface $container = null;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        // reference, active, email, company_email, firstname, lastname, gender, phone, username, password, role, fos, picture_path, account_number

        $user_array = [
            ['user1',  '1', 'petter@stud.ntnu.no',     'petter@vektorprogrammet.no', 'Petter',      'Johansen',             '0', '95347865', 'petjo',    '1234', ROLES::ADMIN,       'fos-1', 'images/profile1.jpg',       '1234.56.78903'],
            ['user2',  '1', 'ida@stud.ntnu.no',        'vek2@vektorprogrammet.no',   'Ida',         'Andreassen',           '1', '95267841', 'idaan',    '1234', ROLES::TEAM_MEMBER, 'fos-2', 'images/profile2.jpg',       '1234.56.78903'],
            ['user3',  '1', 'kristoffer@stud.ntnu.no', 'vek3@vektorprogrammet.no',   'Kristoffer',  'Bø',                   '0', '95148725', 'kribo',    '1234', ROLES::ASSISTANT,   'fos-3', 'images/profile3.jpg',       '1234.56.78903'],
            ['user4',  '1', 'alm@mail.com',            'vek4@vektorprogrammet.no',   'Thomas',      'Alm',                  '0', '12312312', 'thomas',   '1234', ROLES::TEAM_MEMBER, 'fos-1', 'images/profile4.jpg',       '1234.56.78903'],
            ['user5',  '1', 'a@b.c',                   'vek5@vektorprogrammet.no',   'Reidun',      'Persdatter Ødegaard',  '1', '92269548', 'reidun',   '1234', ROLES::ADMIN,       'fos-1', 'images/profile5.jpg',       '1234.56.78903'],
            ['user6',  '1', 'b@b.c',                   'vek6@vektorprogrammet.no',   'Siri',        'Brenna Eskeland',      '1', '99540025', 'siri',     '1234', ROLES::ADMIN,       'fos-1', 'images/defaultProfile.png', '1234.56.78903'],
            ['user7',  '1', 'c@b.c',                   'vek7@vektorprogrammet.no',   'Eirik',       'Myrvoll-Nilsen',       '0', '93093824', 'eirik',    '1234', ROLES::TEAM_LEADER, 'fos-1', 'images/defaultProfile.png', '1234.56.78903'],
            ['user8',  '1', 'd@b.c',                   'vek8@vektorprogrammet.no',   'Ruben',       'Ravnå',                '0', '98059155', 'ruben',    '1234', ROLES::ADMIN,       'fos-1', 'images/defaultProfile.png', '1234.56.78903'],
            ['user9',  '1', 'e@b.c',                   'vek9@vektorprogrammet.no',   'Liv',         'Rasdal Håland',        '1', '45506381', 'liv',      '1234', ROLES::TEAM_LEADER, 'fos-1', 'images/defaultProfile.png', '1234.56.78903'],
            ['user10', '1', 'f@b.c',                   'vek10@vektorprogrammet.no',  'Johannes',    'Bogen',                '0', '95480124', 'johannes', '1234', ROLES::TEAM_LEADER, 'fos-1', 'images/defaultProfile.png', '1234.56.78903'],
            ['user11', '1', 'g@b.c',                   'vek11@vektorprogrammet.no',  'Cecilie',     'Teisberg',             '1', '45688060', 'cecilie',  '1234', ROLES::TEAM_LEADER, 'fos-1', 'images/defaultProfile.png', '1234.56.78903'],
            ['user12', '1', 'h@b.c',                   'vek12@vektorprogrammet.no',  'Håkon',       'Nøstvik',              '0', '99413718', 'haakon',   '1234', ROLES::TEAM_LEADER, 'fos-1', 'images/defaultProfile.png', '1234.56.78903'],
            ['user13', '1', 'i@b.c',                   'vek13@vektorprogrammet.no',  'Maulisha',    'Thavarajan',           '1', '45439367', 'maulisha', '1234', ROLES::TEAM_LEADER, 'fos-4', 'images/defaultProfile.png', '1234.56.78903'],
            ['user14', '1', 'ij@b.c',                  'vek14@vektorprogrammet.no',  'Åse',         'Thavarajan',           '1', '45439369', 'aase',     '1234', ROLES::TEAM_LEADER, 'fos-4', 'images/defaultProfile.png', '1234.56.78903'],
        ];

        // Create users from array
        foreach ($user_array as $item) {
            $user = new User();
            $user->setActive($item[1]);
            $user->setEmail($item[2]);
            $user->setCompanyEmail($item[3]);
            $user->setFirstName($item[4]);
            $user->setLastName($item[5]);
            $user->setGender($item[6]);
            $user->setPhone($item[7]);
            $user->setUserName($item[8]);
            $user->setPassword($item[9]);
            $user->addRole($item[10]);
            $user->setFieldOfStudy($this->getReference($item[11]));
            $user->setPicturePath($item[12]);
            $user->setAccountNumber($item[13]);
            $manager->persist($user);
            $this->addReference($item[0], $user);

        }


        $userInTeam1 = new User();
        $userInTeam1->setActive('1');
        $userInTeam1->setEmail('sortland@mail.com');
        $userInTeam1->setFirstName('Sondre');
        $userInTeam1->setLastName('Sortland');
        $userInTeam1->setGender('0');
        $userInTeam1->setPhone('12312312');
        $userInTeam1->setUserName('userInTeam1');
        $userInTeam1->setPassword('1234');
        $userInTeam1->addRole(Roles::TEAM_MEMBER);
        $userInTeam1->setFieldOfStudy($this->getReference('fos-1'));
        $userInTeam1->setPicturePath('images/sondre.jpg');
        $manager->persist($userInTeam1);

        $user = new User();
        $user->setActive('1');
        $user->setEmail('marte@mail.no');
        $user->setFirstName('Marte');
        $user->setLastName('Saghagen');
        $user->setGender('1');
        $user->setPhone('97623818');
        $user->setUserName('marte');
        $user->setPassword('123');
        $user->addRole(Roles::TEAM_MEMBER);
        $user->setFieldOfStudy($this->getReference('fos-1'));
        $user->setPicturePath('images/profile6.jpg');
        $manager->persist($user);
        $this->setReference('user-marte', $user);

        $user = new User();
        $user->setActive('1');
        $user->setEmail('anna@mail.no');
        $user->setFirstName('Anna');
        $user->setLastName('Madeleine Goldsack');
        $user->setGender('1');
        $user->setPhone('98896056');
        $user->setUserName('anna');
        $user->setPassword('123');
        $user->addRole(Roles::TEAM_LEADER);
        $user->setFieldOfStudy($this->getReference('fos-1'));
        $user->setPicturePath('images/profile7.jpg');
        $manager->persist($user);
        $this->setReference('user-anna', $user);

        $user = new User();
        $user->setActive('1');
        $user->setEmail('angela@mail.no');
        $user->setFirstName('Angela');
        $user->setLastName('Maiken Johnsen');
        $user->setGender('1');
        $user->setPhone('91152489');
        $user->setUserName('angela');
        $user->setPassword('123');
        $user->addRole(Roles::ASSISTANT);
        $user->setFieldOfStudy($this->getReference('fos-1'));
        $user->setPicturePath('images/defaultProfile.png');
        $manager->persist($user);
        $this->setReference('user-angela', $user);

        $user = new User();
        $user->setActive('0');
        $user->setEmail('inactive@mail.com');
        $user->setFirstName('Ina');
        $user->setLastName('Ktiv');
        $user->setGender('1');
        $user->setPhone('40404040');
        $user->setUserName('inactive');
        $user->setPassword('123');
        $user->addRole(Roles::ASSISTANT);
        $user->setFieldOfStudy($this->getReference('fos-1'));
        $user->setPicturePath('images/defaultProfile.png');
        $manager->persist($user);
        $this->setReference('user-inactive', $user);

        $user10 = new User();
        $user10->setActive('1');
        $user10->setEmail('aaf@b.c');
        $user10->setFirstName('Kamilla');
        $user10->setLastName('Plaszko');
        $user10->setGender('1');
        $user10->setPhone('45484008');
        $user10->setUserName('kampla');
        $user10->setPassword('123');
        $user10->addRole(Roles::TEAM_MEMBER);
        $user10->setFieldOfStudy($this->getReference('fos-5'));
        $user10->setPicturePath('images/defaultProfile.png');
        $manager->persist($user10);

        $user11 = new User();
        $user11->setActive('1');
        $user11->setEmail('aag@b.c');
        $user11->setFirstName('Vuk');
        $user11->setLastName('Krivokapic');
        $user11->setGender('0');
        $user11->setPhone('47000000');
        $user11->setUserName('vuk');
        $user11->setPassword('123');
        $user11->addRole(Roles::TEAM_LEADER);
        $user11->setFieldOfStudy($this->getReference('fos-3'));
        $user11->setPicturePath('images/defaultProfile.png');
        $manager->persist($user11);

        $user12 = new User();
        $user12->setActive('1');
        $user12->setEmail('aah@b.c');
        $user12->setFirstName('Markus');
        $user12->setLastName('Gundersen');
        $user12->setGender('0');
        $user12->setPhone('46000000');
        $user12->setUserName('markus');
        $user12->setPassword('123');
        $user12->addRole(Roles::TEAM_LEADER);
        $user12->setFieldOfStudy($this->getReference('fos-1'));
        $user12->setPicturePath('images/defaultProfile.png');
        $manager->persist($user12);

        $user13 = new User();
        $user13->setActive('1');
        $user13->setEmail('aai@b.c');
        $user13->setFirstName('Erik');
        $user13->setLastName('Trondsen ');
        $user13->setGender('0');
        $user13->setPhone('45000000');
        $user13->setUserName('erik');
        $user13->addRole(Roles::TEAM_MEMBER);
        $user13->setFieldOfStudy($this->getReference('fos-1'));
        $user13->setPicturePath('images/defaultProfile.png');
        $manager->persist($user13);

        $userAssistant = new User();
        $userAssistant->setActive('1');
        $userAssistant->setEmail('assistant@gmail.com');
        $userAssistant->setFirstName('Assistent');
        $userAssistant->setLastName('Johansen');
        $userAssistant->setGender('0');
        $userAssistant->setPhone('47658937');
        $userAssistant->setUserName('assistent');
        $userAssistant->setPassword('1234');
        $userAssistant->addRole(Roles::ASSISTANT);
        $userAssistant->setFieldOfStudy($this->getReference('fos-1'));
        $userAssistant->setPicturePath('images/defaultProfile.png');
        $userAssistant->setAccountNumber('1234.56.78903');
        $manager->persist($userAssistant);

        $userTeamMember = new User();
        $userTeamMember->setActive('1');
        $userTeamMember->setEmail('team@gmail.com');
        $userTeamMember->setFirstName('Team');
        $userTeamMember->setLastName('Johansen');
        $userTeamMember->setGender('0');
        $userTeamMember->setPhone('47658937');
        $userTeamMember->setUserName('teammember');
        $userTeamMember->setPassword('1234');
        $userTeamMember->addRole(Roles::TEAM_MEMBER);
        $userTeamMember->setFieldOfStudy($this->getReference('fos-1'));
        $userTeamMember->setPicturePath('images/defaultProfile.png');
        $userTeamMember->setAccountNumber('1234.56.78903');
        $manager->persist($userTeamMember);

        $user16 = new User();
        $user16->setActive('1');
        $user16->setEmail('nmbu@admin.no');
        $user16->setFirstName('Muhammed');
        $user16->setLastName('Thavarajan');
        $user16->setGender('1');
        $user16->setPhone('45439367');
        $user16->setUserName('nmbu');
        $user16->setPassword('1234');
        $user16->addRole(Roles::ADMIN);
        $user16->setFieldOfStudy($this->getReference('fos-4'));
        $user16->setPicturePath('images/defaultProfile.png');
        $manager->persist($user16);

        $userTeamLeader = new User();
        $userTeamLeader->setActive('1');
        $userTeamLeader->setEmail('teamleader@gmail.com');
        $userTeamLeader->setFirstName('TeamLeader');
        $userTeamLeader->setLastName('Johansen');
        $userTeamLeader->setGender('0');
        $userTeamLeader->setPhone('47658937');
        $userTeamLeader->setUserName('teamleader');
        $userTeamLeader->setPassword('1234');
        $userTeamLeader->addRole(Roles::TEAM_LEADER);
        $userTeamLeader->setFieldOfStudy($this->getReference('fos-1'));
        $userTeamLeader->setPicturePath('images/harold.jpg');
        $userTeamLeader->setAccountNumber('1234.56.78903');
        $manager->persist($userTeamLeader);

        $userAdmin = new User();
        $userAdmin->setActive('1');
        $userAdmin->setEmail('admin@gmail.com');
        $userAdmin->setFirstName('Admin');
        $userAdmin->setLastName('Johansen');
        $userAdmin->setGender('0');
        $userAdmin->setPhone('47658937');
        $userAdmin->setUserName('admin');
        $userAdmin->setPassword('1234');
        $userAdmin->addRole('ROLE_ADMIN');
        $userAdmin->setFieldOfStudy($this->getReference('fos-1'));
        $userAdmin->setPicturePath('images/harold.jpg');
        $userAdmin->setAccountNumber('1234.56.78903');
        $manager->persist($userAdmin);

        $user20 = new User();
        $user20->setActive('1');
        $user20->setEmail('jan-per-gustavio@gmail.com');
        $user20->setFirstName('Jan-Per-Gustavio');
        $user20->setLastName('Tacopedia');
        $user20->setGender('0');
        $user20->setPhone('81549300');
        $user20->setUserName('JanPerGustavio');
        $user20->setPassword('1234');
        $user20->addRole(Roles::TEAM_LEADER);
        $user20->setFieldOfStudy($this->getReference('fos-3'));
        $user20->setPicturePath('images/defaultProfile.png');
        $manager->persist($user20);

        $user21 = new User();
        $user21->setActive('1');
        $user21->setEmail('seip@mail.com');
        $user21->setFirstName('Ingrid');
        $user21->setLastName('Seip Domben');
        $user21->setGender('1');
        $user21->setPhone('91104644');
        $user21->setUserName('ingrid');
        $user21->setPassword('123');
        $user21->addRole(Roles::ASSISTANT);
        $user21->setFieldOfStudy($this->getReference('fos-1'));
        $user21->setPicturePath('images/defaultProfile.png');
        $manager->persist($user21);

        for ($i = 0; $i < 100; ++$i) {
            $user = new User();
            $user->setActive('0');
            $user->setEmail('scheduling-user-' . $i . '@mail.com');
            $user->setFirstName('scheduling-user-' . $i);
            $user->setLastName('user-lastName-' . $i);
            $user->setGender($i % 2 === 0 ? '0' : '1');
            $user->setPhone('12345678');
            $user->setUserName('scheduling-user-' . $i);
            $user->addRole(Roles::ASSISTANT);
            $user->setFieldOfStudy($this->getReference('fos-1'));
            $user->setPicturePath('images/defaultProfile.png');
            $this->setReference('scheduling-user-' . $i, $user);
            $manager->persist($user);
        }

        $manager->flush();

        $this->setReference('user-1', $user1);
        $this->setReference('user-2', $user2);
        $this->setReference('user-3', $user3);
        $this->setReference('user-4', $user4);
        $this->setReference('user-8', $user8);
        $this->setReference('user-9', $user9);
        $this->setReference('user-10', $user10);
        $this->setReference('user-11', $user11);
        $this->setReference('user-12', $user12);
        $this->setReference('user-13', $user13);
        $this->setReference('user-14', $user14);
        $this->setReference('user-16', $user16);
        $this->setReference('user-20', $user20);
        $this->setReference('user-21', $user21);
        $this->setReference('userInTeam1', $userInTeam1);
        $this->setReference('user-assistant', $userAssistant);
        $this->setReference('user-team-member', $userTeamMember);
        $this->setReference('user-team-leader', $userTeamLeader);
        $this->setReference('user-admin', $userAdmin);
    }

    public function getOrder(): int
    {
        return 4;
    }
}
