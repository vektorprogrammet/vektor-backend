<?php

namespace App\Controller;

class ParentsController extends BaseController
{
    public function index()
    {
        return $this->render('/parents/parents.html.twig');
    }
}
