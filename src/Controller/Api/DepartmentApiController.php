<?php

namespace App\Controller\Api;

use App\Core\Application\DTO\DepartmentDTO;
use App\Core\Application\Service\AuthorizationService;
use App\Core\Application\UseCase\DepartmentUseCase;
use App\Core\Application\UseCase\Interfaces\Persistence\IDepartmentRepository;
use App\Core\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;

#[OA\Tag(name: 'Department', description: 'Department API endpoints')]
#[Route('/api/department')]
class DepartmentApiController extends AbstractController
{

    public function __construct(private DepartmentUseCase $departmentUseCase)
    {
    }

    /**
     * Get all departments
     */
    #[Route('/all', name: 'api_department_all', methods: ['GET'])]
    public function getAllDepartments(): JsonResponse
    {
        return new JsonResponse($this->departmentUseCase->getAllDepartments());
    }

    /**
     * Get a department by id
     */
    #[Route('/{id}', name: 'api_department_by_id', methods: ['GET'])]
    public function getDepartmentById(int $id): JsonResponse
    {
        return new JsonResponse($this->departmentUseCase->getDepartmentById($id));
    }

    /**
     * Create a department
     */
    #[OA\RequestBody("", required: true, description: 'Department data')]
    #[Route('/create', name: 'api_department_create', methods: ['POST'])]
    public function createDepartment(Request $request): JsonResponse
    {
        $departmentDTO = new DepartmentDTO(
            null,
            $request->get('name'),
            $request->get('shortName'),
            $request->get('email'),
            $request->get('logoPath'),
            $request->get('city')
        );

        $currentUser = $this->getUser();
        //Cast from App\Entity\User to App\Core\Domain\Entity\User
        //TODO: Fix this, should not be two different User classes
        $user = new User();
        $user->setRoles($currentUser->getRoles());

        $result = $this->departmentUseCase->createDepartment($departmentDTO, $user);

        if ($result === null) {
            return new JsonResponse(['message' => 'Could not create department'], 400);
        }
        
        return new JsonResponse($result);
    }
    
}