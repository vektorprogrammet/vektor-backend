<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmationController extends BaseController
{
    /**
     * @Route("/bekreftelse", name="confirmation", methods={"GET"})
     *
     * @return Response
     */
    public function show()
    {
        return $this->render('confirmation/confirmation.html.twig');
    }
}
