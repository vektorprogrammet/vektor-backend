<?php

namespace App\Controller;

class TeacherController extends BaseController
{
    public function index()
    {
        return $this->render('teacher/index.html.twig');
    }
}
