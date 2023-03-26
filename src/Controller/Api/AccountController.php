<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

class AccountController extends BaseController
{

    public function getCurrentUser(): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(null);
        }

        $user = $this->getUser();

        return new JsonResponse([
            'user' => $user->getUsername()
        ]);
    }

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
