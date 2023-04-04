<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'school')]
#[ORM\Entity(repositoryClass: 'App\Repository\SchoolRepository')]
class School
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?string $name = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?string $contactPerson = null;

    #[ORM\ManyToMany(targetEntity: 'Department', mappedBy: 'schools')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    protected $departments;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Email(message: 'Ikke gyldig e-post.')]
    protected ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: 'AssistantHistory')]
    private $assistantHistories;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?string $phone = null;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $international;

    /**
     * @var SchoolCapacity[]
     */
    #[ORM\OneToMany(targetEntity: 'SchoolCapacity', mappedBy: 'school')]
    private $capacities;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    private $active;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->international = false;
        $this->active = true;
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

    public function addDepartment(Department $departments): self
    {
        $this->departments[] = $departments;

        return $this;
    }

    /**
     * Remove departments.
     */
    public function removeDepartment(Department $departments): void
    {
        $this->departments->removeElement($departments);
    }

    /**
     * Get departments.
     *
     * @return Collection
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return School
     */
    public function setEmail(string $email)
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

    // Used for unit testing
    public function fromArray($data = [])
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    /**
     * @return bool
     */
    public function isInternational()
    {
        return $this->international;
    }

    /**
     * @param bool $international
     */
    public function setInternational($international)
    {
        $this->international = $international;
    }

    public function belongsToDepartment(Department $department): bool
    {
        foreach ($this->departments as $dep) {
            if ($dep === $department) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return AssistantHistory[]
     */
    public function getAssistantHistories()
    {
        return $this->assistantHistories;
    }

    /**
     * @return SchoolCapacity[]
     */
    public function getCapacities()
    {
        return $this->capacities;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): School
    {
        $this->active = $active;

        return $this;
    }
}
