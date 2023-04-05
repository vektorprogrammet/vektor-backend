<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'assistant_history')]
#[ORM\Entity(repositoryClass: 'App\Repository\AssistantHistoryRepository')]
class AssistantHistory
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'assistantHistories')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $user;

    #[ORM\ManyToOne(targetEntity: 'Semester')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?Semester $semester = null;

    #[ORM\ManyToOne(targetEntity: 'Department')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\NotBlank(message: 'Region må velges.')]
    private ?Department $department = null;

    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'assistantHistories')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?School $school = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $workdays = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $bolk = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $day = null;

    public function activeInGroup($group): bool
    {
        return mb_strpos($this->bolk, "Bolk $group") !== false;
    }

    /**
     * Set user.
     *
     * @return AssistantHistory
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function setSemester(Semester $semester = null): self
    {
        $this->semester = $semester;

        return $this;
    }

    public function getSemester(): ?Semester
    {
        return $this->semester;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    /**
     * @return AssistantHistory
     */
    public function setDepartment(Department $department)
    {
        $this->department = $department;

        return $this;
    }

    public function setSchool(School $school = null): self
    {
        $this->school = $school;

        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * Set workdays.
     *
     * @param string $workdays
     */
    public function setWorkdays($workdays): self
    {
        $this->workdays = $workdays;

        return $this;
    }

    public function getWorkdays(): ?string
    {
        return $this->workdays;
    }

    public function getBolk(): ?string
    {
        return $this->bolk;
    }

    /**
     * @param string $bolk
     */
    public function setBolk($bolk): void
    {
        $this->bolk = $bolk;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    /**
     * @param string $day
     */
    public function setDay($day): void
    {
        $this->day = $day;
    }

    // Used for unit testing
    public function fromArray($data = [])
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }
}
