<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicStaticPageController extends AbstractController
{
    #[Route('/omvektor', name: 'about', methods: ['GET'])]
    public function showAboutPage(): Response
    {
        return $this->render('about/about_vektor.html.twig');
    }

    #[Route('/laerere', name: 'teachers', methods: ['GET'])]
    public function showTeacherPage(): Response
    {
        return $this->render('teacher/index.html.twig');
    }

    #[Route('/foreldre', name: 'parents', methods: ['GET'])]
    public function showParentsPage(): Response
    {
        return $this->render('/parents/parents.html.twig');
    }
}
