<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\User;
use App\Form\Type\CreateUserType;
use App\Role\Roles;
use App\Service\UserRegistration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAdminController extends BaseController
{
    public function __construct(private readonly UserRegistration $userRegistration)
    {
    }

    public function createUser(Request $request, Department $department = null)
    {
        if (!$this->isGranted(Roles::TEAM_LEADER) || $department === null) {
            $department = $this->getUser()->getDepartment();
        }

        // Create the user object
        $user = new User();

        $form = $this->createForm(CreateUserType::class, $user, [
            'validation_groups' => ['create_user'],
            'department' => $department,
        ]);

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isSubmitted() && $form->isValid()) {
            $role = Roles::ASSISTANT;
            $user->addRole($role);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->userRegistration->sendActivationCode($user);

            return $this->redirectToRoute('useradmin_show');
        }

        // Render the view
        return $this->render('user_admin/create_user.html.twig', [
            'form' => $form->createView(),
            'department' => $department,
        ]);
    }

    public function show(): Response
    {
        // Finds all the departments
        $activeDepartments = $this->getDoctrine()->getRepository(Department::class)->findActive();

        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        $activeUsers = $this->getDoctrine()->getRepository(User::class)->findAllActiveUsersByDepartment($department);
        $inActiveUsers = $this->getDoctrine()->getRepository(User::class)->findAllInActiveUsersByDepartment($department);

        return $this->render('user_admin/index.html.twig', [
            'activeUsers' => $activeUsers,
            'inActiveUsers' => $inActiveUsers,
            'departments' => $activeDepartments,
            'department' => $department,
        ]);
    }

    public function showUsersByDepartment(Department $department): Response
    {
        // Finds all the departments
        $activeDepartments = $this->getDoctrine()->getRepository(Department::class)->findActive();

        $activeUsers = $this->getDoctrine()->getRepository(User::class)->findAllActiveUsersByDepartment($department);
        $inActiveUsers = $this->getDoctrine()->getRepository(User::class)->findAllInActiveUsersByDepartment($department);

        // Renders the view with the variables
        return $this->render('user_admin/index.html.twig', [
            'activeUsers' => $activeUsers,
            'inActiveUsers' => $inActiveUsers,
            'departments' => $activeDepartments,
            'department' => $department,
        ]);
    }

    public function deleteUserById(User $user)
    {
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Du kan ikke slette deg selv.');
        } elseif ($this->isGranted(ROLES::ADMIN) || $user->getDepartment() === $this->getUser()->getDepartment()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', "$user har blitt slettet.");
        } else {
            throw $this->createAccessDeniedException();
        }
        // Redirect to useradmin page, set department to that of the deleted user
        return $this->redirectToRoute('useradmin_filter_users_by_department', ['id' => $user->getDepartment()->getId()]);
    }

    public function sendActivationMail(User $user)
    {
        $this->userRegistration->sendActivationCode($user);

        return $this->redirectToRoute('useradmin_show');
    }
}
