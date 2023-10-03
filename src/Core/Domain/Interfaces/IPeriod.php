<?php

namespace App\Core\Domain\Interfaces;

interface IPeriod
{
    public function getStartDate(): ?\DateTime;

    public function getEndDate(): ?\Datetime;
}
