<?php

namespace Tests\Core\Application\Util;

use App\Core\Application\Util\SemesterUtil;
use App\Core\Domain\Entity\Semester;
use PHPUnit\Framework\TestCase;
use DateTime;


class SemesterUtilTest extends TestCase
{

    public function testTimeToSemester(): void
    {
        $semester = SemesterUtil::timeToSemester(new DateTime('2020-09-01'));
        $actualSemester = new Semester();
        $actualSemester->setSemesterTime('Høst');
        $actualSemester->setYear('2020');
        $this->assertEquals($actualSemester, $semester);

        $semester = SemesterUtil::timeToSemester(new DateTime('2020-12-31'));
        $actualSemester = new Semester();
        $actualSemester->setSemesterTime('Høst');
        $actualSemester->setYear('2020');
        $this->assertEquals($actualSemester, $semester);

        $semester = SemesterUtil::timeToSemester(new DateTime('2021-01-01'));
        $actualSemester = new Semester();
        $actualSemester->setSemesterTime('Vår');
        $actualSemester->setYear('2021');
        $this->assertEquals($actualSemester, $semester);

        $semester = SemesterUtil::timeToSemester(new DateTime('2021-08-31'));
        $actualSemester = new Semester();
        $actualSemester->setSemesterTime('Høst');
        $actualSemester->setYear('2021');
        $this->assertEquals($actualSemester, $semester);

        $semester = SemesterUtil::timeToSemester(new \DateTime('2021-09-01'));
        $actualSemester = new Semester();
        $actualSemester->setSemesterTime('Høst');
        $actualSemester->setYear('2021');
        $this->assertEquals($actualSemester, $semester);

        $semester = SemesterUtil::timeToSemester(new \DateTime('2021-12-31'));
        $actualSemester = new Semester();
        $actualSemester->setSemesterTime('Høst');
        $actualSemester->setYear('2021');
        $this->assertEquals($actualSemester, $semester);
    }
}