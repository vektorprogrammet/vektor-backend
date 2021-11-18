<?php

namespace App\Tests\AppBundle\Extension;

use App\Entity\AdmissionPeriod;
use App\Entity\Department;
use App\Entity\ExecutiveBoard;
use App\Entity\ExecutiveBoardMembership;
use App\Entity\Position;
use App\Entity\Semester;
use App\Entity\Team;
use App\Entity\TeamMembership;
use App\Entity\User;
use App\Service\FilterService;
use App\Service\Sorter;
use App\Twig\TeamPositionSortExtension;
use DateTime;
use PHPUnit\Framework\TestCase;

class TeamPositionSortExtensionUnitTest extends TestCase
{
    private $sortExtension;
    private $activeSemester;
    private $latestAdmissionPeriod;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->sortExtension = new TeamPositionSortExtension(new Sorter(), new FilterService());

        $this->activeSemester = new Semester();
        $this->activeSemester
            ->setYear(2013)
            ->setSemesterTime('Vår');

        $this->latestAdmissionPeriod = new AdmissionPeriod();
        $this->latestAdmissionPeriod
            ->setSemester($this->activeSemester)
            ->setStartDate(new DateTime('2013-01-01'))
            ->setEndDate((new DateTime())->modify('+1day'));
    }

    public function testExecutiveMembers()
    {
        $users = array();
        $positions = ['Sekretær', 'Leder', '', 'Økonomi', 'Assistent', 'Medlem', 'Nestleder'];
        $board = new ExecutiveBoard();

        for ($x = 0; $x < 7; ++$x) {
            $user = new User();
            $membership = new ExecutiveBoardMembership();
            $membership->setPositionName($positions[$x])
                       ->setBoard($board)
                       ->setStartSemester($this->activeSemester);
            $user->setMemberships(array($membership));
            $users[] = $user;
        }

        $sortedMemberships = $this->sortExtension->teamPositionSortFilter($users, $board);
        $sortedPositions = ['Leder', 'Nestleder', 'Assistent', 'Medlem', 'Sekretær', 'Økonomi', ''];
        for ($x = 0; $x < 7; ++$x) {
            $this->assertEquals($sortedMemberships[$x]->getActiveExecutiveBoardMemberships()[0]->getPositionName(), $sortedPositions[$x]);
        }
    }

    public function testTeamMemberships()
    {
        $users = array();
        $positions = ['Sekretær', 'Leder', '', 'Økonomi', 'Assistent', 'Medlem', 'Nestleder'];
        $department = new Department();
        $department->addAdmissionPeriod($this->latestAdmissionPeriod);
        $team = new Team();
        $team->setDepartment($department);

        for ($x = 0; $x < 7; ++$x) {
            $user = new User();
            $membership = new TeamMembership();
            $position = new Position();
            $position->setName($positions[$x]);
            $membership->setPosition($position)
                ->setTeam($team)
                ->setStartSemester($this->activeSemester);
            $user->setMemberships(array($membership));
            $users[] = $user;
        }

        $sortedMemberships = $this->sortExtension->teamPositionSortFilter($users, $team);
        $sortedPositions = ['Leder', 'Nestleder', 'Assistent', 'Medlem', 'Sekretær', 'Økonomi', ''];
        for ($x = 0; $x < 7; ++$x) {
            $this->assertEquals($sortedMemberships[$x]->getActiveTeamMemberships()[0]->getPositionName(), $sortedPositions[$x]);
        }
    }
}