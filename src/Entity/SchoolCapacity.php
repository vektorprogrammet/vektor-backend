<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'school_capacity')]
#[ORM\Entity(repositoryClass: 'App\Repository\SchoolCapacityRepository')]
#[ORM\HasLifecycleCallbacks]
class SchoolCapacity
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'capacities')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'id')]
    protected ?School $school = null;

    #[ORM\ManyToOne(targetEntity: 'Semester')]
    #[ORM\JoinColumn(name: 'semester_id', referencedColumnName: 'id')]
    protected ?Semester $semester = null;

    #[ORM\ManyToOne(targetEntity: 'Department')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id')]
    protected ?Department $department = null;

    #[ORM\Column(name: 'monday', type: 'integer')]
    protected ?int $monday = null;

    #[ORM\Column(name: 'tuesday', type: 'integer')]
    protected ?int $tuesday = null;

    #[ORM\Column(name: 'wednesday', type: 'integer')]
    protected ?int $wednesday = null;

    #[ORM\Column(name: 'thursday', type: 'integer')]
    protected ?int $thursday = null;

    #[ORM\Column(name: 'friday', type: 'integer')]
    protected ?int $friday = null;

    /**
     * SchoolCapacity constructor.
     */
    public function __construct()
    {
        $this->monday = 0;
        $this->tuesday = 0;
        $this->wednesday = 0;
        $this->thursday = 0;
        $this->friday = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonday(): ?int
    {
        return $this->monday;
    }

    public function setMonday(int $monday): void
    {
        $this->monday = $monday;
    }

    public function getTuesday(): ?int
    {
        return $this->tuesday;
    }

    public function setTuesday(int $tuesday): void
    {
        $this->tuesday = $tuesday;
    }

    public function getWednesday(): ?int
    {
        return $this->wednesday;
    }

    public function setWednesday(int $wednesday): void
    {
        $this->wednesday = $wednesday;
    }

    public function getThursday(): ?int
    {
        return $this->thursday;
    }

    public function setThursday(int $thursday): void
    {
        $this->thursday = $thursday;
    }

    public function getFriday(): ?int
    {
        return $this->friday;
    }

    public function setFriday(int $friday): void
    {
        $this->friday = $friday;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(School $school): void
    {
        $this->school = $school;
    }

    public function getSemester(): ?Semester
    {
        return $this->semester;
    }

    public function setSemester(Semester $semester): void
    {
        $this->semester = $semester;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): self
    {
        $this->department = $department;

        return $this;
    }
}
