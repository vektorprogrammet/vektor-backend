<?php

namespace App\Controller\Api;

use App\Core\Application\DTO\AdmissionPeriodDTO;
use App\Core\Application\DTO\DepartmentDTO;
use App\Core\Application\UseCase\AdmissionPeriodUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;

#[OA\Tag(name: 'Admission Period', description: 'Admission Period API endpoints')]
#[Route('/api/admission-period')]
class AdmissionPeriodApiController extends AbstractController
{
    public function __construct(private AdmissionPeriodUseCase $admissionPeriodUseCase)
    {
    }

    #[Route('/current/department/{departmentId}', name: 'api_admission_period_current', methods: ['GET'])]
    public function getCurrentAdmissionPeriod(int $departmentId): JsonResponse
    {
        $department = new DepartmentDTO($departmentId, null, null, null, null, null);
        return new JsonResponse($this->admissionPeriodUseCase->getCurrentAdmissionPeriod($department));
    }

    #[Route('/all', name: 'api_admission_period_all', methods: ['GET'])]
    public function getAllAdmissionPeriods(): JsonResponse
    {
        return new JsonResponse($this->admissionPeriodUseCase->getAllAdmissionPeriods());
    }
}