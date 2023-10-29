<?php

namespace App\Core\Domain\Entity;

use App\Core\Domain\Interfaces\ITeam;
use App\Entity\TeamMembership;
use App\Validator\Constraints as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'team')]
#[ORM\Entity]
#[UniqueEntity(fields: ['department', 'name'], message: 'Et team med dette navnet finnes allerede i avdelingen.')]
class Team implements ITeam
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 250)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?string $name = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Assert\Email(message: 'Ugyldig e-post')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være blankt.')]
    #[CustomAssert\UniqueCompanyEmail]
    #[CustomAssert\VektorEmail]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'teams')]
    #[Assert\NotNull(message: 'Avdeling kan ikke være null')]
    protected ?Department $department = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'short_description', type: Types::STRING, nullable: true)]
    #[Assert\Length(max: 125, maxMessage: 'Maks 125 Tegn')]
    private ?string $shortDescription = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $acceptApplication = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $deadline = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $active;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: TeamMembership::class)]
    private Collection $teamMemberships;

    public function __construct()
    {
        $this->active = true;
        $this->teamMemberships = new ArrayCollection();
    }

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

    public function setAcceptApplication(bool $acceptApplication): self
    {
        $this->acceptApplication = $acceptApplication;

        return $this;
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

    public function getTeamMemberships(): Collection
    {
        return $this->teamMemberships;
    }
}
