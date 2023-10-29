<?php

namespace App\Controller\Api;

use App\Core\Application\DTO\SchoolDTO;
use App\Core\Application\UseCase\SchoolUseCase;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'School', description: 'School API endpoints')]
class SchoolApiController extends AbstractController
{
    public function __construct(private readonly SchoolUseCase $schoolUseCase)
    {
    }

    #[OA\Response(response: 200, description: 'Schools found', content: new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: SchoolDTO::class))))]
    #[Route('/api/department/{departmentId}/schools', name: 'api_schools_by_department_id', methods: ['GET'])]
    public function getSchoolsByDepartment(int $departmentId): JsonResponse
    {
        return new JsonResponse($this->schoolUseCase->getSchoolsByDepartmentId($departmentId));
    }
}
