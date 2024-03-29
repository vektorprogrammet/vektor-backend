<?php

namespace App\Core\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'school')]
#[ORM\Entity]
class School
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?string $name = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?string $contactPerson = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'schools')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    protected ?Department $department = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Email(message: 'Ikke gyldig e-post.')]
    protected ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: AssistantHistory::class)]
    private Collection $assistantHistories;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?string $phone = null;

    // TODO: dette feltet settes til 0 eller 1. Vi burde bruke bool
    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $international;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: SchoolCapacity::class)]
    private Collection $capacities;

    /**
     * @var bool
     */
    // TODO: refactor to use actual boolean values (not 1, 2..)
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 1])]
    private $active;

    public function __construct()
    {
        $this->international = false;
        $this->active = true;
        $this->assistantHistories = new ArrayCollection();
        $this->capacities = new ArrayCollection();
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

    public function setContactPerson(string $contactPerson): self
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

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

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function isInternational(): ?bool
    {
        return $this->international;
    }

    /**
     * @param bool $international
     */
    public function setInternational($international): void
    {
        $this->international = $international;
    }

    public function belongsToDepartment(Department $department): bool
    {
        if ($this->department === $department) {
            return true;
        }

        return false;
    }

    public function getAssistantHistories(): Collection
    {
        return $this->assistantHistories;
    }

    public function getCapacities(): Collection
    {
        return $this->capacities;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
