<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends BaseController
{
    #[Route('/api/account/user', name: 'api_account_user', methods: ['GET'])]
    public function getCurrentUser(): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(null);
        }

        $user = $this->getUser();

        return new JsonResponse([
            'user' => $user->getUsername(),
        ]);
    }

    #[Route('/api/account/department', name: 'api_account_get_department', methods: ['GET'])]
    public function getDepartmentApi(): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(null);
        }

        $department = $this->getUser()->getDepartment();

        if (!$department) {
            return new JsonResponse(null);
        }

        // This is not a proper DTO, and should be changed, but as we really only need the id for now... :
        $departmentDto = [
            'id' => $department->getId(),
            'name' => $department->getName(),
        ];

        return new JsonResponse($departmentDto);
    }
}
