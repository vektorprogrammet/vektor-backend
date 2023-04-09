<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'team_membership')]
#[ORM\Entity(repositoryClass: 'App\Repository\TeamMembershipRepository')]
class TeamMembership implements TeamMembershipInterface
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'teamMemberships')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\Valid]
    #[Assert\NotNull(message: 'Dette feltet kan ikke være tomt')]
    protected $user;

    #[ORM\ManyToOne(targetEntity: 'Semester')]
    #[Assert\Valid]
    #[Assert\NotNull(message: 'Dette feltet kan ikke være tomt')]
    protected ?Semester $startSemester = null;

    #[ORM\ManyToOne(targetEntity: 'Semester')]
    #[Assert\Valid]
    protected ?Semester $endSemester = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $deletedTeamName = null;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotNull(message: 'Dette feltet kan ikke være tomt')]
    private $isTeamLeader;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotNull(message: 'Dette feltet kan ikke være tomt')]
    private $isSuspended;

    #[ORM\ManyToOne(targetEntity: 'Team', inversedBy: 'teamMemberships')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    protected ?Team $team = null;

    #[ORM\ManyToOne(targetEntity: 'Position')]
    #[ORM\JoinColumn(name: 'position_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[Assert\Valid]
    #[Assert\NotNull(message: 'Dette feltet kan ikke være tomt')]
    protected ?Position $position = null;

    public function __construct()
    {
        $this->isTeamLeader = false;
        $this->isSuspended = false;
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

    public function setTeam(Team $team = null): self
    {
        $this->team = $team;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setPosition(Position $position = null): self
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    /**
     * Set startSemester.
     *
     * @return TeamMembership
     */
    public function setStartSemester(Semester $startSemester = null): self
    {
        $this->startSemester = $startSemester;

        return $this;
    }

    public function getStartSemester(): ?Semester
    {
        return $this->startSemester;
    }

    public function setEndSemester(Semester $endSemester = null): self
    {
        $this->endSemester = $endSemester;

        return $this;
    }

    public function getEndSemester(): ?Semester
    {
        return $this->endSemester;
    }

    /**
     * @return bool
     */
    public function isActiveInSemester(Semester $semester)
    {
        $semesterStartLaterThanTeamMembership = $semester->getStartDate() >= $this->getStartSemester()->getStartDate();
        $semesterEndsBeforeTeamMembership = $this->getEndSemester() === null || $semester->getEndDate() <= $this->getEndSemester()->getEndDate();

        return $semesterStartLaterThanTeamMembership && $semesterEndsBeforeTeamMembership;
    }

    public function isActive()
    {
        $department = $this->team->getDepartment();
        $activeSemester = $department->getCurrentOrLatestAdmissionPeriod()->getSemester();

        return $this->isActiveInSemester($activeSemester);
    }

    public function getTeamName(): string
    {
        if ($this->deletedTeamName !== null) {
            return $this->deletedTeamName;
        }

        return $this->team->getName();
    }

    public function setDeletedTeamName(string $deletedTeamName)
    {
        $this->deletedTeamName = $deletedTeamName;
    }

    public function getPositionName(): string
    {
        return $this->position->getName();
    }

    public function isTeamLeader(): bool
    {
        return $this->isTeamLeader;
    }

    /**
     * @param bool $isTeamLeader
     */
    public function setIsTeamLeader($isTeamLeader)
    {
        $this->isTeamLeader = $isTeamLeader;
    }

    public function isSuspended(): bool
    {
        return $this->isSuspended;
    }

    /**
     * @param bool $isSuspended
     */
    public function setIsSuspended($isSuspended)
    {
        $this->isSuspended = $isSuspended;
    }
}
