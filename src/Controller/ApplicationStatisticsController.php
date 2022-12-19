<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Service\ApplicationData;
use App\Service\AssistantHistoryData;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationStatisticsController extends BaseController
{
    public function __construct(
        private readonly AssistantHistoryData $AssistantHistoryData,
        private readonly ApplicationData $ApplicationData,
        private readonly ManagerRegistry $doctrine
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function show(Request $request): Response
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->doctrine
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        $assistantHistoryData = $this->AssistantHistoryData;
        $assistantHistoryData->setSemester($semester)->setDepartment($department);

        $applicationData = $this->ApplicationData;
        if ($admissionPeriod !== null) {
            $applicationData->setAdmissionPeriod($admissionPeriod);
        }

        return $this->render('statistics/statistics.html.twig', [
            'applicationData' => $applicationData,
            'assistantHistoryData' => $assistantHistoryData,
            'semester' => $semester,
            'department' => $department,
        ]);
    }
}
