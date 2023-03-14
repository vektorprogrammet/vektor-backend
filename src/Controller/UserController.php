<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\AssistantHistory;
use App\Service\ApplicationManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseController
{
    public function __construct(
        private readonly ApplicationManager $applicationManager,
        private readonly ManagerRegistry $doctrine
    ) {
    }

    public function myPage(): Response
    {
        $user = $this->getUser();

        $department = $user->getDepartment();
        $semester = $this->getCurrentSemester();
        $admissionPeriod = $this->doctrine
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        $activeApplication = null;
        if (null !== $admissionPeriod) {
            $activeApplication = $this->doctrine
                ->getRepository(Application::class)
                ->findByUserInAdmissionPeriod($user, $admissionPeriod);
        }

        $applicationStatus = null;
        if (null !== $activeApplication) {
            $applicationStatus = $this->applicationManager->getApplicationStatus($activeApplication);
        }
        $activeAssistantHistories = $this->doctrine->getRepository(AssistantHistory::class)->findActiveAssistantHistoriesByUser($user);

        return $this->render('my_page/my_page.html.twig', [
            'active_application' => $activeApplication,
            'application_status' => $applicationStatus,
            'active_assistant_histories' => $activeAssistantHistories,
        ]);
    }

    public function myPartner(): Response
    {
        if (!$this->getUser()->isActive()) {
            throw $this->createAccessDeniedException();
        }
        $activeAssistantHistories = $this->doctrine->getRepository(AssistantHistory::class)->findActiveAssistantHistoriesByUser($this->getUser());
        if (empty($activeAssistantHistories)) {
            throw $this->createNotFoundException();
        }

        $partnerInformations = [];
        $partnerCount = 0;

        foreach ($activeAssistantHistories as $activeHistory) {
            $schoolHistories = $this->doctrine->getRepository(AssistantHistory::class)->findActiveAssistantHistoriesBySchool($activeHistory->getSchool());
            $partners = [];

            foreach ($schoolHistories as $sh) {
                if ($sh->getUser() === $this->getUser()) {
                    continue;
                }
                if ($sh->getDay() !== $activeHistory->getDay()) {
                    continue;
                }
                if ($activeHistory->activeInGroup(1) && $sh->activeInGroup(1) ||
                    $activeHistory->activeInGroup(2) && $sh->activeInGroup(2)) {
                    $partners[] = $sh;
                    ++$partnerCount;
                }
            }
            $partnerInformations[] = [
                'school' => $activeHistory->getSchool(),
                'assistantHistory' => $activeHistory,
                'partners' => $partners,
            ];
        }

        $semester = $this->getCurrentSemester();

        return $this->render('user/my_partner.html.twig', [
            'partnerInformations' => $partnerInformations,
            'partnerCount' => $partnerCount,
            'semester' => $semester,
        ]);
    }
}
