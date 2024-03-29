<?php

namespace App\Role;

class Roles
{
    final public const ASSISTANT = 'ROLE_USER';
    final public const TEAM_MEMBER = 'ROLE_TEAM_MEMBER';
    final public const TEAM_LEADER = 'ROLE_TEAM_LEADER';
    final public const ADMIN = 'ROLE_ADMIN';

    final public const ALIAS_ASSISTANT = 'assistant';
    final public const ALIAS_TEAM_MEMBER = 'team_member';
    final public const ALIAS_TEAM_LEADER = 'team_leader';
    final public const ALIAS_ADMIN = 'admin';

    public static function GetRoleName($role): string
    {
        $roleName = '';
        if ($role === Roles::ASSISTANT) {
            $roleName = 'Bruker';
        }
        if ($role === Roles::TEAM_MEMBER) {
            $roleName = 'Teammedlem';
        }
        if ($role === Roles::TEAM_LEADER) {
            $roleName = 'Teamleder';
        }
        if ($role === Roles::ADMIN) {
            $roleName = 'Admin';
        }

        return $roleName;
    }
}
