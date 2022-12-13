<?php

namespace App\Role;

class Roles
{
    public const ASSISTANT = 'ROLE_USER';
    public const TEAM_MEMBER = 'ROLE_TEAM_MEMBER';
    public const TEAM_LEADER = 'ROLE_TEAM_LEADER';
    public const ADMIN = 'ROLE_ADMIN';

    public const ALIAS_ASSISTANT = 'assistant';
    public const ALIAS_TEAM_MEMBER = 'team_member';
    public const ALIAS_TEAM_LEADER = 'team_leader';
    public const ALIAS_ADMIN = 'admin';

    public static function GetRoleName($role)
    {
        $roleName = '';
        if (self::ASSISTANT === $role) {
            $roleName = 'Bruker';
        }
        if (self::TEAM_MEMBER === $role) {
            $roleName = 'Teammedlem';
        }
        if (self::TEAM_LEADER === $role) {
            $roleName = 'Teamleder';
        }
        if (self::ADMIN === $role) {
            $roleName = 'Admin';
        }

        return $roleName;
    }
}
