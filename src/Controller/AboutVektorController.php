<?php

namespace App\Controller;

class AboutVektorController extends BaseController
{
    public function showAction()
    {
        return $this->render('about/about_vektor.html.twig');
    }
}
