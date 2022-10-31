<?php

namespace App\Controller;

use App\Entity\Application;
use App\Role\Roles;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'login/login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error' => $error,
            )
        );
    }

    /**
     * @return RedirectResponse
     * @throws NonUniqueResultException
     */
    public function loginRedirect(): RedirectResponse
    {
        if ($this->get('security.authorization_checker')->isGranted(Roles::TEAM_MEMBER)) {
            return $this->redirectToRoute('control_panel');
        } elseif ($this->doctrine->getRepository(Application::class)->findActiveByUser($this->getUser())) {
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
