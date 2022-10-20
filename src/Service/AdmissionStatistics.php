<?php


namespace App\Service;

use App\Entity\AdmissionPeriod;
use App\Entity\AdmissionSubscriber;
use App\Entity\Application;
use App\Entity\PeriodInterface;
use App\Entity\Semester;
use DateTime;

class AdmissionStatistics
{

    /**
     * @param AdmissionSubscriber[] $subscribers
     * @param Semester $semester
     *
     * @return array
     */
    public function generateGraphDataFromSubscribersInSemester($subscribers, Semester $semester)
    {
        $subData = $this->initializeDataArray($semester);
        return $this->populateSubscriberDataWithSubscribers($subData, $subscribers);
    }

    /**
     * @param Application[] $applications
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return array
     */
    public function generateGraphDataFromApplicationsInAdmissionPeriod(array $applications,
                                                                       AdmissionPeriod $admissionPeriod): array
    {
        $endDate = $admissionPeriod->getEndDate();
        $extraDays = $this->calculatePaddingDays($endDate);

        $appData = $this->initializeDataArray($admissionPeriod, $extraDays);
        return $this->populateApplicationDataWithApplications($appData, $applications);
    }

    /**
     * @param Application[] $applications
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return array
     */
    public function generateCumulativeGraphDataFromApplicationsInAdmissionPeriod(array $applications,
                                                                                 AdmissionPeriod $admissionPeriod): array
    {
        $endDate =  $admissionPeriod->getEndDate();
        $extraDays = $this->calculatePaddingDays($endDate);

        $appData = $this->initializeDataArray($admissionPeriod, $extraDays);
        return $this->populateCumulativeApplicationDataWithApplications($appData, $applications);
    }

    private function initializeDataArray(PeriodInterface $admissionPeriod, int $extraDays = 0): array
    {
        $subData = [];

        $now = new DateTime();
        $days = $admissionPeriod->getStartDate()->diff($now)->days;
        if ($now > $admissionPeriod->getEndDate()) {
            $days = $admissionPeriod->getStartDate()->diff($admissionPeriod->getEndDate())->days;
        }

        $days += $extraDays;

        $start = $admissionPeriod->getStartDate()->format('Y-m-d');
        for ($i = 0; $i < $days; $i++) {
            $date = (new DateTime($start))->modify("+$i days")->format('Y-m-d');
            $subData[$date] = 0;
        }

        return $subData;
    }

    /**
     * @param array $appData
     * @param Application[] $applications
     *
     * @return array
     */
    private function populateApplicationDataWithApplications(array $appData, array $applications): array
    {
        foreach ($applications as $application) {
            $date = $application->getCreated()->format('Y-m-d');
            if (!isset($appData[$date])) {
                $appData[$date] = 0;
            }
            $appData[$date]++;
        }
        ksort($appData);

        return $appData;
    }

    /**
     * @param array $appData
     * @param Application[] $applications
     *
     * @return array
     */
    private function populateCumulativeApplicationDataWithApplications(array $appData, array $applications): array
    {
        $populatedAppData = $this->populateApplicationDataWithApplications($appData, $applications);
        $dates = array_keys($populatedAppData);
        foreach ($populatedAppData as $date => $count) {
            $index = array_search($date, $dates);
            if ($index === false || $index === 0) {
                continue;
            }
            $cumulative = $populatedAppData[$dates[$index-1]];
            $populatedAppData[$date] = $populatedAppData[$date] + $cumulative;
        }

        return $populatedAppData;
    }

    /**
     * @param array $subData
     * @param AdmissionSubscriber[] $subscribers
     *
     * @return array
     */
    private function populateSubscriberDataWithSubscribers(array $subData, array $subscribers): array
    {
        foreach ($subscribers as $subscriber) {
            $date = $subscriber->getTimestamp()->format('Y-m-d');
            if (!isset($subData[$date])) {
                $subData[$date] = 0;
            }
            $subData[$date]++;
        }
        ksort($subData);

        return $subData;
    }

    /**
     * @param DateTime $endDate
     * @return int
     */
    private function calculatePaddingDays(DateTime $endDate): int
    {
        $today = new DateTime();

        if ($today > $endDate) {
            // Add extra padding to chart, maximum 6 days
            $extraDays = $endDate->diff($today)->format("%d");
            $extraDays += 2;
            if ($extraDays > 6) {
                $extraDays = 6;
            }
        } else {
            $extraDays = 0;
        }

        return $extraDays;
    }
}
