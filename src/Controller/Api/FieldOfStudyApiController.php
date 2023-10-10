<?php

namespace App\Controller\Api;

use App\Core\Application\UseCase\FieldOfStudyUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

#[OA\Tag(name: 'Field of study', description: 'Field of study API endpoints')]
#[Route('/api/field-of-study')]
class FieldOfStudyApiController extends AbstractController
{
    public function __construct(private FieldOfStudyUseCase $fieldOfStudyUseCase)
    {
    }

    #[Route('/department/{departmentId}', name: 'api_field_of_study_by_department_id', methods: ['GET'])]
    public function getFieldOfStudyByDepartmentId(int $departmentId): JsonResponse
    {
        return new JsonResponse($this->fieldOfStudyUseCase->getFieldOfStudyByDepartmentId($departmentId));
    }
}