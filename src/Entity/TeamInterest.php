<?php

namespace App\Entity;

use App\Repository\TeamInterestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table]
#[ORM\Entity(repositoryClass: TeamInterestRepository::class)]
class TeamInterest implements DepartmentSemesterInterface
{
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Length(max: 255, maxMessage: 'Navnet ditt kan maksimalt være 255 tegn')]
    private ?string $name = null;

    #[ORM\Column(name: 'email', type: Types::STRING, length: 255)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Length(max: 255, maxMessage: 'Emailen din kan maksimalt være 255 tegn')]
    #[Assert\Email(message: 'Emailen din er ikke formatert riktig')]
    private ?string $email = null;

    #[ORM\Column(name: 'timestamp', type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $timestamp = null;

    #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: 'potentialApplicants')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Count(min: 1, minMessage: 'Du må velge minst ett team')]
    private Collection $potentialTeams;

    #[ORM\ManyToOne(targetEntity: Semester::class)]
    private ?Semester $semester = null;

    #[ORM\ManyToOne(targetEntity: Department::class)]
    private ?Department $department = null;

    /**
     * TeamInterest constructor.
     */
    public function __construct()
    {
        $this->timestamp = new \DateTime();
        $this->potentialTeams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTimestamp(): ?\DateTime
    {
        return $this->timestamp;
    }

    public function getPotentialTeams(): Collection
    {
        return $this->potentialTeams;
    }

    public function setPotentialTeams($potentialTeam): self
    {
        $this->potentialTeams->add($potentialTeam);

        return $this;
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

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): self
    {
        $this->department = $department;

        return $this;
    }
}
