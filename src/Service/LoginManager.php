<?php

namespace App\Service;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class LoginManager
{
    private Environment $twig;
    private AuthenticationUtils $authenticationUtils;
    private RouterInterface $router;

    /**
     * LoginManager constructor
     */
    public function __construct(
        Environment $twig,
        AuthenticationUtils $authenticationUtils,
        RouterInterface $router
    )
    {
        $this->twig = $twig;
        $this->authenticationUtils = $authenticationUtils;
        $this->router = $router;
    }

    public function renderLogin(string $message, string $redirectPath)
    {
        return $this->twig->render('login/login.html.twig', [
            'last_username' => null,
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'message' => $message,
            'redirect_path' => $this->router->generate($redirectPath),
        ]);
    }
}
