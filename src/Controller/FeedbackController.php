<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FeedbackController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('feedback_admin/feedback_admin_index.html.twig', [
            'title' => 'Feedback',
        ]);
    }
}
