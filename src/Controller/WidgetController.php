<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\Receipt;
use App\Entity\Survey;
use App\Entity\User;
use App\Service\AdmissionStatistics;
use App\Service\Sorter;
use App\Utils\ReceiptStatistics;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WidgetController extends BaseController
{
    private $sorter;
    private $admissionStatistics;

    public function __construct(Sorter $sorter, AdmissionStatistics $admissionStatistics)
    {
        $this->sorter=$sorter;
        $this->admissionStatistics=$admissionStatistics;
    }
    /**
     * @param Request $request
     * @return Response|null
     */
    public function interviews(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $admissionPeriod = $this->getDoctrine()->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsAssignedToUser = [];

        if ($admissionPeriod !== null) {
            $applicationRepo = $this->getDoctrine()->getRepository(Application::class);
            $applicationsAssignedToUser = $applicationRepo->findAssignedByUserAndAdmissionPeriod($this->getUser(), $admissionPeriod);
        }

        return $this->render('widgets/interviews_widget.html.twig', ['applications' => $applicationsAssignedToUser]);
    }

    public function receipts()
    {
        $usersWithReceipts = $this->getDoctrine()->getRepository(User::class)->findAllUsersWithReceipts();
        $sorter = $this->sorter;

        $sorter->sortUsersByReceiptSubmitTime($usersWithReceipts);
        $sorter->sortUsersByReceiptStatus($usersWithReceipts);

        $pendingReceipts = $this->getDoctrine()->getRepository(Receipt::class)->findByStatus(Receipt::STATUS_PENDING);
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
    public function applicationGraph(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);
        $appData = null;

        $admissionStatistics = $this->admissionStatistics;

        $admissionPeriod = $this->getDoctrine()->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationsInSemester = [];
        if ($admissionPeriod !== null) {
            $applicationsInSemester = $this->getDoctrine()
                ->getRepository(Application::class)
                ->findByAdmissionPeriod($admissionPeriod);
            $appData = $admissionStatistics->generateCumulativeGraphDataFromApplicationsInAdmissionPeriod($applicationsInSemester, $admissionPeriod);
        }

        return $this->render('widgets/application_graph_widget.html.twig', [
            'appData' => $appData,
            'semester' => $semester,
        ]);
    }


    /**
     * @param Request $request
     * @return Response|null
     */
    public function availableSurveys(Request $request)
    {
        $semester = $this->getSemesterOrThrow404($request);

        $surveys = [];
        if ($semester !== null) {
            $surveys = $this->getDoctrine()
                ->getRepository(Survey::class)
                ->findAllNotTakenByUserAndSemester($this->getUser(), $semester);
        }

        return $this->render('widgets/available_surveys_widget.html.twig', [
            'availableSurveys' => $surveys,
        ]);
    }
}
