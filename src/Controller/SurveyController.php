<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * SurveyController is the controller responsible for survey s,
 * such as showing, assigning and conducting surveys.
 */
class SurveyController extends BaseController
{
    public function showSurveys(): Response
    {
        return $this->render('survey/surveys.html.twig');
    }
}
