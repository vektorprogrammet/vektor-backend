<?php

namespace App\Entity;

use App\Repository\TeamApplicationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'team_application')]
#[ORM\Entity(repositoryClass: TeamApplicationRepository::class)]
class TeamApplication
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $name = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Email(message: 'Ikke gyldig e-post.')]
    private ?string $email = null;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(max: 45, maxMessage: 'Dette feltet kan ikke inneholde mer enn 45 tegn')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $fieldOfStudy = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $yearOfStudy = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $motivationText = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $biography = null;

    #[ORM\ManyToOne(targetEntity: 'Team', inversedBy: 'applications')]
    private ?Team $team = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $phone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): void
    {
        $this->team = $team;
    }

    public function getFieldOfStudy(): ?string
    {
        return $this->fieldOfStudy;
    }

    public function setFieldOfStudy(string $fieldOfStudy): void
    {
        $this->fieldOfStudy = $fieldOfStudy;
    }

    public function getMotivationText(): ?string
    {
        return $this->motivationText;
    }

    public function setMotivationText(string $motivationText): void
    {
        $this->motivationText = $motivationText;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(string $biography): void
    {
        $this->biography = $biography;
    }

    public function getYearOfStudy(): ?string
    {
        return $this->yearOfStudy;
    }

    public function setYearOfStudy(string $yearOfStudy): void
    {
        $this->yearOfStudy = $yearOfStudy;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }
}
