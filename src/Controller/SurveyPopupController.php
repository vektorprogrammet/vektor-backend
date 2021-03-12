<?php


namespace App\Controller;

use App\Entity\Semester;
use App\Entity\Survey;
use App\Role\Roles;
use App\Service\RoleManager;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SurveyPopupController extends AbstractController
{
    public function nextSurveyAction()
    {
        $survey = null;
        $user = $this->getUser();
        $userShouldSeePopUp = $user !== null &&
            $this->get(RoleManager::class)->userIsGranted($user, Roles::TEAM_MEMBER) &&
            !$user->getReservedFromPopUp() &&
            $user->getLastPopUpTime()->diff(new DateTime())->days >= 1;

        if ($userShouldSeePopUp) {
            $semester = $this->getDoctrine()->getRepository(Semester::class)->findCurrentSemester();

            $surveys = $this->getDoctrine()
                ->getRepository(Survey::class)
                ->findAllNotTakenByUserAndSemester($this->getUser(), $semester);

            if (!empty($surveys)) {
                $survey = end($surveys);
            }
        }


        $routeName = $this->container->get('request_stack')->getMasterRequest()->get('_route');
        if (strpos($routeName, "survey_show") !== false) {
            return new Response();
        }
        return $this->render(
            "base/popup_lower.twig",
            array('survey' => $survey)
        );
    }
}
