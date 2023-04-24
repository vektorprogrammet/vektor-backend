<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SsoController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    #[Route('/sso/login', name: 'sso_login', methods: ['POST'])]
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

        $activeInTeam = (is_countable($user->getActiveMemberships()) ? count($user->getActiveMemberships()) : 0) > 0;
        if (!$activeInTeam) {
            $response->setStatusCode(401);
            $response->setContent('User does not have any active team memberships');

            return $response;
        }

        return new JsonResponse([
            'name' => $user->getFullName(),
            'username' => $user->getUserIdentifier(),
            'email' => $user->getEmail(),
            'companyEmail' => $user->getCompanyEmail(),
        ]);
    }
}
