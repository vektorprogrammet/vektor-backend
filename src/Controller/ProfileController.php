<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Entity\ExecutiveBoardMembership;
use App\Entity\Signature;
use App\Entity\TeamMembership;
use App\Entity\User;
use App\Event\UserEvent;
use App\Form\Type\EditUserPasswordType;
use App\Form\Type\EditUserType;
use App\Form\Type\NewUserType;
use App\Form\Type\UserCompanyEmailType;
use App\Role\Roles;
use App\Service\LogService;
use App\Service\RoleManager;
use App\Service\UserRegistration;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ProfileController extends BaseController
{
    private RoleManager $RoleManager;
    private LogService $logService;
    private EventDispatcherInterface $eventDispatcher;
    private TokenStorageInterface $tokenStorage;
    private RequestStack $requestStack;

    public function __construct(
        RoleManager $roleManager,
        LogService $logService,
        EventDispatcherInterface $eventDispatcher,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack
    )
    {
        $this->RoleManager = $roleManager;
        $this->logService = $logService;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    public function show(): Response
    {
        // Get the user currently signed in
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        // Fetch the assistant history of the user
        $assistantHistory = $em->getRepository(AssistantHistory::class)->findByUser($user);
        // Find the team history of the user
        $teamMemberships = $em->getRepository(TeamMembership::class)->findByUser($user);
        // Find the executive board history of the user
        $executiveBoardMemberships = $em->getRepository(ExecutiveBoardMembership::class)->findByUser($user);

        // Render the view
        return $this->render('profile/profile.html.twig', [
            'user'                      => $user,
            'assistantHistory'          => $assistantHistory,
            'teamMemberships'            => $teamMemberships,
            'executiveBoardMemberships'  => $executiveBoardMemberships,
        ]);
    }

    public function showSpecificProfile(User $user)
    {
        // If the user clicks their own public profile redirect them to their own profile site
        if ($user === $this->getUser()) {
            return $this->redirectToRoute('profile');
        }

        $em = $this->getDoctrine()->getManager();

        // Find the work history of the user
        $teamMemberships = $em->getRepository(TeamMembership::class)->findByUser($user);

        // Find the executive board history of the user
        $executiveBoardMemberships = $em->getRepository(ExecutiveBoardMembership::class)->findByUser($user);

        $isGrantedAssistant = ($this->getUser() !== null && $this->RoleManager->userIsGranted($this->getUser(), Roles::ASSISTANT));

        if (empty($teamMemberships) && empty($executiveBoardMemberships) && !$isGrantedAssistant) {
            throw $this->createAccessDeniedException();
        }

        // Fetch the assistant history of the user
        $assistantHistory = $em->getRepository(AssistantHistory::class)->findByUser($user);

        // Render the view
        return $this->render('profile/profile.html.twig', [
            'user'                      => $user,
            'assistantHistory'          => $assistantHistory,
            'teamMemberships'            => $teamMemberships,
            'executiveBoardMemberships'  => $executiveBoardMemberships,
        ]);
    }

    public function deactivateUser(User $user): RedirectResponse
    {
        $user->setActive(false);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('specific_profile', ['id' => $user->getId()]);
    }

    public function activateUser(User $user): RedirectResponse
    {
        $user->setActive(true);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('specific_profile', ['id' => $user->getId()]);
    }

    public function activateNewUser(Request $request, $newUserCode)
    {
        $user = $this->get(UserRegistration::class)->activateUserByNewUserCode($newUserCode);

        if ($user === null) {
            return $this->render('error/error_message.html.twig', [
                'title'   => 'Koden er ugyldig',
                'message' => 'Ugyldig kode eller brukeren er allerede opprettet',
            ]);
        }

        $form = $this->createForm(NewUserType::class, $user, [
            'validation_groups' => ['username'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
            $this->tokenStorage->setToken($token);
            $this->requestStack->getSession()->set('_security_secured_area', serialize($token));

            $this->logService->info("User $user activated with new user code");

            return $this->redirectToRoute('my_page');
        }

        return $this->render('new_user/create_new_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    public function changeRole(Request $request, User $user): JsonResponse
    {
        $response = [];

        $roleManager = $this->RoleManager;
        $roleName    = $roleManager->mapAliasToRole($request->request->get('role'));

        if (! $roleManager->loggedInUserCanChangeRoleOfUsersWithRole($user, $roleName)) {
            throw new BadRequestHttpException();
        }

        try {
            $user->setRoles([$roleName]);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $response['success'] = true;
        } catch (Exception $e) {
            $response['success'] = false;

            $response['cause'] = 'Kunne ikke endre rettighetsnivå'; // if you want to see the exception message.
        }

        // Send a response to ajax
        return new JsonResponse($response);
    }

    public function downloadCertificate(Request $request, User $user): ?RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        // Fetch the assistant history of the user
        $assistantHistory = $em->getRepository(AssistantHistory::class)->findByUser($user);

        // Find the work history of the user
        $teamMembership = $em->getRepository(TeamMembership::class)->findByUser($user);

        // Find the signature of the user creating the certificate
        $signature = $this->getDoctrine()->getRepository(Signature::class)->findByUser($this->getUser());

        // Find department
        $department = $this->getUser()->getDepartment();

        // Find any additional comment
        $additional_comment = $signature->getAdditionalComment();

        if ($signature === null) {
            return $this->redirectToRoute('certificate_show');
        }

        $html = $this->renderView('certificate/certificate.html.twig', [
            'user'                  => $user,
            'assistantHistory'      => $assistantHistory,
            'teamMembership'        => $teamMembership,
            'signature'             => $signature,
            'additional_comment'    => $additional_comment,
            'department'            => $department,
            'base_dir'              => $this->getParameter('kernel.project_dir') . '/public',
        ]);
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $options->setChroot("/../");

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4');

        $html = preg_replace('/>\s+</', "><", $html);
        $dompdf->loadHtml($html);

        $dompdf->render();

        $dompdf->stream($filename='attest.pdf');

        return null;
    }

    public function editProfileInformation(Request $request)
    {
        $user            = $this->getUser();
        $oldCompanyEmail = $user->getCompanyEmail();

        $form = $this->createForm(EditUserType::class, $user, [
            'department'        => $user->getDepartment(),
            'validation_groups' => ['edit_user'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->eventDispatcher->dispatch(new UserEvent($user, $oldCompanyEmail), UserEvent::EDITED);

            return $this->redirect($this->generateUrl('profile'));
        }

        return $this->render('profile/edit_profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    public function editProfilePassword(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(EditUserPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('profile'));
        }

        return $this->render('profile/edit_profile_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    public function editProfileInformationAdmin(Request $request, User $user)
    {
        $form            = $this->createForm(EditUserType::class, $user, [
            'department' => $user->getDepartment(),
        ]);
        $oldCompanyEmail = $user->getCompanyEmail();

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->eventDispatcher->dispatch(new UserEvent($user, $oldCompanyEmail), UserEvent::EDITED);

            return $this->redirect($this->generateUrl('specific_profile', ['id' => $user->getId()]));
        }

        return $this->render('profile/edit_profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    public function editCompanyEmail(Request $request, User $user)
    {
        $oldCompanyEmail = $user->getCompanyEmail();
        $form            = $this->createForm(UserCompanyEmailType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->eventDispatcher->dispatch(new UserEvent($user, $oldCompanyEmail), UserEvent::COMPANY_EMAIL_EDITED);

            return $this->redirectToRoute('specific_profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/edit_company_email.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
