<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'team_application')]
#[ORM\Entity(repositoryClass: 'App\Repository\TeamApplicationRepository')]
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
    private $motivationText;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $biography;

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

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): void
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

    /**
     * @param string $fieldOfStudy
     */
    public function setFieldOfStudy($fieldOfStudy): void
    {
        $this->fieldOfStudy = $fieldOfStudy;
    }

    /**
     * @return string
     */
    public function getMotivationText()
    {
        return $this->motivationText;
    }

    /**
     * @param string $motivationText
     */
    public function setMotivationText($motivationText)
    {
        $this->motivationText = $motivationText;
    }

    /**
     * @return string
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * @param string $biography
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;
    }

    public function getYearOfStudy(): ?string
    {
        return $this->yearOfStudy;
    }

    /**
     * @param string $yearOfStudy
     */
    public function setYearOfStudy($yearOfStudy): void
    {
        $this->yearOfStudy = $yearOfStudy;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }
}
