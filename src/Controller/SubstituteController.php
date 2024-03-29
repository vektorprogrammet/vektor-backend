<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Form\Type\ModifySubstituteType;
use App\Service\DepartmentSemesterService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SubstituteController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly DepartmentSemesterService $departmentSemesterService,
    ) {
    }

    public function show(Request $request): ?Response
    {
        // No department specified, get the user's department and call showBySemester with
        // either current or latest semester for that department
        $user = $this->getUser();
        $department = $this->departmentSemesterService->getDepartmentOrThrow404($request, $user);
        $semester = $this->departmentSemesterService->getSemesterOrThrow404($request);

        $admissionPeriod = $this->doctrine
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        $substitutes = null;
        if ($admissionPeriod !== null) {
            $substitutes = $this->doctrine
                ->getRepository(Application::class)
                ->findSubstitutesByAdmissionPeriod($admissionPeriod);
        }

        return $this->render('substitute/index.html.twig', [
            'substitutes' => $substitutes,
            'semester' => $semester,
            'department' => $department,
        ]);
    }

    public function showModifyForm(Request $request, Application $application)
    {
        // Only substitutes should be modified with this form
        if (!$application->isSubstitute()) {
            throw new BadRequestHttpException();
        }

        $department = $application->getUser()->getDepartment();

        $form = $this->createForm(ModifySubstituteType::class, $application, [
            'department' => $department,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->persist($application);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('substitute_show', [
                'semester' => $application->getSemester()->getId(),
                'department' => $department->getId(),
            ]));
        }

        return $this->render('substitute/modify_substitute.twig', [
            'application' => $application,
            'form' => $form->createView(),
        ]);
    }

    public function deleteSubstituteById(Application $application): RedirectResponse
    {
        $application->setSubstitute(false);

        $em = $this->doctrine->getManager();
        $em->persist($application);
        $em->flush();

        // Redirect to substitute page, set semester to that of the deleted substitute
        return $this->redirectToRoute('substitute_show', [
            'semester' => $application->getSemester()->getId(),
            'department' => $application->getAdmissionPeriod()->getDepartment()->getid(),
        ]);
    }

    public function createSubstituteFromApplication(Application $application): RedirectResponse
    {
        if ($application->isSubstitute()) {
            // User is already substitute
            throw new BadRequestHttpException();
        }
        $application->setSubstitute(true);

        $em = $this->doctrine->getManager();
        $em->persist($application);
        $em->flush();

        // Redirect to substitute page, set semester to that of the newly added substitute
        return $this->redirectToRoute('substitute_show', [
            'semester' => $application->getSemester()->getId(),
            'department' => $application->getAdmissionPeriod()->getDepartment()->getId(),
        ]);
    }
}
