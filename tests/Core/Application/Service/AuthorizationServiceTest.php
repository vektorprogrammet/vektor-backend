<?php

namespace Tests\Core\Application\Service;

use App\Core\Application\Service\AuthorizationService;
use App\Core\Domain\Entity\User;
use PHPUnit\Framework\TestCase;


class AuthorizationServiceTest extends TestCase {

    public function testTeamLeaderCanCreateDepartment(): void
    {
        $authorizationService = new AuthorizationService();
        $user = new User();
        $user->setRoles(['ROLE_TEAMLEADER']);

        $this->assertTrue($authorizationService->userHasPermission($user, 'Department::Create'));
    }

    public function testAdminInheritsTeamLeaderPermissions(): void
    {
        $authorizationService = new AuthorizationService();
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $this->assertTrue($authorizationService->userHasPermission($user, 'Department::Create'));
    }

    public function testUserCanNotCreateDepartment(): void
    {
        $authorizationService = new AuthorizationService();
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        $this->assertFalse($authorizationService->userHasPermission($user, 'Department::Create'));
    }
}