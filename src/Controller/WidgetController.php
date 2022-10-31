<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\Receipt;
use App\Entity\User;
use App\Service\AdmissionStatistics;
use App\Service\Sorter;
use App\Utils\ReceiptStatistics;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WidgetController extends BaseController
{
    private Sorter $sorter;
    private AdmissionStatistics $admissionStatistics;
    protected ManagerRegistry $doctrine;

    public function __construct(Sorter $sorter,
                                AdmissionStatistics $admissionStatistics,
                                ManagerRegistry $doctrine)
    {
        parent::__construct($doctrine);
        $this->sorter=$sorter;
        $this->admissionStatistics=$admissionStatistics;
        $this->doctrine = $doctrine;
    }
    /**
     * @param Request $request
     * @return Response|null
     */
    public function interviews(Request $request): ?Response
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->doctrine->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsAssignedToUser = [];

        if ($admissionPeriod !== null) {
            $applicationRepo = $this->doctrine->getRepository(Application::class);
            $applicationsAssignedToUser = $applicationRepo->findAssignedByUserAndAdmissionPeriod($this->getUser(), $admissionPeriod);
        }

        return $this->render('widgets/interviews_widget.html.twig', ['applications' => $applicationsAssignedToUser]);
    }

    public function receipts(): Response
    {
        $usersWithReceipts = $this->doctrine->getRepository(User::class)->findAllUsersWithReceipts();
        $sorter = $this->sorter;

        $sorter->sortUsersByReceiptSubmitTime($usersWithReceipts);
        $sorter->sortUsersByReceiptStatus($usersWithReceipts);

        $pendingReceipts = $this->doctrine->getRepository(Receipt::class)->findByStatus(Receipt::STATUS_PENDING);
        $pendingReceiptStatistics = new ReceiptStatistics($pendingReceipts);

        $hasReceipts = !empty($pendingReceipts);

        return $this->render('widgets/receipts_widget.html.twig', [
            'users_with_receipts' => $usersWithReceipts,
            'statistics' => $pendingReceiptStatistics,
            'has_receipts' => $hasReceipts,
        ]);
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    public function applicationGraph(Request $request): ?Response
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $appData = null;

        $admissionStatistics = $this->admissionStatistics;

        $admissionPeriod = $this->doctrine->getRepository(AdmissionPeriod::class)
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
