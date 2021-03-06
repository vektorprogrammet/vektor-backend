<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SsoController extends BaseController
{
    public function login(Request $request)
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
            $user = $this->getDoctrine()->getRepository(User::class)->findByUsernameOrEmail($username);
        } catch (NoResultException $e) {
            $response->setStatusCode(401);
            $response->setContent('Username does not exist');
            return $response;
        }

        $validPassword = $this->get('security.password_encoder')->isPasswordValid($user, $password);
        if (!$validPassword) {
            $response->setStatusCode(401);
            $response->setContent('Wrong password');
            return $response;
        }

        $activeInTeam = count($user->getActiveMemberships()) > 0;
        if (!$activeInTeam) {
            $response->setStatusCode(401);
            $response->setContent('User does not have any active team memberships');
            return $response;
        }

        return new JsonResponse([
            'name' => $user->getFullName(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'companyEmail' => $user->getCompanyEmail(),
        ]);
    }
}
