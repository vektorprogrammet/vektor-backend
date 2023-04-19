<?php

namespace App\Controller;

use App\Entity\AdmissionSubscriber;
use App\Entity\Department;
use App\Form\Type\AdmissionSubscriberType;
use App\Service\AdmissionNotifier;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdmissionSubscriberController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine, private readonly AdmissionNotifier $admissionNotifier)
    {
    }

    public function subscribePage(Request $request, Department $department): RedirectResponse|Response
    {
        $subscriber = new AdmissionSubscriber();
        $subscriber->setDepartment($department);

        $form = $this->createForm(AdmissionSubscriberType::class, $subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->admissionNotifier->createSubscription($department, $subscriber->getEmail(), $subscriber->getInfoMeeting());
                $this->addFlash('success', $subscriber->getEmail() . ' har blitt meldt på interesselisten. Du vil få en e-post når opptaket starter');
            } catch (\InvalidArgumentException) {
                $this->addFlash('danger', 'Kunne ikke melde ' . $subscriber->getEmail() . ' på interesselisten. Vennligst prøv igjen.');
            }

            return $this->redirectToRoute('interest_list', ['shortName' => $department->getShortName()]);
        }

        return $this->render('admission_subscriber/subscribe_page.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $email = $request->request->get('email');
        $departmentId = $request->request->get('department');
        $infoMeeting = filter_var($request->request->get('infoMeeting'), FILTER_VALIDATE_BOOLEAN);
        if (!$email || !$departmentId) {
            return new JsonResponse('Email or department missing', 400);
        }
        $department = $this->doctrine->getRepository(Department::class)->find($departmentId);
        if (!$department) {
            return new JsonResponse('Invalid department', 400);
        }

        try {
            $this->admissionNotifier->createSubscription($department, $email, $infoMeeting);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse($e->getMessage(), 400);
        }

        return new JsonResponse(null, 201);
    }

    public function unsubscribe($code): RedirectResponse
    {
        $subscriber = $this->doctrine
            ->getRepository(AdmissionSubscriber::class)
            ->findByUnsubscribeCode($code);

        $this->addFlash('title', 'Opptaksvarsel - Avmelding');
        if (!$subscriber) {
            $this->addFlash('message', 'Du vil ikke lengre motta varsler om opptak');
        } else {
            $email = $subscriber->getEmail();
            $this->addFlash('message', "Du vil ikke lengre motta varsler om opptak på $email");
            $em = $this->doctrine->getManager();
            $em->remove($subscriber);
            $em->flush();
        }

        return $this->redirectToRoute('confirmation');
    }
}
