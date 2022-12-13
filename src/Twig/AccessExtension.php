<?php

namespace App\Twig;

use App\Service\AccessControlService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AccessExtension extends AbstractExtension
{
    private $accessControlService;

    /**
     */
    public function __construct(AccessControlService $accessControlService)
    {
        $this->accessControlService = $accessControlService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_access_to', [$this, 'hasAccessTo']),
        ];
    }

    /**
     * Checks if the user has access to the resource.
     *
     *
     * @param null $user
     * @return boolean True if the user has access to the resource, false otherwise
     */
    public function hasAccessTo($routes, $user = null): bool
    {
        return $this->accessControlService->checkAccess($routes, $user);
    }
}
