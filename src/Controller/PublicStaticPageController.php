<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PublicStaticPageController extends AbstractController
{
    // AboutController
    public function showAboutPage(): Response
    {
        return $this->render('about/about_vektor.html.twig');
    }

    // TeacherController
    public function showTeacherPage(): Response
    {
        return $this->render('teacher/index.html.twig');
    }

    // ParentsController
    public function showParentsPage(): Response
    {
        return $this->render('/parents/parents.html.twig');
    }
}
