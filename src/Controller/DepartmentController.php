<?php

namespace App\Controller;

use App\Entity\Department;
use App\Form\Type\CreateDepartmentType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentController extends BaseController
{
    public function show(): Response
    {
        return $this->render('department_admin/index.html.twig', []);
    }

    public function createDepartment(Request $request)
    {
        $department = new Department();

        $form = $this->createForm(CreateDepartmentType::class, $department);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            $this->addFlash("success", "$department ble opprettet");

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function deleteDepartmentById(Department $department): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($department);
        $em->flush();

        $this->addFlash("success", "Avdelingen ble slettet");

        return $this->redirectToRoute("departmentadmin_show");
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $form = $this->createForm(CreateDepartmentType::class, $department);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            $this->addFlash("success", "$department ble oppdatert");

            return $this->redirectToRoute('departmentadmin_show');
        }

        return $this->render('department_admin/create_department.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }
}
