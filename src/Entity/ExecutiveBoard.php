<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'executive_board')]
#[ORM\Entity(repositoryClass: 'App\Repository\ExecutiveBoardRepository')]
class ExecutiveBoard implements TeamInterface
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
    private ?string $email = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'short_description', type: 'string', nullable: true)]
    #[Assert\Length(max: 125, maxMessage: 'Maks 125 Tegn')]
    private ?string $shortDescription = null;

    /**
     * @var ExecutiveBoardMembership[]
     */
    #[ORM\OneToMany(mappedBy: 'board', targetEntity: 'ExecutiveBoardMembership')]
    private $boardMemberships;

    public function __toString()
    {
        return (string) $this->getName();
    }

    public function getType(): string
    {
        return 'executive_board';
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     */
    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return ExecutiveBoardMembership[]
     */
    public function getBoardMemberships()
    {
        return $this->boardMemberships;
    }

    /**
     * @return ExecutiveBoardMembership[]
     */
    public function getTeamMemberships()
    {
        return $this->boardMemberships;
    }

    public function getAcceptApplication(): bool
    {
        return false;
    }

    public function getAcceptApplicationAndDeadline(): bool
    {
        return false;
    }

    /**
     * @return ExecutiveBoardMembership[]
     */
    public function getActiveTeamMemberships()
    {
        $activeTeamMemberships = [];

        foreach ($this->getTeamMemberships() as $teamMembership) {
            if ($teamMembership->isActive()) {
                $activeTeamMemberships[] = $teamMembership;
            }
        }

        return $activeTeamMemberships;
    }

    /**
     * @return User[]
     */
    public function getActiveUsers()
    {
        $activeUsers = [];

        foreach ($this->getActiveTeamMemberships() as $activeExecutiveBoardHistory) {
            if (!in_array($activeExecutiveBoardHistory->getUser(), $activeUsers, true)) {
                $activeUsers[] = $activeExecutiveBoardHistory->getUser();
            }
        }

        return $activeUsers;
    }
}
