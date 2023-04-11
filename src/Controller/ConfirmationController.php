<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ConfirmationController extends AbstractController
{
    public function show(): Response
    {
        return $this->render('confirmation/confirmation.html.twig');
    }
}
