<?php

namespace App\Core\Application\DTO;

use App\Core\Domain\Entity\FieldOfStudy;
use JsonSerializable;

class FieldOfStudyDTO implements JsonSerializable
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $shortName = null;
    private ?string $department = null;

    public function __construct(?int $id, ?string $name, ?string $shortName, ?string $department)
    {
        $this->id = $id;
        $this->name = $name;
        $this->shortName = $shortName;
        $this->department = $department;
    }

    public static function createFromEntity(FieldOfStudy $fieldOfStudy): FieldOfStudyDTO
    {
        return new FieldOfStudyDTO(
            $fieldOfStudy->getId(),
            $fieldOfStudy->getName(),
            $fieldOfStudy->getShortName(),
            $fieldOfStudy->getDepartment()->getShortName()
        );
    }

    public function toEntity(): FieldOfStudy
    {
        $fieldOfStudy = new  FieldOfStudy();
        $fieldOfStudy->setName($this->name);
        $fieldOfStudy->setShortName($this->shortName);
        return $fieldOfStudy;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'shortName' => $this->shortName,
            'department' => $this->department
        ];
    }

}