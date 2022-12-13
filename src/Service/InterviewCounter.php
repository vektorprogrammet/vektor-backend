<?php

namespace App\Service;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\InterviewDistribution;

class InterviewCounter
{
    public const YES = 'Ja';
    public const MAYBE = 'Kanskje';
    public const NO = 'Nei';

    /**
     * @param Application[] $applications
     *
     */
    public function count(array $applications, string $suitable): int
    {
        $count = 0;

        foreach ($applications as $application) {
            $interview = $application->getInterview();
            if ($interview === null) {
                continue;
            }

            $suitableAssistant = $interview->getInterviewScore()->getSuitableAssistant();
            if ($suitableAssistant === $suitable) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * @param Application[] $applications
     *
     */
    public function createInterviewDistributions(array $applications, AdmissionPeriod $admissionPeriod): array
    {
        $interviewDistributions = [];

        foreach ($applications as $application) {
            $interviewer = $application->getInterview()->getInterviewer();

            if (!array_key_exists($interviewer->__toString(), $interviewDistributions)) {
                $interviewDistributions[$interviewer->__toString()] = new InterviewDistribution($interviewer, $admissionPeriod);
            }
        }

        return array_values($interviewDistributions);
    }
}
