<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table]
#[ORM\Entity(repositoryClass: 'App\Repository\TeamInterestRepository')]
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

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'timestamp', type: 'datetime')]
    private $timestamp;

    /**
     * @var Team[]
     */
    #[ORM\ManyToMany(targetEntity: 'App\Entity\Team', inversedBy: 'potentialApplicants')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Count(min: 1, minMessage: 'Du må velge minst ett team')]
    private $potentialTeams;

    /**
     * @var Semester
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Semester')]
    private $semester;

    /**
     * @var Department
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Department')]
    private $department;

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

    /**
     * Get name.
     *
     * @param string $name
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set email.
     *
     * @param string $email
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Get timestamp.
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Get potentialTeams.
     *
     * @return Team[]
     */
    public function getPotentialTeams()
    {
        return $this->potentialTeams;
    }

    /**
     * Set potentialTeams.
     *
     * @param Team[] $potentialTeams
     *
     * @return TeamInterest
     */
    public function setPotentialTeams($potentialTeams)
    {
        $this->potentialTeams = $potentialTeams;

        return $this;
    }

    /**
     * Get semester.
     *
     * @return Semester
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * Set semester.
     */
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
