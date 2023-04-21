<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use App\Validator\Constraints as CustomAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'team')]
#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[UniqueEntity(fields: ['department', 'name'], message: 'Et team med dette navnet finnes allerede i avdelingen.')]
class Team implements TeamInterface
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 250)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?string $name = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Email(message: 'Ugyldig e-post')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være blankt.')]
    #[CustomAssert\UniqueCompanyEmail]
    #[CustomAssert\VektorEmail]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: 'Department', inversedBy: 'teams')]
    #[Assert\NotNull(message: 'Avdeling kan ikke være null')]
    protected ?Department $department = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'short_description', type: 'string', nullable: true)]
    #[Assert\Length(max: 125, maxMessage: 'Maks 125 Tegn')]
    private ?string $shortDescription = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $acceptApplication = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $deadline = null;

    /**
     * Applications with team interest.
     *
     * @var Application[]
     */
    #[ORM\ManyToMany(targetEntity: 'Application', mappedBy: 'potentialTeams')]
    private $potentialMembers;

    /**
     * TeamInterest entities not corresponding to any Application.
     *
     * @var TeamInterest[]
     */
    #[ORM\ManyToMany(targetEntity: 'TeamInterest', mappedBy: 'potentialTeams')]
    private $potentialApplicants;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $active;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: 'TeamApplication')]
    private $applications;

    /**
     * @var TeamMembership[]
     */
    #[ORM\OneToMany(mappedBy: 'team', targetEntity: 'TeamMembership')]
    private $teamMemberships;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    public function getAcceptApplication(): bool
    {
        return $this->acceptApplication;
    }

    /**
     * @return Team
     */
    public function setAcceptApplication(bool $acceptApplication)
    {
        $this->acceptApplication = $acceptApplication;

        return $this;
    }

    public function __construct()
    {
        $this->active = true;
        $this->teamMemberships = [];
    }

    public function __toString()
    {
        return (string) $this->getName();
    }

    public function getType(): string
    {
        return 'team';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
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

    // Used for unit testing
    public function fromArray($data = []): void
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDeadline(): ?\DateTime
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTime $deadline = null): self
    {
        $now = new \DateTime();
        if ($this->acceptApplication && $now <= $deadline) {
            $this->deadline = $deadline;
        } else {
            $this->deadline = null;
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return TeamMembership[]
     */
    public function getTeamMemberships()
    {
        return $this->teamMemberships;
    }

    /**
     * @return TeamMembership[]
     */
    public function getActiveTeamMemberships()
    {
        $histories = [];

        foreach ($this->teamMemberships as $wh) {
            $semester = $wh->getUser()->getDepartment()->getCurrentOrLatestAdmissionPeriod()->getSemester();
            if ($semester !== null && $wh->isActiveInSemester($semester)) {
                $histories[] = $wh;
            }
        }

        return $histories;
    }

    /**
     * @return User[]
     */
    public function getActiveUsers()
    {
        $activeUsers = [];

        foreach ($this->getActiveTeamMemberships() as $activeTeamMembership) {
            if (!in_array($activeTeamMembership->getUser(), $activeUsers, true)) {
                $activeUsers[] = $activeTeamMembership->getUser();
            }
        }

        return $activeUsers;
    }

    /**
     * @return Application[]
     */
    public function getPotentialMembers()
    {
        return $this->potentialMembers;
    }

    /**
     * @param Application[] $potentialMembers
     */
    public function setPotentialMembers($potentialMembers): void
    {
        $this->potentialMembers = $potentialMembers;
    }

    /**
     * @return TeamInterest[]
     */
    public function getPotentialApplicants()
    {
        return $this->potentialApplicants;
    }

    /**
     * @param TeamInterest[] $potentialApplicants
     */
    public function setPotentialApplicants($potentialApplicants): self
    {
        $this->potentialApplicants = $potentialApplicants;

        return $this;
    }

    public function getNumberOfPotentialMembersAndApplicantsInSemester($semester)
    {
        $array = array_merge($this->potentialApplicants->toArray(), $this->potentialMembers->toArray());
        $array = array_filter($array, function (DepartmentSemesterInterface $a) use ($semester) {
            return $a->getSemester() === $semester;
        });

        return count($array);
    }

    /**
     * @return TeamApplication[]
     */
    public function getApplications()
    {
        return $this->applications;
    }

    public function setApplications(TeamApplication $applications): void
    {
        $this->applications = $applications;
    }

    public function getAcceptApplicationAndDeadline(): bool
    {
        $now = new \DateTime();

        return ($this->acceptApplication && $now < $this->deadline) || ($this->acceptApplication && $this->deadline === null);
    }
}
