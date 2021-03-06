<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Service\ApplicationData;
use App\Service\AssistantHistoryData;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationStatisticsController extends BaseController
{
    private $AssistantHistoryData;
    private $ApplicationData;

    public function __construct(AssistantHistoryData $assistantHistoryData, ApplicationData $applicationData)
    {
        $this->AssistantHistoryData=$assistantHistoryData;
        $this->ApplicationData=$applicationData;
    }
    /**
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->getDoctrine()
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        $assistantHistoryData = $this->AssistantHistoryData;
        $assistantHistoryData->setSemester($semester)->setDepartment($department);

        $applicationData = $this->ApplicationData;
        if ($admissionPeriod !== null) {
            $applicationData->setAdmissionPeriod($admissionPeriod);
        }

        return $this->render('statistics/statistics.html.twig', array(
            'applicationData' => $applicationData,
            'assistantHistoryData' => $assistantHistoryData,
            'semester' => $semester,
            'department' => $department,
        ));
    }
}
