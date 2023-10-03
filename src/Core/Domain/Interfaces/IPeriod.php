<?php

namespace App\Core\Domain\Interfaces;

use DateTime;

interface IPeriod
{
    public function getStartDate(): ?DateTime;

    public function getEndDate(): ?Datetime;
}
