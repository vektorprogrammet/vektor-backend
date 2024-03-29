<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\AdmissionSubscriber;
use App\Entity\Application;
use App\Service\AdmissionStatistics;
use App\Service\DepartmentSemesterService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StandController extends AbstractController
{
    public function __construct(
        private readonly AdmissionStatistics $AdmissionStatistics,
        private readonly ManagerRegistry $doctrine,
        private readonly DepartmentSemesterService $departmentSemesterService,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/kontrollpanel/stand', name: 'stand', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $department = $this->departmentSemesterService->getDepartmentOrThrow404($request, $user);
        $semester = $this->departmentSemesterService->getSemesterOrThrow404($request);

        $admissionStatistics = $this->AdmissionStatistics;

        $subscribers = $this->doctrine
            ->getRepository(AdmissionSubscriber::class)
            ->findFromWebByDepartment($department);

        $subscribersInDepartmentAndSemester = $this->doctrine
            ->getRepository(AdmissionSubscriber::class)
            ->findFromWebByDepartmentAndSemester($department, $semester);

        $subData = $admissionStatistics->generateGraphDataFromSubscribersInSemester($subscribersInDepartmentAndSemester, $semester);

        $applications = $this->doctrine
            ->getRepository(Application::class)
            ->findByDepartment($department);

        $admissionPeriod = $this->doctrine
            ->getRepository(AdmissionPeriod::class)
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
