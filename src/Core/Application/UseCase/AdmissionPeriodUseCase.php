<?php

namespace App\Core\Application\UseCase;

use App\Core\Application\DTO\AdmissionPeriodDTO;
use App\Core\Application\DTO\DepartmentDTO;
use App\Core\Application\UseCase\Interfaces\Persistence\IAdmissionPeriodRepository;
use Psr\Log\LoggerInterface;

class AdmissionPeriodUseCase
{
    public function __construct(private IAdmissionPeriodRepository $admissionPeriodRepository, private LoggerInterface $logger)
    {
    }

    public function getCurrentAdmissionPeriod(DepartmentDTO $department): ?AdmissionPeriodDTO
    {
        $admissionPeriod = $this->admissionPeriodRepository->findActiveByDepartmentId($department->getId());
        if ($admissionPeriod === null) {
            $this->logger->info("[{method}] Trying to get current admission period for department with id {$department->getId()}, but it does not exist", ['method' => __METHOD__]);
            return null;
        }
        return AdmissionPeriodDTO::createFromEntity($admissionPeriod);
    }

    public function getAllAdmissionPeriods(): array
    {
        $admissionPeriods = $this->admissionPeriodRepository->findAll();
        $admissionPeriodDTOs = [];
        foreach ($admissionPeriods as $admissionPeriod) {
            $admissionPeriodDTOs[] = AdmissionPeriodDTO::createFromEntity($admissionPeriod);
        }
        return $admissionPeriodDTOs;
    }
}
