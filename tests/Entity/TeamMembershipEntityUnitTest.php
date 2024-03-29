<?php

namespace App\Tests\Entity;

use App\Entity\Position;
use App\Entity\Team;
use App\Entity\TeamMembership;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TeamMembershipEntityUnitTest extends TestCase
{
    // Check whether the setUser function is working correctly
    public function testSetUser()
    {
        // new entity
        $wh = new TeamMembership();

        // dummy entity
        $user = new User();
        $user->setFirstName('Per');

        // Use the setUser method
        $wh->setUser($user);

        // Assert the result
        $this->assertEquals($user, $wh->getUser());
    }

    // Check whether the setTeam function is working correctly
    public function testSetTeam()
    {
        // new entity
        $wh = new TeamMembership();

        // dummy entity
        $team = new Team();
        $team->setName('Hovedstyret');

        // Use the setTeam method
        $wh->setTeam($team);

        // Assert the result
        $this->assertEquals($team, $wh->getTeam());
    }

    // Check whether the setPosition function is working correctly
    public function testSetPosition()
    {
        // new entity
        $wh = new TeamMembership();

        // dummy entity
        $position = new Position();
        $position->setName('Superleder');

        // Use the setPosition method
        $wh->setPosition($position);

        // Assert the result
        $this->assertEquals($position, $wh->getPosition());
    }
}
