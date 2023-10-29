<?php

namespace Tests\Core\Application\Util;

use App\Core\Application\Util\SemesterUtil;
use App\Core\Domain\Entity\Semester;
use PHPUnit\Framework\TestCase;


class SemesterUtilTest extends TestCase
{

    public function testTimeToSemester(): void
    {
        $semester = SemesterUtil::timeToSemester(new \DateTime('2020-09-01'));
        $this->assertEquals(new Semester(1, 2020, 1), $semester);

        $semester = SemesterUtil::timeToSemester(new \DateTime('2020-12-31'));
        $this->assertEquals(new Semester(2, 2020, 2), $semester);

        $semester = SemesterUtil::timeToSemester(new \DateTime('2021-01-01'));
        $this->assertEquals(new Semester(3, 2021, 1), $semester);

        $semester = SemesterUtil::timeToSemester(new \DateTime('2021-08-31'));
        $this->assertEquals(new Semester(4, 2021, 1), $semester);

        $semester = SemesterUtil::timeToSemester(new \DateTime('2021-09-01'));
        $this->assertEquals(new Semester(5, 2021, 2), $semester);

        $semester = SemesterUtil::timeToSemester(new \DateTime('2021-12-31'));
        $this->assertEquals(new Semester(6, 2021, 2), $semester);
    }
}