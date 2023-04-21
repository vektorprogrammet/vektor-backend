<?php

namespace App\Tests\Entity;

use App\Entity\Department;
use App\Entity\Semester;
use App\Entity\Team;
use App\Entity\TeamInterest;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class TeamInterestEntityUnitTest extends TestCase
{
    private TeamInterest $teamInterest;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamInterest = new TeamInterest();
    }

    public function testSetName()
    {
        $this->teamInterest->setName('test');
        $this->assertEquals('test', $this->teamInterest->getName());
    }

    public function testSetEmail()
    {
        $this->teamInterest->setEmail('test@test.com');
        $this->assertEquals('test@test.com', $this->teamInterest->getEmail());
    }

    public function testSetPotentialTeams()
    {
        $teams_arr = new ArrayCollection();
        $team1 = new Team();
        $team2 = new Team();
        $teams_arr->add($team1);
        $teams_arr->add($team2);

        $this->teamInterest->setPotentialTeams($team1);
        $this->teamInterest->setPotentialTeams($team2);
        $this->assertEquals($teams_arr, $this->teamInterest->getPotentialTeams());
    }

    public function testSetSemester()
    {
        $semester = new Semester();
        $this->teamInterest->setSemester($semester);
        $this->assertEquals($semester, $this->teamInterest->getSemester());
    }

    public function testSetDepartment()
    {
        $department = new Department();
        $this->teamInterest->setDepartment($department);
        $this->assertEquals($department, $this->teamInterest->getDepartment());
    }
}
