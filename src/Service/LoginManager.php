<?php

namespace App\Service;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class LoginManager
{
    /**
     * LoginManager constructor.
     */
    public function __construct(private readonly Environment $twig, private readonly AuthenticationUtils $authenticationUtils, private readonly RouterInterface $router)
    {
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
