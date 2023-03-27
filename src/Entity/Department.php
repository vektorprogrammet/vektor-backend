<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="department")
 * @ORM\Entity(repositoryClass="App\Repository\DepartmentRepository")
 * @UniqueEntity(fields={"city"})
 */
class Department
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     */
    private ?string $name = null;

    /**
     * @ORM\Column(name="short_name", type="string", length=50)
     * @Assert\NotBlank
     */
    private string $shortName;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     * @Assert\Email
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected string $address;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     * @Assert\NotBlank
     */
    private ?string $city = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255)
     */
    private string $latitude;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255)
     */
    private string $longitude;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $slackChannel;

    /**
     * @ORM\ManyToMany(targetEntity="School", inversedBy="departments")
     * @ORM\JoinTable(name="department_school")
     * @ORM\JoinColumn(onDelete="cascade")
     **/
    protected $schools;

    /**
     * @ORM\OneToMany(targetEntity="FieldOfStudy", mappedBy="department",
     *     cascade={"remove"})
     */
    private $fieldOfStudy;

    /**
     * @ORM\OneToMany(targetEntity="AdmissionPeriod", mappedBy="department",
     *     cascade={"remove"})
     * @ORM\OrderBy({"startDate" = "DESC"})
     **/
    private $admissionPeriods;

    /**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="department",
     *     cascade={"remove"})
     **/
    private $teams;

    /**
     * @ORM\Column(name="logo_path", type="string", length=255, nullable=true)
     * @Assert\Length(min = 1, max = 255, maxMessage="Path kan maks være 255
     *     tegn."))
     **/
    private string $logoPath;

    /**
     * @ORM\Column(name="active", type="boolean", nullable=false,
     *     options={"default" : 1})
     */
    private bool $active;

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
            if ($admissionPeriod->getSemester()->getStartDate() < $now &&
                $admissionPeriod->getSemester()->getEndDate() > $latestAdmissionPeriod->getSemester()->getEndDate()) {
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

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     */
    public function setName(string $name): Department
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set shortName.
     */
    public function setShortName(string $shortName): Department
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName.
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * Add fieldOfStudy.
     */
    public function addFieldOfStudy(FieldOfStudy $fieldOfStudy): Department
    {
        $this->fieldOfStudy[] = $fieldOfStudy;

        return $this;
    }

    /**
     * Remove fieldOfStudy.
     */
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
//        return '' . $this->getCity();
        return $this->getCity();
    }

    /**
     * Set email.
     */
    public function setEmail(string $email): Department
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Add schools.
     */
    public function addSchool(School $schools): Department
    {
        $this->schools[] = $schools;

        return $this;
    }

    /**
     * Remove schools.
     */
    public function removeSchool(School $schools): void
    {
        $this->schools->removeElement($schools);
    }

    /**
     * Get schools.
     */
    public function getSchools()
    {
        return $this->schools;
    }

    /**
     * Set address.
     */
    public function setAddress(string $address): Department
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     */
    public function getAddress(): string
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

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    /**
     * Set latitude.
     */
    public function setLatitude(string $latitude): Department
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * get longitude.
     */
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    /**
     * set longitude.
     */
    public function setLongitude(string $longitude): Department
    {
        $this->longitude = $longitude;

        return $this;
    }

    // Used for unit testing
    public function fromArray($data = [])
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    public function getSlackChannel(): string
    {
        return $this->slackChannel;
    }

    /**
     * set slack channel.
     */
    public function setSlackChannel(string $slackChannel): Department
    {
        $this->slackChannel = $slackChannel;

        return $this;
    }

    public function getLogoPath(): string
    {
        return $this->logoPath;
    }

    /**
     * Update logo path.
     */
    public function setLogoPath(string $logoPath): Department
    {
        $this->logoPath = $logoPath;

        return $this;
    }

    /**
     * @return bool $active
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Update active status.
     */
    public function setActive(bool $active): Department
    {
        $this->active = $active;

        return $this;
    }
}
