<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="field_of_study")
 * @ORM\Entity(repositoryClass="App\Repository\FieldOfStudyRepository")
 */
class FieldOfStudy
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=250)
     */
    private string $name;

    /**
     * @ORM\Column(name="short_name", type="string", length=50)
     */
    private string $shortName;

    /**
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="fieldOfStudy")
     */
    private Department $department;

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set department name.
     */
    public function setName(string $name): FieldOfStudy
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get department name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set department shortName.
     */
    public function setShortName(string $shortName): FieldOfStudy
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get department shortName.
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * Set department.
     */
    public function setDepartment(Department $department = null): FieldOfStudy
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department.
     */
    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function __toString()
    {
        return $this->getShortName();
    }
}
