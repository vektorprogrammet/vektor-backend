<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Service\SbsData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControlPanelController extends BaseController
{
    private SbsData $sbsData;

    public function __construct(SbsData $sbsData)
    {
        $this->sbsData=$sbsData;
    }

    /**
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $admissionPeriod = $this->getDoctrine()->getRepository(AdmissionPeriod::class)
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
