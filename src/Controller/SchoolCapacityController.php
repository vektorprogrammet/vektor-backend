<?php

namespace App\Controller;

use App\Entity\SchoolCapacity;
use App\Form\Type\SchoolCapacityEditType;
use App\Form\Type\SchoolCapacityType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolCapacityController extends BaseController
{
    public function create(Request $request): RedirectResponse|Response
    {
        $department = $this->getDepartmentOrThrow404($request);
        $currentSemester = $this->getSemesterOrThrow404($request);

        $schoolCapacity = new SchoolCapacity();
        $schoolCapacity->setSemester($currentSemester);
        $schoolCapacity->setDepartment($department);
        $form = $this->createForm(SchoolCapacityType::class, $schoolCapacity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($schoolCapacity);
            $em->flush();

            return $this->redirect($this->generateUrl('school_allocation'));
        }

        return $this->render('school_admin/school_allocate_create.html.twig', [
            'message' => '',
            'form' => $form->createView(),
        ]);
    }

    public function edit(Request $request, SchoolCapacity $capacity)
    {
        $form = $this->createForm(SchoolCapacityEditType::class, $capacity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($capacity);
            $em->flush();

            return $this->redirect($this->generateUrl('school_allocation'));
        }

        return $this->render('school_admin/school_allocate_edit.html.twig', [
            'capacity' => $capacity,
            'form' => $form->createView(),
        ]);
    }
}
