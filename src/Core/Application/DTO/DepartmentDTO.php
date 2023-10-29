<?php

namespace App\Core\Application\DTO;

use App\Core\Domain\Entity\Department;

class DepartmentDTO implements \JsonSerializable
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $shortName = null;
    private ?string $email = null;
    private ?string $logoPath = null;
    private ?string $city = null;

    public function __construct(?int $id, ?string $name, ?string $shortName, ?string $email, ?string $logoPath, ?string $city)
    {
        $this->id = $id;
        $this->name = $name;
        $this->shortName = $shortName;
        $this->email = $email;
        $this->logoPath = $logoPath;
        $this->city = $city;
    }

    public static function createFromEntity(Department $department): DepartmentDTO
    {
        return new DepartmentDTO(
            $department->getId(),
            $department->getName(),
            $department->getShortName(),
            $department->getEmail(),
            $department->getLogoPath(),
            $department->getCity()
        );
    }

    public function toEntity(): Department
    {
        return new Department(
            $this->id,
            $this->name,
            $this->shortName,
            $this->email,
            $this->logoPath,
            $this->city
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'shortName' => $this->shortName,
            'email' => $this->email,
            'logoPath' => $this->logoPath,
            'city' => $this->city,
        ];
    }
}
