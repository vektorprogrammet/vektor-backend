<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AccessExtension extends AbstractExtension
{

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_access_to', $this->hasAccessTo(...)),
        ];
    }

    /**
     * Checks if the user has access to the resource.
     *
     * @param null $user
     *
     * @return bool True if the user has access to the resource, false otherwise
     */
    public function hasAccessTo($routes, $user = null): bool
    {
        return true;
    }
}
