<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class ContentModeManager
{
    private RequestStack $requestStack;

    /**
     * ContentModeManager constructor
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function isEditMode()
    {
        return $this->requestStack->getSession('edit-mode', false);
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
