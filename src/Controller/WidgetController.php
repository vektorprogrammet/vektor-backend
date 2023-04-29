<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\Department;
use App\Entity\Receipt;
use App\Entity\Semester;
use App\Entity\User;
use App\Service\AdmissionStatistics;
use App\Service\DepartmentSemesterService;
use App\Service\Sorter;
use App\Utils\ReceiptStatistics;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WidgetController extends AbstractController
{
    public function __construct(
        private readonly Sorter $sorter,
        private readonly AdmissionStatistics $admissionStatistics,
        private readonly ManagerRegistry $doctrine,
        private readonly DepartmentSemesterService $departmentSemesterService,
    ) {
    }

    public function interviews(Request $request): ?Response
    {
        $user = $this->getUser();
        $department = $this->departmentSemesterService->getDepartmentOrThrow404($request, $user);
        $semester = $this->departmentSemesterService->getSemesterOrThrow404($request);

        $admissionPeriod = $this->doctrine
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        $applicationsAssignedToUser = [];

        if ($admissionPeriod !== null) {
            $applicationsAssignedToUser = $this->doctrine
                ->getRepository(Application::class)
                ->findAssignedByUserAndAdmissionPeriod($this->getUser(), $admissionPeriod);
        }

        return $this->render('widgets/interviews_widget.html.twig', ['applications' => $applicationsAssignedToUser]);
    }

    public function receipts(): Response
    {
        $usersWithReceipts = $this->doctrine
            ->getRepository(User::class)
            ->findAllUsersWithReceipts();

        $sorter = $this->sorter;

        $sorter->sortUsersByReceiptSubmitTime($usersWithReceipts);
        $sorter->sortUsersByReceiptStatus($usersWithReceipts);

        $pendingReceipts = $this->doctrine
            ->getRepository(Receipt::class)
            ->findByStatus(Receipt::STATUS_PENDING);

        $pendingReceiptStatistics = new ReceiptStatistics($pendingReceipts);
        $hasReceipts = !empty($pendingReceipts);

        return $this->render('widgets/receipts_widget.html.twig', [
            'users_with_receipts' => $usersWithReceipts,
            'statistics' => $pendingReceiptStatistics,
            'has_receipts' => $hasReceipts,
        ]);
    }

    public function applicationGraph(Request $request, Department $department, Semester $semester): ?Response
    {
        if (is_null($department)) {
            $user = $this->getUser();
            $department = $this->departmentSemesterService->getDepartmentOrThrow404($request, $user);
        }
        if (is_null($semester)) {
            $semester = $this->departmentSemesterService->getSemesterOrThrow404($request);
        }
        $appData = null;

        $admissionStatistics = $this->admissionStatistics;

        $admissionPeriod = $this->doctrine
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        $applicationsInSemester = [];
        if ($admissionPeriod !== null) {
            $applicationsInSemester = $this->doctrine
                ->getRepository(Application::class)
                ->findByAdmissionPeriod($admissionPeriod);
            $appData = $admissionStatistics->generateCumulativeGraphDataFromApplicationsInAdmissionPeriod($applicationsInSemester, $admissionPeriod);
        }

        return $this->render('widgets/application_graph_widget.html.twig', [
            'appData' => $appData,
            'semester' => $semester,
        ]);
    }
}
