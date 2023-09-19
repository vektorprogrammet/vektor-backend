<?php

namespace App\Controller;

use App\Entity\Department;
use App\Form\Type\CreateDepartmentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    /**
     * Page showing all departments.
     */
    public function show(): Response
    {
        return $this->render('department_admin/index.html.twig', []);
    }

    /**
     * Create or update department.
     */
    public function createOrUpdateDepartment(Request $request, Department $department = null): RedirectResponse|Response
    {
        $isEdit = $department !== null;
        if (!$isEdit) {
            $department = new Department();
        }

        $form = $this->createForm(CreateDepartmentType::class, $department);
        $form->handleRequest($request);

        // If form submitted and valid, save department
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->persist($department);
            $em->flush();

            $action = $isEdit ? 'oppdatert' : 'opprettet';
            $this->addFlash('success', "$department ble $action");

            return $this->redirectToRoute('departmentadmin_show');
        }

        // Render create/edit department form
        return $this->render('department_admin/create_department.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
            'isEdit' => $isEdit,
        ]);
    }

    /**
     * Delete department by id.
     */
    public function deleteDepartmentById(Department $department): RedirectResponse
    {
        $em = $this->doctrine->getManager();
        $em->remove($department);
        $em->flush();

        $this->addFlash('success', 'Avdelingen ble slettet');

        return $this->redirectToRoute('departmentadmin_show');
    }
}
