<?php


namespace App\Service;

use App\Entity\Application;
use App\Entity\ApplicationStatus;
use App\Entity\InterviewStatusType;

class ApplicationManager
{
    public function getApplicationStatus(Application $application): ApplicationStatus
    {
        $interview = $application->getInterview();
        $user = $application->getUser();
        if ($application->getUser()->isActiveAssistant()) {
            return new ApplicationStatus(
                ApplicationStatus::ASSIGNED_TO_SCHOOL,
                "Tatt opp som vektorassistent",
                "Ta kontakt med dine vektorpartnere og dra ut til skolen"
            );
        } elseif ($user->hasBeenAssistant()) {
            return new ApplicationStatus(
                ApplicationStatus::INTERVIEW_COMPLETED,
                "Søknad mottatt",
                "Siden du har vært assistent tidligere trenger du ikke å møte på intervju. Du vil få en e-post når opptaket er klart."
            );
        } elseif ($interview === null) {
            return new ApplicationStatus(
                ApplicationStatus::APPLICATION_RECEIVED,
                "Søknad mottatt",
                "Vent på å bli invitert til intervju"
            );
        } elseif ($interview->getInterviewed()) {
            return new ApplicationStatus(
                ApplicationStatus::INTERVIEW_COMPLETED,
                "Intervju gjennomført",
                "Søknaden din vurderes av Vektorprogrammet. Du vil få svar på e-post."
            );
        }

        switch ($interview->getInterviewStatus()) {
            case InterviewStatusType::NO_CONTACT:
                return new ApplicationStatus(
                    ApplicationStatus::APPLICATION_RECEIVED,
                    "Søknad mottatt",
                    "Vent på å bli invitert til intervju"
                );
            case InterviewStatusType::REQUEST_NEW_TIME:
                return new ApplicationStatus(
                    ApplicationStatus::APPLICATION_RECEIVED,
                    "Endring av tidspunkt til intervju",
                    "Vent på å få et nytt tidspunkt til intervju"
                );
            case InterviewStatusType::PENDING:
                return new ApplicationStatus(
                    ApplicationStatus::INVITED_TO_INTERVIEW,
                    "Invitert til intervju",
                    "Godta intervjutidspunktet"
                );
            case InterviewStatusType::ACCEPTED:
                return new ApplicationStatus(
                    ApplicationStatus::INTERVIEW_ACCEPTED,
                    "Intervjutidspunkt godtatt",
                    "Møt opp til intervju. Sted: " . $interview->getRoom() . ". Tid: " . $interview->getScheduled()->format("d. M H:i")
                );
            case InterviewStatusType::CANCELLED:
                return new ApplicationStatus(
                    ApplicationStatus::CANCELLED,
                    "Søknad kansellert",
                    "Ingen videre handling er nødvendig. Du vil ikke bli tatt opp som vektorassistent."
                );
            default: return new ApplicationStatus(
                ApplicationStatus::APPLICATION_NOT_RECEIVED,
                "Ingen søknad mottatt",
                "Send inn søknad om å bli vektorassistent"
            );
        }
    }
}
