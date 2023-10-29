<?php

namespace App\Core\Application\UseCase;

use App\Core\Application\DTO\FieldOfStudyDTO;
use App\Core\Application\UseCase\Interfaces\Persistence\IFieldOfStudyRepository;
use Psr\Log\LoggerInterface;

class FieldOfStudyUseCase
{
    public function __construct(private IFieldOfStudyRepository $fieldOfStudyRepository, private LoggerInterface $logger)
    {
    }

    public function getFieldOfStudyByDepartmentId(int $departmentId): array
    {
        $fieldOfStudies = $this->fieldOfStudyRepository->findByDepartmentId($departmentId);

        $fieldOfStudyDTOs = [];
        foreach ($fieldOfStudies as $fieldOfStudy) {
            $fieldOfStudyDTOs[] = FieldOfStudyDTO::createFromEntity($fieldOfStudy);
        }

        return $fieldOfStudyDTOs;
    }
}
