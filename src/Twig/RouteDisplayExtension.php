<?php

namespace App\Twig;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouteDisplayExtension extends AbstractExtension
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_path', $this->getPath(...)),
        ];
    }

    /**
     * Gets the path of the given route name.
     *
     * @return string The path of the route
     */
    public function getPath(string $name): string
    {
        if (!$this->isRoute($name)) {
            return $name;
        }

        return $this->router->getRouteCollection()->get($name)->getPath();
    }

    private function isRoute(string $name): bool
    {
        return $this->router->getRouteCollection()->get($name) !== null;
    }
}
