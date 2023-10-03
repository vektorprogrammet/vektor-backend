<?php

namespace App\Core\Application\UseCase\Interfaces\Persistence;

use App\Core\Domain\Entity\Semester;
use DateTime;

interface ISemesterRepository
{
    public function findById(int $id): ?Semester;
    public function findAll();
    public function findSemesterByDate(DateTime $date): ?Semester;
    public function save(Semester $semester): void;
    public function delete(Semester $semester): void;
}