<?php

namespace App\Entity;

use App\Repository\SemesterRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'semester')]
#[ORM\Entity(repositoryClass: SemesterRepository::class)]
class Semester implements PeriodInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $semesterTime = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $year = null;

    #[ORM\OneToMany(mappedBy: 'semester', targetEntity: AdmissionPeriod::class)]
    private Collection $admissionPeriods;

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->semesterTime . ' ' . $this->year;
    }

    public function getStartDate(): \DateTime
    {
        $startMonth = $this->semesterTime === 'Vår' ? '01' : '08';

        return date_create($this->year . '-' . $startMonth . '-01 00:00:00');
    }

    public function getEndDate(): \DateTime
    {
        $endMonth = $this->semesterTime === 'Vår' ? '07' : '12';

        return date_create($this->year . '-' . $endMonth . '-31 23:59:59');
    }

    // Used for unit testing
    public function fromArray($data = []): void
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getSemesterTime(): ?string
    {
        return $this->semesterTime;
    }

    public function setSemesterTime(string $semesterTime): self
    {
        $this->semesterTime = $semesterTime;

        return $this;
    }

    public function isActive(): bool
    {
        $now = new \DateTime();

        return $this->getStartDate() < $now && $now <= $this->getEndDate();
    }

    public function getAdmissionPeriods(): Collection
    {
        return $this->admissionPeriods;
    }

    public function setAdmissionPeriods(AdmissionPeriod $admissionPeriods): self
    {
        $this->admissionPeriods->add($admissionPeriods);

        return $this;
    }

    /**
     * Checks if this semester is between the bounds $semesterPrevious and $semesterLater.
     *
     * **Note**: This range comparison is weak, meaning the semester can count as
     * being inBetween even though it is equal to one or both of the semester
     * bounds.
     * Furthermore, the semester bounds can be null, which implies the range
     * extends infinitely far into the past or into the future.
     */
    public function isBetween(?Semester $semesterPrevious, ?Semester $semesterLater): bool
    {
        return $this->isAfter($semesterPrevious) && $this->isBefore($semesterLater);
    }

    /**
     * Checks if this semester is before $semester.
     *
     * **Note**: This function performs a weak comparison, meaning equal semesters count as before.
     * Furthermore, null semesters also count as before
     */
    public function isBefore(?Semester $semester): bool
    {
        if ($semester === null) {
            return true;
        }
        if ($this->year === $semester->getYear()) {
            return !($this->semesterTime === 'Høst' &&
                $semester->getSemesterTime() === 'Vår');
        }

        return $this->year < $semester->getYear();
    }

    /**
     * Checks if this semester is after $semester.
     *
     * **Note**: This function performs a weak comparison, meaning equal semesters count as after.
     * Furthermore, null semesters also count as after
     */
    public function isAfter(?Semester $semester): bool
    {
        if ($semester === null) {
            return true;
        }
        if ($this->year === $semester->getYear()) {
            return !($this->semesterTime === 'Vår' &&
                $semester->getSemesterTime() === 'Høst');
        }

        return $this->year > $semester->getYear();
    }
}
