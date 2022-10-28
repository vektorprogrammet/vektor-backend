<?php
namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;

class FeedbackController extends BaseController
{
    public function index(): Response
    {
        return $this->render('feedback_admin/feedback_admin_index.html.twig', array(
            'title' => 'Feedback'
        ));
    }

}
