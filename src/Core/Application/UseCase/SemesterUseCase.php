<?php

namespace App\Core\Application\UseCase;

use App\Core\Application\DTO\SemesterDTO;
use App\Core\Application\UseCase\Interfaces\Persistence\ISemesterRepository;
use Psr\Log\LoggerInterface;

class SemesterUseCase
{
    public function __construct(private ISemesterRepository $semesterRepository, private LoggerInterface $logger)
    {
    }

    public function getCurrentSemester(): ?SemesterDTO
    {
        $date_now = new \DateTime();
        $semester = $this->semesterRepository->findSemesterByDate($date_now);
        if ($semester === null) {
            $this->logger->warning('[{method}] Trying to get current semester, but it does not exist', ['method' => __METHOD__]);

            return null;
        }

        return SemesterDTO::createFromEntity($semester);
    }

    public function getAllSemesters(): array
    {
        $semesters = $this->semesterRepository->findAll();

        $semesterDTOs = [];
        foreach ($semesters as $semester) {
            $semesterDTOs[] = SemesterDTO::createFromEntity($semester);
        }

        return $semesterDTOs;
    }
}
