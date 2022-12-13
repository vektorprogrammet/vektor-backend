<?php

namespace App\Utils;

use App\Entity\Semester;
use DateTime;

class SemesterUtil
{
    /**
     *
     */
    public static function timeToYear(DateTime $time): string
    {
        return $time->format('Y');
    }

    /**
     *
     */
    public static function timeToSemesterTime(DateTime $time): string
    {
        return $time->format('m') <= 7 ? 'Vår' : 'Høst';
    }

    /**
     */
    public static function timeToSemester(Datetime $time): Semester
    {
        $semester = new Semester();
        $semester->setYear(self::timeToYear($time));
        $semester->setSemesterTime(self::timeToSemesterTime($time));
        return $semester;
    }
}
