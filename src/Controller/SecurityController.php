<?php

namespace App\Controller;

use App\Entity\Application;
use App\Role\Roles;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

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
        if ($this->authorizationChecker->isGranted(Roles::TEAM_MEMBER)) {
            return $this->redirectToRoute('control_panel');
        } elseif ($this->doctrine->getRepository(Application::class)->findActiveByUser($this->getUser())) {
            return $this->redirectToRoute('my_page');
        }

        return $this->redirectToRoute('profile');
    }

    public function loginCheck(): RedirectResponse
    {
        return $this->redirectToRoute('home');
    }
}
