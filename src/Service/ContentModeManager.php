<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class ContentModeManager
{
    /**
     * ContentModeManager constructor.
     */
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function isEditMode()
    {
        return $this->requestStack->getSession();
    }

    public function changeToEditMode()
    {
        return $this->requestStack->getSession()->set('edit-mode', true);
    }

    public function changeToReadMode()
    {
        return $this->requestStack->getSession()->set('edit-mode', false);
    }
}
