<?php

namespace App\Entity;

use App\Repository\FieldOfStudyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'field_of_study')]
#[ORM\Entity(repositoryClass: FieldOfStudyRepository::class)]
class FieldOfStudy
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 250)]
    private ?string $name = null;

    #[ORM\Column(name: 'short_name', type: 'string', length: 50)]
    private ?string $shortName = null;

    #[ORM\ManyToOne(targetEntity: 'Department', inversedBy: 'fieldOfStudy')]
    private ?Department $department = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setShortName(string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getShortName(): string
    {
        return $this->shortName;
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

    public function __toString()
    {
        return $this->getShortName();
    }
}
