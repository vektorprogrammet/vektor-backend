<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AbstractController
{

    #[Route('/kontrollpanel/feedback', name: 'feedback_admin_index')]
    public function index(): Response
    {
        return $this->render('feedback_admin/feedback_admin_index.html.twig', [
            'title' => 'Feedback',
        ]);
    }
}
