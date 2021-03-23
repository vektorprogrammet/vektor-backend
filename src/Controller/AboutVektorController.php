<?php

namespace App\Controller;

class AboutVektorController extends BaseController
{
    public function show()
    {
        return $this->render('about/about_vektor.html.twig');
    }
}
