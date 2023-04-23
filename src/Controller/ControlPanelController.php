<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Service\SbsData;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ControlPanelController extends BaseController
{
    public function __construct(
        private readonly SbsData $sbsData,
        private readonly ManagerRegistry $doctrine
    ) {
    }

    #[Route('/kontrollpanel', name: 'control_panel', methods: ['GET'])]
    public function show(Request $request): Response
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $admissionPeriod = $this->doctrine
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        // Return the view to be rendered
        return $this->render('control_panel/index.html.twig', [
            'admissionPeriod' => $admissionPeriod,
            'department' => $department,
            'semester' => $semester,
        ]);
    }

    public function showSBS(): Response
    {
        $sbsData = $this->sbsData;
        $currentAdmissionPeriod = $this->getUser()->getDepartment()->getCurrentAdmissionPeriod();

        if ($currentAdmissionPeriod) {
            $sbsData->setAdmissionPeriod($currentAdmissionPeriod);
        }

        // Return the view to be rendered
        return $this->render('control_panel/sbs.html.twig', [
            'data' => $sbsData,
        ]);
    }
}
