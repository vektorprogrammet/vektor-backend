<?php

namespace App\Entity;

use App\Repository\ExecutiveBoardMembershipRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'executive_board_membership')]
#[ORM\Entity(repositoryClass: ExecutiveBoardMembershipRepository::class)]
class ExecutiveBoardMembership implements TeamMembershipInterface
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'executiveBoardMemberships')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $user;

    #[ORM\ManyToOne(targetEntity: ExecutiveBoard::class, inversedBy: 'boardMemberships')]
    private ?ExecutiveBoard $board = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private ?string $positionName = null;

    #[ORM\ManyToOne(targetEntity: Semester::class)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected ?Semester $startSemester = null;

    #[ORM\ManyToOne(targetEntity: Semester::class)]
    #[Assert\Valid]
    protected ?Semester $endSemester = null;

    public function __construct()
    {
        $this->positionName = '';
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser(User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setBoard(ExecutiveBoard $board = null): self
    {
        $this->board = $board;

        return $this;
    }

    public function getBoard(): ExecutiveBoard
    {
        return $this->board;
    }

    public function getPositionName(): ?string
    {
        return $this->positionName;
    }

    /**
     * @param string $positionName
     */
    public function setPositionName($positionName): self
    {
        $this->positionName = $positionName;

        return $this;
    }

    public function setStartSemester(Semester $semester = null): self
    {
        $this->startSemester = $semester;

        return $this;
    }

    public function getStartSemester(): ?Semester
    {
        return $this->startSemester;
    }

    public function setEndSemester(Semester $semester = null): self
    {
        $this->endSemester = $semester;

        return $this;
    }

    public function getEndSemester(): ?Semester
    {
        return $this->endSemester;
    }

    public function isActive(): bool
    {
        $now = new \DateTime();
        $termEndsInFuture = $this->endSemester === null || $this->endSemester->getEndDate() > $now;
        $termStartedInPast = $this->startSemester !== null && $this->startSemester->getStartDate() < $now;

        return $termEndsInFuture && $termStartedInPast;
    }

    /**
     * @return TeamInterface
     */
    public function getTeam()
    {
        return $this->board;
    }
}
