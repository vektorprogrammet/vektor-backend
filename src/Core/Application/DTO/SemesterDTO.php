<?php

namespace App\Core\Application\DTO;

use App\Core\Domain\Entity\Semester;
use JsonSerializable;

class SemesterDTO implements JsonSerializable
{
    private ?int $id = null;
    private ?string $semesterTime = null;
    private ?string $year = null;
    private ?string $name = null;
    private ?string $startDate = null;
    private ?string $endDate = null;

    public function __construct(?int $id, ?string $semesterTime, ?string $year, ?string $name, ?string $startDate, ?string $endDate)
    {
        $this->id = $id;
        $this->semesterTime = $semesterTime;
        $this->year = $year;
        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public static function createFromEntity(Semester $semester): SemesterDTO
    {
        return new SemesterDTO(
            $semester->getId(),
            $semester->getSemesterTime(),
            $semester->getYear(),
            $semester->getName(),
            $semester->getStartDate()->format('Y-m-d'),
            $semester->getEndDate()->format('Y-m-d')
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSemesterTime(): ?string
    {
        return $this->semesterTime;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'semesterTime' => $this->semesterTime,
            'year' => $this->year,
            'name' => $this->name,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ];
    }
}
