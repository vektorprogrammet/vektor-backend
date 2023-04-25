<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LinjeforeningRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinjeforeningRepository::class)]
#[ApiResource]
class Linjeforening
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\OneToOne(inversedBy: 'linjeforening', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    public ?FieldOfStudy $fieldOfStudy = null;

    #[ORM\Column(length: 70, nullable: true)]
    private ?string $contact_person = null;
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

    public function getFOS(): ?FieldOfStudy
    {
        return $this->fieldOfStudy;
    }

    public function setFOS(FieldOfStudy $fieldOfStudy): self
    {
        $this->fieldOfStudy = $fieldOfStudy;

        return $this;
    }

    public function getContactPerson(): ?string
    {
        return $this->contact_person;
    }

    public function setContactPerson(?string $contact_person): self
    {
        $this->contact_person = $contact_person;

        return $this;
    }
}
