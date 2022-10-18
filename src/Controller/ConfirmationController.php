<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmationController extends BaseController
{
    /**
     * @return Response
     */
    public function show()
    {
        return $this->render('confirmation/confirmation.html.twig');
    }
}
