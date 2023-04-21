<?php

namespace App\Entity;

use App\Repository\TeamInterestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table]
#[ORM\Entity(repositoryClass: TeamInterestRepository::class)]
class TeamInterest implements DepartmentSemesterInterface
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Length(max: 255, maxMessage: 'Navnet ditt kan maksimalt være 255 tegn')]
    private ?string $name = null;

    #[ORM\Column(name: 'email', type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Length(max: 255, maxMessage: 'Emailen din kan maksimalt være 255 tegn')]
    #[Assert\Email(message: 'Emailen din er ikke formatert riktig')]
    private ?string $email = null;

    #[ORM\Column(name: 'timestamp', type: 'datetime')]
    private ?\DateTime $timestamp = null;

    /**
     * @var Team[]
     */
    #[ORM\ManyToMany(targetEntity: 'Team', inversedBy: 'potentialApplicants')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Count(min: 1, minMessage: 'Du må velge minst ett team')]
    private $potentialTeams;

    #[ORM\ManyToOne(targetEntity: 'Semester')]
    private ?Semester $semester = null;

    #[ORM\ManyToOne(targetEntity: 'Department')]
    private ?Department $department = null;

    /**
     * TeamInterest constructor.
     */
    public function __construct()
    {
        $this->timestamp = new \DateTime();
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

    /**
     * @return Team[]
     */
    public function getPotentialTeams()
    {
        return $this->potentialTeams;
    }

    /**
     * @param Team[] $potentialTeams
     */
    public function setPotentialTeams($potentialTeams): self
    {
        $this->potentialTeams = $potentialTeams;

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

    public function setDepartment(Department $department): TeamInterest
    {
        $this->department = $department;

        return $this;
    }
}
