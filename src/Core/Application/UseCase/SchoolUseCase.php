<?php

namespace App\Core\Application\UseCase;

use App\Core\Application\DTO\SchoolDTO;
use App\Core\Application\UseCase\Interfaces\Persistence\ISchoolRepository;
use Psr\Log\LoggerInterface;

class SchoolUseCase
{
    public function __construct(private ISchoolRepository $schoolRepository, private LoggerInterface $logger)
    {
    }

    public function getSchoolsByDepartmentId(int $departmentId): array
    {
        $schools = $this->schoolRepository->findByDepartmentId($departmentId);

        $schoolDTOs = [];
        foreach ($schools as $school) {
            $schoolDTOs[] = SchoolDTO::createFromEntity($school);
        }

        return $schoolDTOs;
    }
}
