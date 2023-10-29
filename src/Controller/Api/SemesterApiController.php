<?php

namespace App\Controller\Api;

use App\Core\Application\UseCase\SemesterUseCase;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Semester', description: 'Semester API endpoints')]
#[Route('/api/semester')]
class SemesterApiController extends AbstractController
{
    public function __construct(private SemesterUseCase $semesterUseCase)
    {
    }

    /**
     * Get the current active semester.
     */
    #[Route('/current', name: 'api_semester_current', methods: ['GET'])]
    public function getCurrentSemester(): JsonResponse
    {
        return new JsonResponse($this->semesterUseCase->getCurrentSemester());
    }

    /**
     * List all semesters.
     */
    #[Route('/all', name: 'api_semester_all', methods: ['GET'])]
    public function getAllSemesters(): JsonResponse
    {
        return new JsonResponse($this->semesterUseCase->getAllSemesters());
    }
}
