<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\AdmissionSubscriber;
use App\Entity\Application;
use App\Service\AdmissionStatistics;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StandController extends BaseController
{
    private AdmissionStatistics $AdmissionStatistics;
    protected ManagerRegistry $doctrine;

    public function __construct(AdmissionStatistics $admissionStatistics,
                                ManagerRegistry $doctrine)
    {
        parent::__construct($doctrine);
        $this->AdmissionStatistics=$admissionStatistics;
        $this->doctrine = $doctrine;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function index(Request $request): Response
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        $admissionStatistics = $this->AdmissionStatistics;

        $subscribers = $this->doctrine->getRepository(AdmissionSubscriber::class)->findFromWebByDepartment($department);
        $subscribersInDepartmentAndSemester = $this->doctrine->getRepository(AdmissionSubscriber::class)
            ->findFromWebByDepartmentAndSemester($department, $semester);
        $subData = $admissionStatistics->generateGraphDataFromSubscribersInSemester($subscribersInDepartmentAndSemester, $semester);

        $applications = $this->doctrine->getRepository(Application::class)->findByDepartment($department);
        $admissionPeriod = $this->doctrine->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsInSemester = [];
        $appData = null;
        if ($admissionPeriod !== null) {
            $applicationsInSemester = $this->doctrine
                ->getRepository(Application::class)
                ->findByAdmissionPeriod($admissionPeriod);
            $appData = $admissionStatistics->generateGraphDataFromApplicationsInAdmissionPeriod($applicationsInSemester, $admissionPeriod);
        }


        return $this->render('stand_admin/stand.html.twig', [
            'department' => $department,
            'semester' => $semester,
            'subscribers' => $subscribers,
            'subscribers_in_semester' => $subscribersInDepartmentAndSemester,
            'subData' => $subData,
            'applications' => $applications,
            'applications_in_semester' => $applicationsInSemester,
            'appData' => $appData,
        ]);
    }
}
