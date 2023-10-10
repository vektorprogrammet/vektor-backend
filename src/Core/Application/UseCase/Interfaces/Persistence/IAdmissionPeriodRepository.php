<?php

namespace App\Core\Application\UseCase\Interfaces\Persistence;

use App\Core\Domain\Entity\AdmissionPeriod;

interface IAdmissionPeriodRepository
{
    public function findById(int $id): ?AdmissionPeriod;
    public function findAll();
    public function findActiveByDepartmentId(int $departmentId): ?AdmissionPeriod;
    public function save(AdmissionPeriod $admissionPeriod): void;
    public function delete(AdmissionPeriod $admissionPeriod): void;
}