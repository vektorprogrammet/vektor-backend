<?php

namespace App\Entity;

use App\Repository\AdmissionPeriodRepository;
use App\Utils\TimeUtil;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DepartmentSpecificSemester.
 */
#[ORM\Table]
#[ORM\Entity(repositoryClass: AdmissionPeriodRepository::class)]
class AdmissionPeriod implements PeriodInterface
{
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'admissionPeriods')]
    private ?Department $department = null;

    #[ORM\Column(name: 'start_date', type: Types::DATETIME_MUTABLE, length: 150)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?\DateTime $startDate = null;

    #[ORM\Column(name: 'end_date', type: Types::DATETIME_MUTABLE, length: 150)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?\DateTime $endDate = null;

    #[ORM\OneToOne(targetEntity: InfoMeeting::class, cascade: ['remove', 'persist'])]
    #[Assert\Valid]
    private ?InfoMeeting $infoMeeting = null;

    #[ORM\ManyToOne(targetEntity: Semester::class, inversedBy: 'admissionPeriods')]
    private ?Semester $semester = null;

    public function __toString()
    {
        return $this->semester->getName() . ' - ' . $this->getDepartment();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDepartment(Department $department = null): self
    {
        $this->department = $department;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setStartDate(\DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setEndDate(\DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function getInfoMeeting(): ?InfoMeeting
    {
        return $this->infoMeeting;
    }

    public function setInfoMeeting(InfoMeeting $infoMeeting = null): void
    {
        $this->infoMeeting = $infoMeeting;
    }

    public function isActive(): bool
    {
        $now = new \DateTime();

        return $this->semester->getStartDate() < $now && $now <= $this->semester->getEndDate();
    }

    public function hasActiveAdmission(): bool
    {
        $now = new \DateTime();

        return $this->getStartDate() <= $now && $now <= $this->getEndDate();
    }

    public function getSemester(): ?Semester
    {
        return $this->semester;
    }

    public function setSemester(Semester $semester): self
    {
        $this->semester = $semester;

        return $this;
    }

    public function shouldSendInfoMeetingNotifications(): bool
    {
        return $this->infoMeeting !== null
            && $this->infoMeeting->getDate() !== null
            && $this->infoMeeting->isShowOnPage()
            && TimeUtil::dateTimeIsToday($this->infoMeeting->getDate())
            && TimeUtil::dateTimeIsInTheFuture($this->infoMeeting->getDate());
    }
}
