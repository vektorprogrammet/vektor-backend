<?php

namespace App\Entity;

interface PeriodInterface
{
    public function getStartDate(): ?\DateTime;

    public function getEndDate(): ?\Datetime;
}
