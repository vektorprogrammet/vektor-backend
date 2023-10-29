<?php

namespace App\Controller\Api;

use App\Core\Application\DTO\FieldOfStudyDTO;
use App\Core\Application\UseCase\FieldOfStudyUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Field of study', description: 'Field of study API endpoints')]
class FieldOfStudyApiController extends AbstractController
{
    public function __construct(private FieldOfStudyUseCase $fieldOfStudyUseCase)
    {
    }

    #[OA\Response(response: 200, description: 'Field of study found', content: new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: FieldOfStudyDTO::class))))]
    #[Route('/api/department/{departmentId}/field-of-study', name: 'api_field_of_study_by_department_id', methods: ['GET'])]
    public function getFieldOfStudyByDepartmentId(int $departmentId): JsonResponse
    {
        return new JsonResponse($this->fieldOfStudyUseCase->getFieldOfStudyByDepartmentId($departmentId));
    }
}
