<?php

namespace App\Role;

class Roles
{
    const ASSISTANT = 'ROLE_USER';
    const TEAM_MEMBER = 'ROLE_TEAM_MEMBER';
    const TEAM_LEADER = 'ROLE_TEAM_LEADER';
    const ADMIN = 'ROLE_ADMIN';

    const ALIAS_ASSISTANT = 'assistant';
    const ALIAS_TEAM_MEMBER = 'team_member';
    const ALIAS_TEAM_LEADER = 'team_leader';
    const ALIAS_ADMIN = 'admin';

    static function GetRoleName($role)
    {
        $roleName = "";
        if($role === Roles::ASSISTANT)
        {
            $roleName = "Bruker";
        }
        if($role === Roles::TEAM_MEMBER)
        {
            $roleName = "Teammedlem";
        }
        if($role === Roles::TEAM_LEADER)
        {
            $roleName = "Teamleder";
        }
        if($role === Roles::ADMIN)
        {
            $roleName = "Admin";
        }
        return $roleName;
    }
}
