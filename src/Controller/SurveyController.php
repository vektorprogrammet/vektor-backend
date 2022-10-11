<?php

namespace App\Controller;

/**
 * SurveyController is the controller responsible for survey s,
 * such as showing, assigning and conducting surveys.
 */
class SurveyController extends BaseController
{
    public function showSurveys()
    {
        return $this->render('survey/surveys.html.twig');
    }
}
