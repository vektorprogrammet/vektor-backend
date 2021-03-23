<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Service\SbsData;
use Symfony\Component\HttpFoundation\Request;

class ControlPanelController extends BaseController
{

    /**
     *
     * @param Request $request
     */
    public function show(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $admissionPeriod = $this->getDoctrine()->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        // Return the view to be rendered
        return $this->render('control_panel/index.html.twig', array(
            'admissionPeriod' => $admissionPeriod,
        ));
    }

    public function showSBS()
    {
        $sbsData = $this->get(SbsData::class);
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
