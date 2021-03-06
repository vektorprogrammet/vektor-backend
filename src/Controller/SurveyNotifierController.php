<?php
namespace App\Controller;

use App\Entity\SurveyNotificationCollection;
use App\Entity\UserGroupCollection;
use App\Form\Type\SurveyNotifierType;
use App\Service\SurveyNotifier;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SurveyNotifierController extends BaseController
{


    /**
     * @param Request $request
     * @param SurveyNotificationCollection|null $surveyNotificationCollection
     * @return Response
     */
    public function createSurveyNotifier(Request $request, SurveyNotificationCollection $surveyNotificationCollection = null)
    {
        $isUserGroupCollectionEmpty = empty($this->getDoctrine()->getManager()->getRepository(UserGroupCollection::class)->findAll());
        if ($isUserGroupCollectionEmpty) {
            $this->addFlash("danger", "Brukergruppesamling må lages først");
            return $this->redirect($this->generateUrl('survey_notifiers'));
        }

        if ($isCreate = $surveyNotificationCollection === null) {
            $surveyNotificationCollection = new SurveyNotificationCollection();
        }
        $canEdit = !$surveyNotificationCollection->isActive();

        $form = $this->createForm(SurveyNotifierType::class, $surveyNotificationCollection, array(
            'canEdit' => $canEdit,
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('preview')->isClicked()) {
                $subject = $surveyNotificationCollection->getEmailSubject();
                $emailType = $surveyNotificationCollection->getEmailType();
                $view = 'survey/email_notification.html.twig';
                if ($emailType === 1) {
                    $view = 'survey/default_assistant_survey_notification_email.html.twig';
                } elseif ($emailType === 2) {
                    $view = 'survey/personal_email_notification.html.twig';
                    $subject = "Hvordan var det på "."Blussvoll"."?";
                }
                return $this->render(
                    $view,
                    array(
                        'title' => $subject,
                        'firstname' => $this->getUser()->getFirstName(),
                        'route' => $this->generateUrl('survey_show', ['id' => $surveyNotificationCollection->getSurvey()->getId()], RouterInterface::ABSOLUTE_URL),
                        'day' => "Mandag",
                        'mainMessage' => $surveyNotificationCollection->getEmailMessage(),
                        'endMessage' => $surveyNotificationCollection->getEmailEndMessage(),
                        'school' => "Blussvoll",
                        'fromName' => $surveyNotificationCollection->getEmailFromName(),
                    )
                );
            }

            $this->get(SurveyNotifier::class)->initializeSurveyNotifier($surveyNotificationCollection);
            return $this->redirect($this->generateUrl('survey_notifiers'));
        }

        return $this->render('survey/notifier_create.html.twig', array(
            'form' => $form->createView(),
            'surveyNotificationCollection' => $surveyNotificationCollection,
            'isCreate' => $isCreate,
            'isUserGroupCollectionEmpty' => $isUserGroupCollectionEmpty,
        ));
    }


    public function surveyNotificationCollections()
    {
        $surveyNotificationCollections =$this->getDoctrine()->getManager()->getRepository(SurveyNotificationCollection::class)->findAll();

        return $this->render('survey/notifiers.html.twig', array(
             'surveyNotificationCollections' => $surveyNotificationCollections,
         ));
    }


    public function sendSurveyNotifications(SurveyNotificationCollection $surveyNotificationCollection)
    {
        if ($surveyNotificationCollection->getTimeOfNotification() > new DateTime() || $surveyNotificationCollection->isAllSent()) {
            throw new AccessDeniedException();
        }
        $this->get(SurveyNotifier::class)->sendNotifications($surveyNotificationCollection);

        if ($surveyNotificationCollection->isAllSent()) {
            $this->addFlash("success", "Sendt");
            $response['success'] = true;
        } else {
            $this->addFlash("warning", "Alle ble ikke sendt");
            $response['success'] = false;
        }

        return new JsonResponse($response);
    }



    public function deleteSurveyNotifier(SurveyNotificationCollection $surveyNotificationCollection)
    {
        if ($surveyNotificationCollection->isActive()) {
            throw new AccessDeniedException();
        }

        $this->getDoctrine()->getManager()->remove($surveyNotificationCollection);
        $response['success'] = true;
        return new JsonResponse($response);
    }
}
