<?php

namespace App\Core\Application\Service;

use App\Core\Domain\Entity\User;

class AuthorizationService
{
    private static $rolesWithPermissions = [
        'ROLE_ANONYMOUS' => [], // This role is used for unauthenticated users
        'ROLE_USER' => [],
        'ROLE_TEAMLEADER' => ['Department::Create'],
        'ROLE_ADMIN' => ['Department::Create', 'Department::Update', 'Department::Delete'],
    ];

    // This method checks if a user has a specific permission
    public static function userHasPermission(?User $user, string $permissionToCheck): bool
    {
        if ($user === null) {
            // User is not authenticated
            return self::roleHasPermission('ROLE_ANONYMOUS', $permissionToCheck);
        }
        // Get the roles associated with the user
        $roles = $user->getRoles();

        // Check if any of the user's roles have the permission
        foreach ($roles as $role) {
            if (self::roleHasPermission($role, $permissionToCheck)) {
                return true; // User has the permission
            }
        }

        return false; // User does not have the permission
    }

    // This method checks if a role has a specific permission
    private static function roleHasPermission(string $role, string $permissionToCheck): bool
    {
        // Get the permissions associated with the role
        // Check if the role has the permission
        return in_array($permissionToCheck, self::$rolesWithPermissions[$role], true);
    }
}
