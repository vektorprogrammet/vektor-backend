<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SsoController extends BaseController
{
    public function error($response, $message, $statusCode){
        $response->setStatusCode(401);
        $response->setContent($message);
        return $response;
    }
    public function login(Request $request)
    {
        $response = new JsonResponse();

        $username = $request->get('username');
        $password = $request->get('password');

        if (!$username || !$password) {
            return $this->error($response,'Username or password not provided');
        }

        try {
            $user = $this->getDoctrine()->getRepository(User::class)->findByUsernameOrEmail($username);
        } catch (NoResultException $e) {
            return $this->error($response,'Username does not exist');
        }

        $validPassword = $this->get('security.password_encoder')->isPasswordValid($user, $password);
        if (!$validPassword) {
            return $this->error($response,'Wrong password');
        }

        $activeInTeam = count($user->getActiveMemberships()) > 0;
        if (!$activeInTeam) {
            return $this->error($response,'User does not have any active team memberships');
        }

        return new JsonResponse([
            'name' => $user->getFullName(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'companyEmail' => $user->getCompanyEmail(),
        ]);
    }
}
