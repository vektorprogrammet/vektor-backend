<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class ConfirmationController extends BaseController
{
    /**
     */
    public function show(): Response
    {
        return $this->render('confirmation/confirmation.html.twig');
    }
}
