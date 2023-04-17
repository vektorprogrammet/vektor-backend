<?php

namespace App\Entity;

use App\Validator\Constraints as CustomAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'application')]
#[ORM\Entity(repositoryClass: 'App\Repository\ApplicationRepository')]
#[CustomAssert\ApplicationEmail(groups: ['admission'])]
class Application implements DepartmentSemesterInterface
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    /**
     * @var AdmissionPeriod
     */
    #[ORM\ManyToOne(targetEntity: 'AdmissionPeriod')]
    private $admissionPeriod;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['admission', 'admission_existing'])]
    private ?string $yearOfStudy = null;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private $monday;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private $tuesday;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private $wednesday;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private $thursday;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private $friday;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private $substitute;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['interview', 'admission_existing'])]
    private ?string $language = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['interview', 'admission_existing'])]
    // TODO: refactor to use actual boolean values (not 1, 2..)
    private $doublePosition;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $preferredGroup = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Dette feltet kan ikke inneholde mer enn 255 tegn.')]
    private ?string $preferredSchool = null;

    #[ORM\ManyToOne(targetEntity: 'User', cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\Valid]
    private $user;

    #[ORM\Column(type: 'boolean')]
    private $previousParticipation;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTime $last_edited = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTime $created = null;

    #[ORM\Column(type: 'array')]
    // TODO: type array is deprecated
    private $heardAboutFrom;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.', groups: ['interview', 'admission_existing'])]
    // TODO: refactor to use actual boolean values (not 1, 2..)
    private $teamInterest;

    #[ORM\ManyToMany(targetEntity: 'App\Entity\Team', inversedBy: 'potentialMembers')]
    private $potentialTeams;

    #[ORM\OneToOne(inversedBy: 'application', targetEntity: 'Interview', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Assert\Valid]
    private ?Interview $interview = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $specialNeeds = null;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->last_edited = new \DateTime();
        $this->created = new \DateTime();
        $this->substitute = false;
        $this->doublePosition = false;
        $this->previousParticipation = false;
        $this->teamInterest = false;
        $this->specialNeeds = '';
        $this->monday = true;
        $this->tuesday = true;
        $this->wednesday = true;
        $this->thursday = true;
        $this->friday = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return AdmissionPeriod
     */
    public function getAdmissionPeriod()
    {
        return $this->admissionPeriod;
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     */
    public function setAdmissionPeriod($admissionPeriod): self
    {
        $this->admissionPeriod = $admissionPeriod;

        return $this;
    }

    public function getYearOfStudy(): ?string
    {
        return $this->yearOfStudy;
    }

    public function setYearOfStudy(string $yearOfStudy): void
    {
        $this->yearOfStudy = $yearOfStudy;
    }

    public function isMonday(): bool
    {
        return $this->monday;
    }

    public function setMonday(bool $monday)
    {
        $this->monday = $monday;
    }

    public function isTuesday(): bool
    {
        return $this->tuesday;
    }

    public function setTuesday(bool $tuesday)
    {
        $this->tuesday = $tuesday;
    }

    public function isWednesday(): bool
    {
        return $this->wednesday;
    }

    public function setWednesday(bool $wednesday)
    {
        $this->wednesday = $wednesday;
    }

    public function isThursday(): bool
    {
        return $this->thursday;
    }

    public function setThursday(bool $thursday)
    {
        $this->thursday = $thursday;
    }

    public function isFriday(): bool
    {
        return $this->friday;
    }

    public function setFriday(bool $friday)
    {
        $this->friday = $friday;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return bool
     */
    public function getDoublePosition()
    {
        return $this->doublePosition;
    }

    /**
     * @param bool $doublePosition
     */
    public function setDoublePosition($doublePosition)
    {
        $this->doublePosition = $doublePosition;
    }

    public function getPreferredGroup(): ?string
    {
        return $this->preferredGroup;
    }

    public function setPreferredGroup(string $preferredGroup): void
    {
        $this->preferredGroup = $preferredGroup;
    }

    public function getLastEdited(): ?\DateTime
    {
        return $this->last_edited;
    }

    public function setLastEdited(\DateTime $last_edited): void
    {
        $this->last_edited = $last_edited;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    }

    /**
     * Get heardAboutFrom.
     *
     * @return array
     */
    public function getHeardAboutFrom()
    {
        return $this->heardAboutFrom;
    }

    /**
     * Set heardAboutFrom.
     *
     * @param array $heardAboutFrom
     */
    public function setHeardAboutFrom($heardAboutFrom)
    {
        $this->heardAboutFrom = $heardAboutFrom;
    }

    /**
     * @return Interview
     */
    public function getInterview()
    {
        return $this->interview;
    }

    /**
     * @param Interview $interview
     */
    public function setInterview($interview)
    {
        $this->interview = $interview;
    }

    /**
     * @return bool
     */
    public function getPreviousParticipation()
    {
        return $this->previousParticipation;
    }

    /**
     * @param bool $previousParticipation
     */
    public function setPreviousParticipation($previousParticipation)
    {
        $this->previousParticipation = $previousParticipation;
    }

    /**
     * @return bool
     */
    public function getTeamInterest()
    {
        return $this->teamInterest;
    }

    /**
     * @param bool $teamInterest
     */
    public function setTeamInterest($teamInterest)
    {
        $this->teamInterest = $teamInterest;
    }

    /**
     * @return bool
     */
    public function isSubstitute()
    {
        return $this->substitute;
    }

    /**
     * @param bool $substitute
     */
    public function setSubstitute($substitute)
    {
        $this->substitute = $substitute;
    }

    public function getSpecialNeeds(): ?string
    {
        return $this->specialNeeds;
    }

    public function setSpecialNeeds(string $specialNeeds = null): void
    {
        $this->specialNeeds = $specialNeeds;
    }

    public function getPreferredSchool(): ?string
    {
        return $this->preferredSchool;
    }

    public function setPreferredSchool($preferredSchool): void
    {
        $this->preferredSchool = $preferredSchool;
    }

    /**
     * @return Team[]
     */
    public function getPotentialTeams()
    {
        return $this->potentialTeams;
    }

    /**
     * @param Team[] $potentialTeams
     */
    public function setPotentialTeams($potentialTeams)
    {
        $this->potentialTeams = $potentialTeams;
    }

    public function getSemester(): ?Semester
    {
        return $this->admissionPeriod->getSemester();
    }

    public function getDepartment(): ?Department
    {
        return $this->admissionPeriod->getDepartment();
    }
}
