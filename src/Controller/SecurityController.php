<?php

namespace App\Controller;

use App\Entity\Application;
use App\Role\Roles;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'login/login.html.twig',
            [
                // last username entered by the user
                'last_username' => $lastUsername,
                'error' => $error,
            ]
        );
    }

    /**
     * @throws NonUniqueResultException
     */
    public function loginRedirect(): RedirectResponse
    {
        if ($this->get('security.authorization_checker')->isGranted(Roles::TEAM_MEMBER)) {
            return $this->redirectToRoute('control_panel');
        } elseif ($this->getDoctrine()->getRepository(Application::class)->findActiveByUser($this->getUser())) {
            return $this->redirectToRoute('my_page');
        } else {
            return $this->redirectToRoute('profile');
        }
    }

    public function loginCheck(): RedirectResponse
    {
        return $this->redirectToRoute('home');
    }
}
