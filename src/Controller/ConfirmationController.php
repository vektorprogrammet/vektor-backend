<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class ConfirmationController extends BaseController
{
    /**
     * @return Response
     */
    public function show(): Response
    {
        return $this->render('confirmation/confirmation.html.twig');
    }
}
