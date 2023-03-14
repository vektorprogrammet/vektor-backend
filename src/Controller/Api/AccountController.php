<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use App\DataTransferObject\UserDto;
use App\Entity\User;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AccountController extends BaseController
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage,
                                private readonly RequestStack $requestStack,
                                private readonly ManagerRegistry $doctrine)
    {
    }

    public function login(Request $request): JsonResponse
    {
        $response = new JsonResponse();

        $username = $request->get('username');
        $password = $request->get('password');

        if (!$username || !$password) {
            $response->setStatusCode(401);
            $response->setContent('Username or password not provided');

            return $response;
        }

        try {
            $user = $this->doctrine->getRepository(User::class)->findByUsernameOrEmail($username);
        } catch (NoResultException) {
            $response->setStatusCode(401);
            $response->setContent('Username does not exist');

            return $response;
        }

        $validPassword = $this->get('security.password_hasher')->isPasswordValid($user, $password);
        if (!$validPassword) {
            $response->setStatusCode(401);
            $response->setContent('Wrong password');

            return $response;
        }

        $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
        $this->tokenStorage->setToken($token);
        $this->requestStack->getSession()->set('_security_secured_area', serialize($token));

        $mapper = $this->get('bcc_auto_mapper.mapper');
        $mapper->createMap(User::class, UserDto::class);
        $userDto = new UserDto();
        $mapper->map($user, $userDto);

        return new JsonResponse($userDto);
    }

    public function logout(): JsonResponse
    {
        try {
            $this->tokenStorage->setToken(null);

            return new JsonResponse('Logout successful');
        } catch (\Exception $e) {
            $response = new JsonResponse();
            $response->setStatusCode(401);
            $response->setContent($e);

            return $response;
        }
    }

    public function getUser(): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(null);
        }

        $mapper = $this->get('bcc_auto_mapper.mapper');
        $mapper->createMap(User::class, UserDto::class);
        $userDto = new UserDto();
        $mapper->map($this->getUser(), $userDto);

        return new JsonResponse($userDto);
    }

    public function getDepartmentApi(Request $request): JsonResponse
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
