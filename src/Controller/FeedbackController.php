<?php
namespace App\Controller;


class FeedbackController extends BaseController
{
    public function index()
    {
        return $this->render('feedback_admin/feedback_admin_index.html.twig', array(
            'title' => 'Feedback'
        ));
    }

}
