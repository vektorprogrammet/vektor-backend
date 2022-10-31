<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Service\SbsData;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControlPanelController extends BaseController
{
    private SbsData $sbsData;
    protected ManagerRegistry $doctrine;

    public function __construct(SbsData $sbsData, ManagerRegistry $doctrine)
    {
        parent::__construct($doctrine);
        $this->sbsData = $sbsData;
        $this->doctrine = $doctrine;

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

        $admissionPeriod = $this->doctrine->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        // Return the view to be rendered
        return $this->render('control_panel/index.html.twig', array(
            'admissionPeriod' => $admissionPeriod,
        ));
    }

    public function showSBS(): Response
    {
        $sbsData = $this->sbsData;
        $currentAdmissionPeriod = $this->getUser()->getDepartment()->getCurrentAdmissionPeriod();

        if ($currentAdmissionPeriod) {
            $sbsData->setAdmissionPeriod($currentAdmissionPeriod);
        }

        // Return the view to be rendered
        return $this->render('control_panel/sbs.html.twig', array(
            'data' => $sbsData,
        ));
    }
}
