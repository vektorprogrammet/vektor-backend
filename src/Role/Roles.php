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

    public static function GetRoleName($role): string
    {
        $roleName = "";
        if ($role === Roles::ASSISTANT) {
            $roleName = "Bruker";
        }
        if ($role === Roles::TEAM_MEMBER) {
            $roleName = "Teammedlem";
        }
        if ($role === Roles::TEAM_LEADER) {
            $roleName = "Teamleder";
        }
        if ($role === Roles::ADMIN) {
            $roleName = "Admin";
        }
        return $roleName;
    }
}
