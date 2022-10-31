<?php

namespace App\Controller;

use App\Entity\PasswordReset;
use App\Service\LogService;
use App\Service\PasswordManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\NewPasswordType;
use App\Form\Type\PasswordResetType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PasswordResetController.
 */
class PasswordResetController extends BaseController
{
    private LogService $logService;
    protected ManagerRegistry $doctrine;

    public function __construct(LogService $logService, ManagerRegistry $doctrine)
    {
        parent::__construct($doctrine);
        $this->logService = $logService;
        $this->doctrine = $doctrine;

    }
    /**
     * @param Request $request
     *
     * @return Response
     *
     * Shows the request new password page
     */
    public function show(Request $request): Response
    {
        //Creates new PasswordResetType Form
        $form = $this->createForm(PasswordResetType::class);

        $form->handleRequest($request);

        //Checks if the form is valid
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $passwordReset = $this->get(PasswordManager::class)->createPasswordResetEntity($email);

            if ($passwordReset === null) {
                $errorMsg = "Det finnes ingen brukere med denne e-postadressen";
                $ending   = '@vektorprogrammet.no';
                if (strlen($email) > strlen($ending) && substr($email, strlen($email) - strlen($ending)) === $ending) {
                    $errorMsg = 'Kan ikke resette passord med "@vektorprogrammet.no"-adresse. Prøv din private e-post';
                    $this->logService->info("Password reset rejected: Someone tried to reset password with a company email: $email");
                }
                $this->get('session')->getFlashBag()->add('errorMessage', "<em>$errorMsg</em>");
            } elseif (!$passwordReset->getUser()->isActive()) {
                $errorMsg = "Brukeren med denne e-postadressen er deaktivert. Ta kontakt med it@vektorprogrammet.no for å aktivere brukeren din.";
                $this->get('session')->getFlashBag()->add('errorMessage', "<em>$errorMsg</em>");
                $this->logService->notice("Password reset rejected: Someone tried to reset the password for an inactive account: $email");
            } else {
                $this->logService->info("{$passwordReset->getUser()} requested a password reset");
                $oldPasswordResets = $this->doctrine->getRepository(PasswordReset::class)->findByUser($passwordReset->getUser());
                $em = $this->doctrine->getManager();

                foreach ($oldPasswordResets as $oldPasswordReset) {
                    $em->remove($oldPasswordReset);
                }

                $em->persist($passwordReset);
                $em->flush();

                $this->get(PasswordManager::class)->sendResetCode($passwordReset);

                return $this->redirectToRoute('reset_password_confirmation');
            }
        }
        //Render reset_password twig with the form.
        return $this->render('reset_password/reset_password.html.twig', array('form' => $form->createView()));
    }

    public function showConfirmation(): Response
    {
        return $this->render('reset_password/confirmation.html.twig');
    }

    /**
     * @param $resetCode
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * This function resets stores the new password when the user goes to the url for resetting the password
     */
    public function resetPassword($resetCode, Request $request)
    {
        $passwordManager = $this->get(PasswordManager::class);

        if (!$passwordManager->resetCodeIsValid($resetCode) || $passwordManager->resetCodeHasExpired($resetCode)) {
            return $this->render('error/error_message.html.twig', array(
                'title' => 'Ugyldig kode',
                'message' => "Koden er ugyldig eller utløpt. Gå til <a href='{$this->generateUrl('reset_password')}'>Glemt passord?</a> for å få tilsendt ny link.",
            ));
        }

        $passwordReset = $passwordManager->getPasswordResetByResetCode($resetCode);
        $user = $passwordReset->getUser();

        $form = $this->createForm(NewPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->remove($passwordReset);
            $em->persist($user);
            $em->flush();

            $this->logService->info("{$passwordReset->getUser()} successfully created a new password from the reset link");

            return $this->redirectToRoute('login_route');
        }

        return $this->render('reset_password/new_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
