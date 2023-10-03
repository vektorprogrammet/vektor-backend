<?php

namespace App\Controller\Api;

use App\Core\Application\DTO\DepartmentDTO;
use App\Core\Application\UseCase\AdmissionPeriodUseCase;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Admission Period', description: 'Admission Period API endpoints')]
#[Route('/api')]
class AdmissionPeriodApiController extends AbstractController
{
    public function __construct(private AdmissionPeriodUseCase $admissionPeriodUseCase)
    {
    }

    #[Route('/department/{departmentId}/admission-period/current', name: 'api_admission_period_current', methods: ['GET'])]
    public function getCurrentAdmissionPeriod(int $departmentId): JsonResponse
    {
        $department = new DepartmentDTO($departmentId, null, null, null, null, null);

        return new JsonResponse($this->admissionPeriodUseCase->getCurrentAdmissionPeriod($department));
    }

    #[Route('/admission-period/all', name: 'api_admission_period_all', methods: ['GET'])]
    public function getAllAdmissionPeriods(): JsonResponse
    {
        return new JsonResponse($this->admissionPeriodUseCase->getAllAdmissionPeriods());
    }
}
