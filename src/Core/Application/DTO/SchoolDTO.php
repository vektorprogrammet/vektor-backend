<?php

namespace App\Core\Application\DTO;

use App\Core\Domain\Entity\School;

class SchoolDTO implements \JsonSerializable
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $contactPerson = null;
    private ?string $department = null;
    private ?string $email = null;
    private ?string $phone = null;
    private ?bool $international = null;
    private ?bool $active = null;

    public function __construct(?int $id, ?string $name, ?string $contactPerson, ?string $department, ?string $email, ?string $phone, ?bool $international, ?bool $active)
    {
        $this->id = $id;
        $this->name = $name;
        $this->contactPerson = $contactPerson;
        $this->department = $department;
        $this->email = $email;
        $this->phone = $phone;
        $this->international = $international;
        $this->active = $active;
    }

    public static function createFromEntity(School $school): SchoolDTO
    {
        return new SchoolDTO(
            $school->getId(),
            $school->getName(),
            $school->getContactPerson(),
            $school->getDepartment()->getShortName(),
            $school->getEmail(),
            $school->getPhone(),
            $school->isInternational(),
            $school->isActive()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contactPerson' => $this->contactPerson,
            'department' => $this->department,
            'email' => $this->email,
            'phone' => $this->phone,
            'international' => $this->international,
            'active' => $this->active,
        ];
    }
}