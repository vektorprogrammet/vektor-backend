<?php

namespace App\Controller;

class PublicStaticPageController extends BaseController {

    // AboutVektorController
    public function showAboutPage()
    {
        return $this->render('about/about_vektor.html.twig');
    }

    // TeacherController
    public function showTeacherPage()
    {
        return $this->render('teacher/index.html.twig');
    }

    // ParentsController
    public function showParentsPage()
    {
        return $this->render('/parents/parents.html.twig');
    }

}



