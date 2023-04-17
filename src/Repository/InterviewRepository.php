<?php

namespace App\Repository;

use App\Entity\AdmissionPeriod;
use App\Entity\Interview;
use App\Entity\InterviewStatusType;
use App\Entity\Semester;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class InterviewRepository extends EntityRepository
{
    /**
     * @return Interview
     */
    public function findLastScheduledByUserInAdmissionPeriod(User $user, AdmissionPeriod $admissionPeriod)
    {
        $result = $this->createQueryBuilder('interview')
            ->join('interview.application', 'application')
            ->where('interview.interviewer = :user')
            ->setParameter('user', $user)
            ->andWhere('application.admissionPeriod = :admissionPeriod')
            ->setParameter('admissionPeriod', $admissionPeriod)
            ->andWhere('interview.lastScheduleChanged IS NOT NULL')
            ->orderBy('interview.lastScheduleChanged', 'DESC')
            ->getQuery()
            ->getResult();

        return !empty($result) ? $result[0] : null;
    }

    public function findLatestInterviewByUser(User $user)
    {
        $query = $this->getEntityManager()->createQuery('
        SELECT i
        FROM App:Interview i
        WHERE i.user = ?1
        ORDER BY i.conducted ASC
        ')
            ->setParameter(1, $user)
            ->setMaxResults(1);

        return $query->getOneOrNullResult();
    }

    /**
     * @return Interview
     */
    public function findByResponseCode(string $responseCode)
    {
        return $this->createQueryBuilder('interview')
            ->where('interview.responseCode = :responseCode')
            ->setParameter('responseCode', $responseCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Interview[]
     */
    public function findUncompletedInterviewsByInterviewerInCurrentSemester(User $interviewer)
    {
        $semester = $interviewer->getDepartment()->getCurrentAdmissionPeriod();
        if ($semester === null) {
            return [];
        }

        return $this->createQueryBuilder('interview')
            ->join('interview.application', 'application')
            ->join('application.admissionPeriod', 'admissionPeriod')
            ->where('admissionPeriod.semester = :semester')
            ->setParameter('semester', $semester)
            ->andWhere('interview.interviewer = :interviewer OR interview.coInterviewer = :interviewer')
            ->andWhere('interview.interviewed = false')
            ->setParameter('interviewer', $interviewer)
            ->orderBy('interview.scheduled')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[]
     */
    public function findInterviewersInSemester(Semester $semester)
    {
        /**
         * @var $interviews Interview[]
         */
        $interviews = $this->createQueryBuilder('interview')
                    ->join('interview.application', 'application')
                    ->join('application.admissionPeriod', 'admissionPeriod')
                    ->where('admissionPeriod.semester = :semester')
                    ->setParameter('semester', $semester)
                    ->getQuery()
                    ->getResult();
        $interviewers = [];
        foreach ($interviews as $interview) {
            $interviewers[] = $interview->getInterviewer();
            if ($interview->getCoInterviewer()) {
                $interviewers[] = $interview->getCoInterviewer();
            }
        }

        return array_unique($interviewers);
    }

    /**
     * Find interviews which will receive accept-interview notifications.
     * All interviews scheduled to a time after $time and having PENDING
     * interview status apply.
     *
     * @return array
     */
    public function findAcceptInterviewNotificationRecipients(\DateTime $time)
    {
        return $this->createQueryBuilder('i')
            ->select('i')
            ->where('i.scheduled > :time')
            ->andWhere('i.interviewStatus = :status')
            ->setParameter('time', $time)
            ->setParameter('status', InterviewStatusType::PENDING)
            ->getQuery()
            ->getResult();
    }
}
