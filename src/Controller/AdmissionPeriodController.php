<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\AdmissionPeriod;
use App\Form\Type\EditAdmissionPeriodType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\CreateAdmissionPeriodType;

class AdmissionPeriodController extends BaseController
{
    public function show()
    {
        // Finds the departmentId for the current logged in user
        $department = $this->getUser()->getDepartment();

        return $this->showByDepartment($department);
    }

    public function showByDepartment(Department $department)
    {
        $admissionPeriods = $this->getDoctrine()
            ->getRepository(AdmissionPeriod::class)
            ->findByDepartmentOrderedByTime($department);


        // Renders the view with the variables
        return $this->render('admission_period_admin/index.html.twig', array(
            'admissionPeriods' => $admissionPeriods,
            'departmentName' => $department->getShortName(),
            'department' => $department
        ));
    }

    public function createAdmissionPeriod(Request $request, Department $department)
    {
        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriods = $department->getAdmissionPeriods()->toArray();
        $form = $this->createForm(CreateAdmissionPeriodType::class, $admissionPeriod, [
            'admissionPeriods' => $admissionPeriods
        ]);

        $form->handleRequest($request);

        $exists = $department->getAdmissionPeriods()->exists(function ($key, $value) use ($admissionPeriod) {
            return $value->getSemester() === $admissionPeriod->getSemester();
        });

        if ($exists) {
            $this->addFlash('warning', 'Opptaksperioden ' . $admissionPeriod->getSemester() . ' finnes allerede.');
        }
        if ($form->isSubmitted() && $form->isValid() && !$exists) {
            $admissionPeriod->setDepartment($department);

            $em = $this->getDoctrine()->getManager();
            $em->persist($admissionPeriod);
            $em->flush();

            return $this->redirectToRoute('admission_period_admin_show_by_department', array('id' => $department->getId()));
        }

        // Render the view
        return $this->render('admission_period_admin/create_admission_period.html.twig', array(
            'department' => $department,
            'form' => $form->createView(),
        ));
    }

    public function updateAdmissionPeriod(Request $request, AdmissionPeriod $admissionPeriod)
    {
        $form = $this->createForm(EditAdmissionPeriodType::class, $admissionPeriod);

        // Handle the form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($admissionPeriod);
            $em->flush();

            return $this->redirectToRoute('admission_period_admin_show_by_department', array('id' => $admissionPeriod->getDepartment()->getId()));
        }

        return $this->render('admission_period_admin/edit_admission_period.html.twig', array(
            'form' => $form->createView(),
            'semesterName' => $admissionPeriod->getSemester()->getName(),
            'department' => $admissionPeriod->getDepartment(),
        ));
    }

    public function delete(AdmissionPeriod $admissionPeriod)
    {
        $em = $this->getDoctrine()->getManager();
        $infoMeeting = $admissionPeriod->getInfoMeeting();
        if ($infoMeeting) {
            $em->remove($infoMeeting);
        }
        $em->remove($admissionPeriod);
        $em->flush();

        return $this->redirectToRoute('admission_period_admin_show_by_department', ['id' => $admissionPeriod->getDepartment()->getId()]);
    }
}
