<?php

namespace App\Core\Application\UseCase;

use App\Core\Application\DTO\DepartmentDTO;
use App\Core\Application\Service\AuthorizationService;
use App\Core\Application\UseCase\Interfaces\Persistence\IDepartmentRepository;
use App\Core\Domain\Entity\User;
use Psr\Log\LoggerInterface;

class DepartmentUseCase
{
    public function __construct(
        private IDepartmentRepository $departmentRepository,
        private LoggerInterface $logger,
        private AuthorizationService $authorizationService
    ) {
    }

    public function createDepartment(DepartmentDTO $departmentDTO, ?User $user): ?DepartmentDTO
    {
        if (!$this->authorizationService->userHasPermission($user, 'Department::Create')) {
            $this->logger->warning("[{method}] User with id {$user->getId()} tried to create a department, but does not have permission", ['method' => __METHOD__]);

            return null;
        }

        $department = $departmentDTO->toEntity();

        if (!$department->isValid()) {
            $this->logger->warning("[{method}] User with id {$user->getId()} tried to create a department, but the data was invalid", ['method' => __METHOD__, 'department' => $departmentDTO]);

            return null;
        }

        $this->departmentRepository->save($department);

        return DepartmentDTO::createFromEntity($department);
    }

    public function getAllDepartments(): array
    {
        $departments = $this->departmentRepository->findAll();
        $departmentDTOs = [];
        foreach ($departments as $department) {
            $departmentDTOs[] = DepartmentDTO::createFromEntity($department);
        }

        return $departmentDTOs;
    }

    public function getDepartmentById(int $id): ?DepartmentDTO
    {
        $department = $this->departmentRepository->findById($id);

        if ($department === null) {
            $this->logger->info("[{method}] Trying to get department with id {$id}, but it does not exist", ['method' => __METHOD__]);

            return null;
        }

        return DepartmentDTO::createFromEntity($department);
    }
}
