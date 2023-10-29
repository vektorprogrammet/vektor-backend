<?php

namespace App\Core\Application\DTO;

use App\Core\Domain\Entity\AdmissionPeriod;

class AdmissionPeriodDTO implements \JsonSerializable
{
    private ?int $id = null;
    private ?string $department = null;
    private ?string $startDate = null;
    private ?string $endDate = null;
    private ?string $infoMeeting = null;
    private ?string $semester = null;

    public function __construct(?int $id, ?string $department, ?string $startDate, ?string $endDate, ?string $infoMeeting, ?string $semester)
    {
        $this->id = $id;
        $this->department = $department;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->infoMeeting = $infoMeeting;
        $this->semester = $semester;
    }

    public static function createFromEntity(AdmissionPeriod $admissionPeriod): AdmissionPeriodDTO
    {
        return new AdmissionPeriodDTO(
            $admissionPeriod->getId(),
            $admissionPeriod->getDepartment()->getName(),
            $admissionPeriod->getStartDate()->format('Y-m-d'),
            $admissionPeriod->getEndDate()->format('Y-m-d'),
            $admissionPeriod->getInfoMeeting(),
            $admissionPeriod->getSemester()->getName()
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function getInfoMeeting(): ?string
    {
        return $this->infoMeeting;
    }

    public function getSemester(): ?string
    {
        return $this->semester;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'department' => $this->department,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'infoMeeting' => $this->infoMeeting,
            'semester' => $this->semester,
        ];
    }
}
