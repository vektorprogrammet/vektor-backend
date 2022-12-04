<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Role\Roles;
use App\Service\RoleManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoleManagerTest extends KernelTestCase
{
    private RoleManager $roleManager;
    private EntityManagerInterface $em;
    /**
     * @var RoleUserMock[]
     */
    private $mockUsers;

    protected function setUp() : void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->em = $container->get('doctrine')->getManager();
        $this->roleManager = $container->get(RoleManager::class);

        $this->mockUsers = [
            // Assistants
            new RoleUserMock('assistant@gmail.com', Roles::ASSISTANT, Roles::ASSISTANT),
            new RoleUserMock('aai@b.c', Roles::TEAM_MEMBER, Roles::ASSISTANT),
            new RoleUserMock('teamleader@gmail.com', Roles::TEAM_LEADER, Roles::ASSISTANT),

            // Team members
            new RoleUserMock('kristoffer@stud.ntnu.no', Roles::ASSISTANT, Roles::TEAM_MEMBER),
            new RoleUserMock('alm@mail.com', Roles::TEAM_MEMBER, Roles::TEAM_MEMBER),
            new RoleUserMock('aah@b.c', Roles::TEAM_LEADER, Roles::TEAM_MEMBER),

            // Team leaders
            new RoleUserMock('seip@mail.com', Roles::ASSISTANT, Roles::TEAM_LEADER),
            new RoleUserMock('ida@stud.ntnu.no', Roles::TEAM_MEMBER, Roles::TEAM_LEADER),
            new RoleUserMock('anna@mail.no', Roles::TEAM_LEADER, Roles::TEAM_LEADER),

            // Executive board members
            new RoleUserMock('angela@mail.no', Roles::ASSISTANT, Roles::TEAM_LEADER),
            new RoleUserMock('aaf@b.c', Roles::TEAM_MEMBER, Roles::TEAM_LEADER),
            new RoleUserMock('jan-per-gustavio@gmail.com', Roles::TEAM_LEADER, Roles::TEAM_LEADER),

            // Admins
            new RoleUserMock('admin@gmail.com', Roles::ADMIN, Roles::ADMIN),
            new RoleUserMock('petter@stud.ntnu.no', Roles::ADMIN, Roles::ADMIN),
        ];
    }

    public function testUserRolesBeforeExecution()
    {
        foreach ($this->mockUsers as $user) {
            $this->assertThatUserWithEmailHasRole($user->getEmail(), $user->getRoleBeforeExecution());
        }
    }

    public function testUpdateRole()
    {
        $this->updateAllUserRoles();

        foreach ($this->mockUsers as $user) {
            $this->assertThatUserWithEmailHasRole($user->getEmail(), $user->getRoleAfterExecution());
        }
    }

    private function updateAllUserRoles()
    {
        foreach ($this->mockUsers as $mockUser) {
            $user = $this->em->getRepository(User::class)->findUserByEmail($mockUser->getEmail());
            $this->roleManager->updateUserRole($user);
        }
    }

    private function assertThatUserWithEmailHasRole(string $email, string $role)
    {
        $user = $this->em->getRepository(User::class)->findUserByEmail($email);
        $this->assertEquals($role, current($user->getRoles()));
    }
}

class RoleUserMock
{
    private $email;
    private $roleBeforeExecution;
    private $roleAfterExecution;

    /**
     * @param $email
     * @param $roleBeforeExecution
     * @param $roleAfterExecution
     */
    public function __construct($email, $roleBeforeExecution, $roleAfterExecution)
    {
        $this->email = $email;
        $this->roleBeforeExecution = $roleBeforeExecution;
        $this->roleAfterExecution = $roleAfterExecution;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getRoleBeforeExecution()
    {
        return $this->roleBeforeExecution;
    }

    /**
     * @return string
     */
    public function getRoleAfterExecution()
    {
        return $this->roleAfterExecution;
    }
}
