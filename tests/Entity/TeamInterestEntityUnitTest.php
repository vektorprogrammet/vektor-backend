<?php

namespace App\Tests\Entity;

use App\Entity\Department;
use App\Entity\Semester;
use App\Entity\Team;
use App\Entity\TeamInterest;
use PHPUnit\Framework\TestCase;

class TeamInterestEntityUnitTest extends TestCase
{
    /**
     * @var TeamInterest
     */
    private $teamInterest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamInterest = new TeamInterest();
    }

    public function testSetName()
    {
        $this->teamInterest->setName('test');
        $this->assertSame('test', $this->teamInterest->getName());
    }

    public function testSetEmail()
    {
        $this->teamInterest->setEmail('test@test.com');
        $this->assertSame('test@test.com', $this->teamInterest->getEmail());
    }

    public function testSetPotentialTeams()
    {
        $teams = [new Team(), new Team()];
        $this->teamInterest->setPotentialTeams($teams);
        $this->assertSame($teams, $this->teamInterest->getPotentialTeams());
    }

    public function testSetSemester()
    {
        $semester = new Semester();
        $this->teamInterest->setSemester($semester);
        $this->assertSame($semester, $this->teamInterest->getSemester());
    }

    public function testSetDepartment()
    {
        $department = new Department();
        $this->teamInterest->setDepartment($department);
        $this->assertSame($department, $this->teamInterest->getDepartment());
    }
}
