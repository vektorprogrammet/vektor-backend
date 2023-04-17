<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'department')]
#[ORM\Entity(repositoryClass: 'App\Repository\DepartmentRepository')]
#[UniqueEntity(fields: ['city'])]
class Department
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 250)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(name: 'short_name', type: 'string', length: 50)]
    #[Assert\NotBlank]
    private ?string $shortName = null;

    #[ORM\Column(type: 'string', length: 250)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    protected ?string $address = null;

    #[ORM\Column(type: 'string', length: 250, unique: true)]
    #[Assert\NotBlank]
    private ?string $city = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $latitude = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $longitude = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $slackChannel = null;

    #[ORM\JoinTable(name: 'department_school')]
    #[ORM\OneToMany(mappedBy: 'department', targetEntity: 'School')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    protected $schools;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: 'FieldOfStudy', cascade: ['remove'])]
    private $fieldOfStudy;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: 'AdmissionPeriod', cascade: ['remove'])]
    #[ORM\OrderBy(['startDate' => 'DESC'])]
    private $admissionPeriods;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: 'Team', cascade: ['remove'])]
    private $teams;

    #[ORM\Column(name: 'logo_path', type: 'string', length: 255, nullable: true)]
    #[Assert\Length(min: 1, max: 255, maxMessage: '"Pathkanmaksvære')]
    private ?string $logoPath = null;

    # TODO: refactor to use actual boolean values (not 1, 2..)
    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => 1])]
    private $active;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->schools = new ArrayCollection();
        $this->fieldOfStudy = new ArrayCollection();
        $this->admissionPeriods = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->active = true;
    }

    public function getCurrentAdmissionPeriod(): ?AdmissionPeriod
    {
        $now = new \DateTime();

        /** @var AdmissionPeriod $admissionPeriod */
        foreach ($this->admissionPeriods as $admissionPeriod) {
            if ($now > $admissionPeriod->getSemester()->getStartDate() && $now < $admissionPeriod->getSemester()->getEndDate()) {
                return $admissionPeriod;
            }
        }

        return null;
    }

    public function getLatestAdmissionPeriod(): AdmissionPeriod
    {
        /** @var AdmissionPeriod[] $admissionPeriods */
        $admissionPeriods = $this->getAdmissionPeriods()->toArray();

        $latestAdmissionPeriod = current($admissionPeriods);

        $now = new \DateTime();

        foreach ($admissionPeriods as $admissionPeriod) {
            if (
                $admissionPeriod->getSemester()->getStartDate() < $now &&
                $admissionPeriod->getSemester()->getEndDate() > $latestAdmissionPeriod->getSemester()->getEndDate()
            ) {
                $latestAdmissionPeriod = $admissionPeriod;
            }
        }

        return $latestAdmissionPeriod;
    }

    public function getCurrentOrLatestAdmissionPeriod(): ?AdmissionPeriod
    {
        if (null === $admissionPeriod = $this->getCurrentAdmissionPeriod()) {
            $admissionPeriod = $this->getLatestAdmissionPeriod();
        }

        return $admissionPeriod;
    }

    public function activeAdmission(): bool
    {
        $admissionPeriod = $this->getCurrentAdmissionPeriod();
        if (!$admissionPeriod) {
            return false;
        }

        $start = $admissionPeriod->getStartDate();
        $end = $admissionPeriod->getEndDate();
        $now = new \DateTime();

        return $start < $now && $now < $end;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): Department
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setShortName(string $shortName): Department
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function addFieldOfStudy(FieldOfStudy $fieldOfStudy): Department
    {
        $this->fieldOfStudy[] = $fieldOfStudy;

        return $this;
    }

    public function removeFieldOfStudy(FieldOfStudy $fieldOfStudy): void
    {
        $this->fieldOfStudy->removeElement($fieldOfStudy);
    }

    /**
     * Get fieldOfStudy.
     *
     * @return ArrayCollection
     */
    public function getFieldOfStudy()
    {
        return $this->fieldOfStudy;
    }

    public function __toString(): string
    {
        return $this->getCity();
    }

    public function setEmail(string $email): Department
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function addSchool(School $schools): Department
    {
        $this->schools[] = $schools;

        return $this;
    }

    public function removeSchool(School $schools): void
    {
        $this->schools->removeElement($schools);
    }

    public function getSchools()
    {
        return $this->schools;
    }

    public function setAddress(string $address): Department
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Add admission periods.
     */
    public function addAdmissionPeriod(AdmissionPeriod $admissionPeriod): Department
    {
        $this->admissionPeriods[] = $admissionPeriod;

        return $this;
    }

    /**
     * Get admission periods.
     */
    public function getAdmissionPeriods()
    {
        return $this->admissionPeriods;
    }

    /**
     * Add teams.
     */
    public function addTeam(Team $teams): Department
    {
        $this->teams[] = $teams;

        return $this;
    }

    /**
     * Remove teams.
     */
    public function removeTeam(Team $teams): void
    {
        $this->teams->removeElement($teams);
    }

    /**
     * Get teams.
     *
     * @return ArrayCollection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): Department
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): Department
    {
        $this->longitude = $longitude;

        return $this;
    }

    // Used for unit testing
    public function fromArray($data = []): void
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    public function getSlackChannel(): ?string
    {
        return $this->slackChannel;
    }

    public function setSlackChannel(string $slackChannel): Department
    {
        $this->slackChannel = $slackChannel;

        return $this;
    }

    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    public function setLogoPath(string $logoPath): Department
    {
        $this->logoPath = $logoPath;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): Department
    {
        $this->active = $active;

        return $this;
    }
}
